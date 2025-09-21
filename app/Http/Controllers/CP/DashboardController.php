<?php

namespace App\Http\Controllers\CP;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Product;
use App\Models\SucessStory;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $categoriesCount = 0;//Category::where('restaurant_id', $restaurant->id)->count();
            $activeCategoriesCount = 0;//Category::where('restaurant_id', $restaurant->id)->where('active', true)->count();
            $productsCount = 0;//Product::where('restaurant_id', $restaurant->id)->count();
            $activeProductsCount = 0;//Product::where('restaurant_id', $restaurant->id)->where('active', true)->count();
            $sucessStoriesCount = 0;//SucessStory::where('restaurant_id', $restaurant->id)->count();
            $activeSucessStoriesCount = 0;//SucessStory::where('restaurant_id', $restaurant->id)->where('active', true)->count();
            return view('CP.index', compact('categoriesCount', 'activeCategoriesCount', 'productsCount', 'activeProductsCount', 'sucessStoriesCount', 'activeSucessStoriesCount'));
        } catch (Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return view('CP.index', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getSelect2Details(Request $request)
    {
        try {
            // Determine the model dynamically
            $modelClass = $request->get('model');
            if (! class_exists($modelClass) || ! isset($request->model_id)) {
                throw new Exception(t('Item Not Found'));
            }
            $item = $modelClass::query();
            $item = $item->find($request->model_id);
            // $item = null;
            if (! $item) {
                throw new Exception(t('Item Not Found'));
            }

            return response(['status' => true, 'message' => 'done', 'item' => $item ?: []], 200);
        } catch (Exception $ex) {
            return response()->json(['status' => false, 'message' => $ex->getMessage()], 401);
        }
    }

    public function getSelect2(Request $request)
    {
        $search = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 10;
        // Log initial request data
        Log::info('Select2 request received', ['search' => $search, 'page' => $page, 'model' => $request->get('model')]);

        // Determine the model dynamically
        $modelClass = $request->get('model');
        $parent_ids = $request->get('parent_id');
        $searchBy = $request->get('searchBy', []);

        // Log model determination
        if (! class_exists($modelClass)) {
            Log::warning('Model class does not exist', ['modelClass' => $modelClass]);

            return response()->json(['items' => [], 'total_count' => 0]);
        }
        if (! empty($parent_ids)) {
            Log::info('Filtering by parent id.', ['value' => $parent_ids]);
            $parent_ids = filterArrayForNullValues($parent_ids);
            Log::info('Filtered values:', ['parent_ids' => $parent_ids]);
            if (count($parent_ids) > 0) {
            } else {
                Log::info('No valid values to filter for parent_id.');
            }
        }

        $query = $modelClass::query();
        if ($search) {
            Log::info('Search parameter provided', ['search' => $search]);
            if ($modelClass == Lead::class) {
                Log::info('Building query for Client model');
                $locales = config('app.locales'); // Fetch locales from the config file
                Log::info('Fetched locales for search', ['locales' => $locales]);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                    $q->orWhere('email', 'like', "%$search%");
                    $q->orWhere('phone', 'like', "%$search%");
                });
            } elseif ($modelClass == Apartment::class) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            } elseif ($modelClass == Client::class) {
                Log::info('Building query for Client model');
                $locales = config('app.locales'); // Fetch locales from the config file
                Log::info('Fetched locales for search', ['locales' => $locales]);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone', 'like', "%$search%")
                        ->orWhere('address', 'like', "%$search%")
                        ->orWhere('bank_iban', 'like', "%$search%")
                        ->orWhere('bank_account_number', 'like', "%$search%");
                });
            } else {
                Log::warning('Unsupported model class for search', ['modelClass' => $modelClass]);

                return response()->json(['items' => [], 'total_count' => 0]);
            }
        }

        if (! empty($searchBy)) {
            $query->where($searchBy);
        }
        // Log the query just before execution
        Log::info('Final query before execution', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Execute the query and paginate
        $items = $query->latest('updated_at')->paginate($perPage, ['*'], 'page', $page);
        Log::info('Query executed and paginated', ['total_items' => $items->total()]);

        $results = [
            'items' => $items->map(function ($item) use ($modelClass) {
                $name = 'NA';
                if ($modelClass == Vehicle::class) {
                    $vehicle_type = isset($item->vehicle_type) ? $item->vehicle_type->name : 'NA';
                    $brand = isset($item->brand) ? $item->brand->name : 'NA';
                    $name = $vehicle_type . ' - ' . $brand . ' - ' . $item->plate_number;
                } else {
                    $name = $item->name ?? 'NA';
                }

                return [
                    'id' => $item->id,
                    'name' => $name,
                ];
            }),
            'total_count' => $items->total(),
        ];

        Log::info('Select2 response prepared', ['results' => $results]);

        return response()->json($results);
    }

    public function getSelect2WithoutSearchOrPaginate(Request $request)
    {
        try {
            $modelClass = $request->get('model');
            $searchBy = $request->get('searchBy', []);
            $operator = strtolower($request->get('operator', 'and'));

            Log::info('Starting search with parameters', [
                'model' => $modelClass,
                'searchBy' => $searchBy,
                'operator' => $operator,
            ]);

            if (! class_exists($modelClass)) {
                Log::error('Model class not found', ['model' => $modelClass]);

                return response()->json([]);
            }

            $query = $modelClass::query();

            if (! empty($searchBy)) {
                $query->where(function ($q) use ($searchBy, $operator) {
                    $first = true;
                    foreach ($searchBy as $field => $value) {
                        if (! empty($value)) {
                            if ($first) {
                                $q->where($field, $value);
                                $first = false;
                            } else {
                                $method = $operator === 'or' ? 'orWhere' : 'where';
                                $q->$method($field, $value);
                            }
                        }
                    }
                });
            }

            $results = $query->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'current_local_name' => $item->name,
                ];
            });

            Log::info('Search completed', ['count' => $results->count()]);

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error in select2 search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([]);
        }
    }
}
