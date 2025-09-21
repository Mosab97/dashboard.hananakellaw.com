<?php

namespace App\Http\Controllers\CP\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Services\Filters\ProductFilterService;
use App\Services\Restaurant\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    protected $filterService;

    protected $productService;

    private $_model;

    private $config;

    public function __construct(
        Product $_model,
        ProductFilterService $filterService,
        ProductService $productService
    ) {
        $this->config = config('modules.restaurants.children.products');
        $this->_model = $_model;
        $this->filterService = $filterService;
        $this->productService = $productService;

        Log::info('............... ' . $this->config['controller'] . ' initialized with ' . $this->config['singular_name'] . ' model ...........');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('index');

            return view($data['_view_path'] . 'index', $data);
        }

        if ($request->isMethod('POST')) {

            $items = $this->_model->query()
                ->where('restaurant_id', getFirstRestaurant()->id)
                ->with(['category', 'restaurant', 'sizes'])
                ->latest($this->config['table'] . '.updated_at');

            if ($request->input('params')) {
                $this->filterService->applyFilters($items, $request->input('params'));
            }

            return DataTables::eloquent($items)
                ->editColumn('created_at', function ($item) {
                    if ($item->created_at) {
                        return [
                            'display' => $item->created_at->format('Y-m-d'),
                            'timestamp' => $item->created_at->timestamp,
                        ];
                    }
                })
                ->editColumn('name', function ($item) {
                    $imageImg = '';
                    if ($item->image_path) {
                        $imageImg = '<img src="' . $item->image_path . '" class="me-2" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">';
                    }

                    return '<a href="' . route($this->config['full_route_name'] . '.edit', ['_model' => $item->id]) . '"  class="fw-bold text-gray-800 text-hover-primary d-flex align-items-center">
                         ' . $imageImg . ($item->name ?? 'N/A') . '
                    </a>';
                })
                ->addColumn('product_id', function ($item) {
                    return $item->id ?? 'N/A';
                })
                ->addColumn('category_name', function ($item) {
                    return $item->category ? $item->category->name : 'N/A';
                })
                ->addColumn('restaurant_name', function ($item) {
                    return $item->restaurant ? $item->restaurant->name : 'N/A';
                })
                ->addColumn('price', function ($item) {
                    return '<span class="badge badge-light-success">$' . number_format($item->price, 2) . '</span>';
                })
                ->addColumn('status', function ($item) {
                    return $item->getStatusBadge();
                })
                ->addColumn('sizes_count', function ($item) {
                    $count = $item->sizes->count();

                    return '<span class="badge badge-light-info">' . $count . ' sizes</span>';
                })
                ->addColumn('order', function ($item) {
                    return '<span class="badge badge-light-primary">' . ($item->order ?? 0) . '</span>';
                })
                ->addColumn('action', function ($item) {
                    try {
                        return view($this->config['view_path'] . '.actions', [
                            '_model' => $item,
                            'config' => $this->config,
                        ])->render();
                    } catch (\Exception $e) {
                        Log::error('Error in getActionButtons', [
                            'error' => $e->getMessage(),
                            'model_id' => $item->id,
                        ]);
                        throw $e;
                    }
                })
                ->rawColumns(['name', 'price', 'status', 'sizes_count', 'order', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function edit(Request $request, Product $_model)
    {
        $data = $this->getCommonData('edit');
        $data['_model'] = $_model;

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function details(Request $request, Product $_model)
    {
        $data = $this->getCommonData();
        $data['_model'] = $_model;

        $view = view($data['_view_path'] . '.modals.details', $data)->render();

        return response()->json([
            'status' => true,
            'createView' => $view,
        ]);
    }

    public function addedit(ProductRequest $request)
    {
        Log::info('=== Starting ' . $this->config['singular_name'] . ' Add/Edit Process ===', [
            'request_data' => $request->except(['password', 'token']),
            'user_id' => auth()->id(),
        ]);

        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);

            // Handle image upload
            $imagePath = null;
            // Get the file objects once and store them in variables
            if ($request->hasFile('image')) {
                $imagePath = Storage::disk('public')->putFile('products', $request->file('image'));
                // $imagePath = uploadImage($request->file('image'), 'products');
                $validatedData['image'] = $imagePath;
            }
            DB::beginTransaction();

            $isUpdate = ! empty($id);

            if ($isUpdate) {

                $model = Product::findOrFail($id);

                if (request()->has('delete_image')) {
                    if (isset($model->image)) {
                        Storage::disk('public')->delete($model->image);
                    }
                    $validatedData['image'] = null;
                }

                // dd($model);
                $model->update($validatedData);
                $model->image = $imagePath;
                $message = t('Product updated successfully.');
            } else {
                $model = Product::create($validatedData);
                $message = t('Product created successfully.');
            }

            DB::commit();

            return redirect()
                ->route($this->config['full_route_name'] . '.edit', ['_model' => $model->id])
                ->with('status', $message);
        } catch (\Exception $e) {
            Log::error('Error in ' . $this->config['singular_name'] . ' add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'token', 'image']),
            ]);

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(Request $request, Product $_model)
    {
        try {
            DB::beginTransaction();

            // Delete image file if exists
            if ($_model->image && Storage::disk('public')->exists($_model->image)) {
                Storage::disk('public')->delete($_model->image);
            }

            $_model->delete();
            DB::commit();

            Log::info($this->config['singular_name'] . ' deleted successfully', [$this->config['id_field'] => $_model->id]);

            return jsonCRMResponse(true, t($this->config['singular_name'] . ' Deleted Successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting ' . $this->config['singular_name'], [
                'error' => $e->getMessage(),
            ]);

            return jsonCRMResponse(false, 'An error occurred while deleting the ' . $this->config['singular_name'] . '. Please try again.', 500);
        }
    }

    public function export(Request $request)
    {
        // Implementation for export functionality if needed
        $params = $request->all();
        $filterService = $this->filterService;

        // return Excel::download(new ProductExport($params, $filterService), $this->config['plural_key'] . '.xlsx');
    }

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            '_model' => $this->_model,
            'config' => $this->config,
        ];

        // Add data lists needed for forms
        if (in_array($action, ['create', 'edit'])) {
            // Get restaurants and categories list for the dropdowns
            $data['restaurants_list'] = Restaurant::active()->get();
            $data['categories_list'] = Category::active()->where(['restaurant_id' => getFirstRestaurant()->id])->get();
        }

        return $data;
    }
}
