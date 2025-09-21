<?php

namespace App\Http\Controllers\CP\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\CategoryRequest;
use App\Models\Category;
use App\Models\Restaurant;
use App\Services\Filters\CategoryFilterService;
use App\Services\Restaurant\CategoryService;
use App\Traits\HijriDateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    protected $categoryService;

    private $_model;

    private $config;

    public function __construct(
        Category $_model,
        CategoryFilterService $filterService,
        CategoryService $categoryService
    ) {
        $this->config = config('modules.restaurants.children.categories');
        $this->_model = $_model;
        $this->filterService = $filterService;
        $this->categoryService = $categoryService;

        Log::info('............... ' . $this->config['controller'] . ' initialized with ' . $this->config['singular_name'] . ' model ...........');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('index');

            return view($data['_view_path'] . 'index', $data);
        }

        if ($request->isMethod('POST')) {
            $firstRestaurant = getFirstRestaurant();
            $items = $firstRestaurant->categories()
                ->with(['restaurant'])
                ->latest($this->config['table'] . '.updated_at');

            if ($request->input('params')) {
                $this->filterService->applyFilters($items, $request->input('params'));
            }

            return DataTables::eloquent($items)
                ->editColumn('created_at', function ($item) {
                    if ($item->created_at) {
                        return [
                            'display' => $this->convertGregorianToHijri($item->created_at->format('Y-m-d')),
                            'timestamp' => $item->created_at->timestamp,
                        ];
                    }
                })
                ->editColumn('name', function ($item) {
                    $imageImg = '';
                    if ($item->image) {
                        $imageImg = '<img src="' . $item->image_path . '" class="me-2" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">';
                    }

                    return '<a href="' . route($this->config['full_route_name'] . '.edit', ['_model' => $item->id]) . '"  class="fw-bold text-gray-800 text-hover-primary d-flex align-items-center">
                         ' . $imageImg . ($item->name ?? 'N/A') . '
                    </a>';
                })
                ->addColumn('id', function ($item) {
                    return $item->id ?? 'N/A';
                })
                ->addColumn('status', function ($item) {
                    return $item->getStatusBadge();
                })
                ->addColumn('order', function ($item) {
                    return '<span class="badge badge-light-primary">' . ($item->order ?? 0) . '</span>';
                })
                ->addColumn('icon', function ($item) {
                    if ($item->icon) {
                        return '<img src="' . $item->getIconUrl() . '" style="width: 20px; height: 20px; object-fit: cover;">';
                    }

                    return '<span class="text-muted">No Icon</span>';
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
                ->rawColumns(['name', 'status', 'order', 'icon', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function edit(Request $request, Category $_model)
    {
        $data = $this->getCommonData('edit');
        $data['_model'] = $_model;

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function details(Request $request, Category $_model)
    {
        $data = $this->getCommonData();
        $data['_model'] = $_model;

        $view = view($data['_view_path'] . '.modals.details', $data)->render();

        return response()->json([
            'status' => true,
            'createView' => $view,
        ]);
    }

    public function addedit(CategoryRequest $request)
    {


        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);
            DB::beginTransaction();

            $isUpdate = ! empty($id);

            // Get the file objects once and store them in variables
            if ($request->hasFile('image')) {
                $imagePath = uploadImage($request->file('image'), 'categories-images');
                $validatedData['image'] = $imagePath;
            }

            if ($request->hasFile('icon')) {
                $iconPath = uploadImage($request->file('icon'), 'categories-icons');
                $validatedData['icon'] = $iconPath;
            }


            if ($isUpdate) {
                $category = Category::findOrFail($id);
                if ($request->has('delete_image')) {
                    $validatedData['image'] = null;
                }
                if ($request->has('delete_icon')) {
                    $validatedData['icon'] = null;
                }
                $category->update($validatedData);
                $message = t('Category updated successfully.');
            } else {
                $category = Category::create($validatedData);
                $message = t('Category created successfully.');
            }
            DB::commit();

            return redirect()
                ->route($this->config['full_route_name'] . '.edit', ['_model' => $category->id])
                ->with('status', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in ' . $this->config['singular_name'] . ' add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['token', 'image', 'icon']),
            ]);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(Request $request, Category $_model)
    {
        try {
            DB::beginTransaction();

            // Delete image file if exists
            if ($_model->image && Storage::disk('public')->exists($_model->image)) {
                Storage::disk('public')->delete($_model->image);
            }

            // Delete icon file if exists
            if ($_model->icon && Storage::disk('public')->exists($_model->icon)) {
                Storage::disk('public')->delete($_model->icon);
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

        // return Excel::download(new CategoryExport($params, $filterService), $this->config['plural_key'] . '.xlsx');
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
            // Get restaurants list for the dropdown
            $data['restaurants_list'] = Restaurant::active()->get();
        }

        return $data;
    }
}
