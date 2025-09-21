<?php

namespace App\Http\Controllers\CP\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\ProductSizeRequest;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use App\Services\Filters\SizeFilterService;
use App\Services\Restaurant\SizeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ProductSizeController extends Controller
{
    protected $filterService;

    protected $sizeService;

    private $_model;

    private $config;

    public function __construct(
        ProductSize $_model,
        SizeFilterService $filterService,
        SizeService $sizeService
    ) {
        $this->config = config('modules.restaurants.children.products.children.sizes');
        $this->_model = $_model;
        $this->filterService = $filterService;
        $this->sizeService = $sizeService;

        Log::info('............... '.$this->config['controller'].' initialized with '.$this->config['singular_name'].' model ...........');
    }

    public function index(Request $request, Product $product)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('index');

            return view($data['_view_path'].'index', $data);
        }

        if ($request->isMethod('POST')) {
            $items = $this->_model->query()
                ->with(['product', 'size'])
                // ->latest($this->config['table'] . '.updated_at')
                ->where('product_id', $product->id);

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
                ->editColumn('product_name', function ($item) {
                    return optional($item->product)->name ?? 'N/A';
                })
                ->editColumn('size_name', function ($item) {
                    return '<a href="'.route($this->config['full_route_name'].'.edit', ['_model' => $item->id, 'product' => $item->product_id]).'" class="fw-bold text-gray-800 text-hover-primary">'
                        .($item->size->name ?? 'N/A').'</a>';
                })
                ->addColumn('price', function ($item) {
                    return '<span class="badge badge-light-success">$'.number_format($item->price, 2).'</span>';
                })
                ->addColumn('active', function ($item) {
                    return $item->active ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-danger">Inactive</span>';
                })
                ->addColumn('action', function ($item) {
                    try {
                        return view($this->config['view_path'].'.actions', [
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
                ->rawColumns(['product_name', 'price', 'active', 'action', 'size_name'])
                ->make(true);
        }
    }

    public function create(Request $request, Product $product)
    {
        $data = $this->getCommonData('create');
        $data['product'] = $product;
        $createView = view($data['_view_path'].'.modals.addedit', $data)->render();

        return response()->json(['createView' => $createView]);
        // return view($data['_view_path'] . '.modals.addedit', $data);
    }

    public function edit(Request $request, Product $product, ProductSize $_model)
    {
        $data = $this->getCommonData('edit');
        $data['product'] = $product;
        $data['_model'] = $_model;
        $editView = view($data['_view_path'].'.modals.addedit', $data)->render();

        return response()->json(['createView' => $editView]);
        // return view($data['_view_path'] . '.modals.addedit', $data);
    }

    public function addedit(ProductSizeRequest $request)
    {
        Log::info('=== Starting '.$this->config['singular_name'].' Add/Edit Process ===', [
            'request_data' => $request->except(['password', 'token']),
            'user_id' => auth()->id(),
        ]);

        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);

            if ($id) {
                $productSize = $this->_model->find($id);
                $productSize->update($validatedData);
                $result = ['message' => t($this->config['singular_name'].' Updated Successfully!'), 'model' => $productSize];
            } else {
                $productSize = $this->_model->create($validatedData);
                $result = ['message' => t($this->config['singular_name'].' Created Successfully!'), 'model' => $productSize];
            }
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'id' => $result['model']->id,
                    'data' => $result['model'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in '.$this->config['singular_name'].' add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'token']),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete(Request $request, Product $product, ProductSize $_model)
    {
        try {
            DB::beginTransaction();

            $_model->delete();
            DB::commit();

            Log::info($this->config['singular_name'].' deleted successfully', [$this->config['id_field'] => $_model->id]);

            return jsonCRMResponse(true, t($this->config['singular_name'].' Deleted Successfully!'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting '.$this->config['singular_name'], [
                'error' => $e->getMessage(),
            ]);

            return jsonCRMResponse(false, 'An error occurred while deleting the '.$this->config['singular_name'].'. Please try again.', 500);
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
            // Get sizes list for the dropdowns
            $data['sizes_list'] = Size::active()->get();
        }

        return $data;
    }
}
