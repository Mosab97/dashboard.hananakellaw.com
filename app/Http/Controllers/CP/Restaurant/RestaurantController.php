<?php

namespace App\Http\Controllers\CP\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\RestaurantRequest;
use App\Models\Restaurant;
use App\Services\Filters\RestaurantFilterService;
use App\Services\Restaurant\RestaurantService;
use App\Traits\HijriDateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class RestaurantController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    protected $restaurantService;

    private $_model;

    private $config;

    public function __construct(
        Restaurant $_model,
        RestaurantFilterService $filterService,
        RestaurantService $restaurantService
    ) {
        $this->config = config('modules.restaurants');
        $this->_model = $_model;
        $this->filterService = $filterService;
        $this->restaurantService = $restaurantService;

        Log::info('............... '.$this->config['controller'].' initialized with '.$this->config['singular_name'].' model ...........');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('index');

            return view($data['_view_path'].'index', $data);
        }

        if ($request->isMethod('POST')) {
            $items = $this->_model->query()
                ->latest($this->config['table'].'.updated_at');

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
                    $logoImg = '';
                    if ($item->logo) {
                        $logoImg = '<img src="'.$item->getLogoUrl().'" class="me-2" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">';
                    }

                    return '<a href="'.route($this->config['full_route_name'].'.edit', ['_model' => $item->id]).'"  class="fw-bold text-gray-800 text-hover-primary d-flex align-items-center">
                         '.$logoImg.($item->getFormattedName() ?? 'N/A').'
                    </a>';
                })
                ->addColumn('restaurant_id', function ($item) {
                    return $item->id ?? 'N/A';
                })
                ->addColumn('slug', function ($item) {
                    return '<span class="text-muted">'.($item->slug ?? 'N/A').'</span>';
                })
                ->addColumn('contact_info', function ($item) {
                    $html = '';
                    if ($item->phone) {
                        $html .= '<div><i class="fas fa-phone text-primary me-1"></i> '.$item->phone.'</div>';
                    }
                    if ($item->email) {
                        $html .= '<div><i class="fas fa-envelope text-success me-1"></i> '.$item->email.'</div>';
                    }
                    if ($item->website) {
                        $html .= '<div><i class="fas fa-globe text-info me-1"></i> <a href="'.$item->getFormattedWebsite().'" target="_blank">'.$item->website.'</a></div>';
                    }

                    return $html ?: '<span class="text-muted">N/A</span>';
                })
                ->addColumn('services', function ($item) {
                    return $item->getServicesBadges();
                })
                ->addColumn('status', function ($item) {
                    $statusBadge = $item->getStatusBadge();
                    $openingBadge = $item->getOpeningStatusBadge();

                    return $statusBadge.' '.$openingBadge;
                })
                ->addColumn('address', function ($item) {
                    return $item->address ? '<span class="text-wrap" style="max-width: 200px;">'.$item->address.'</span>' : '<span class="text-muted">N/A</span>';
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
                ->rawColumns(['name', 'slug', 'contact_info', 'services', 'status', 'address', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');

        return view($data['_view_path'].'.addedit', $data);
    }

    public function edit(Request $request, Restaurant $_model)
    {
        $data = $this->getCommonData('edit');
        $data['_model'] = $_model;

        return view($data['_view_path'].'.addedit', $data);
    }

    public function details(Request $request, Restaurant $_model)
    {
        $data = $this->getCommonData();
        $data['_model'] = $_model;

        $view = view($data['_view_path'].'.modals.details', $data)->render();

        return response()->json([
            'status' => true,
            'createView' => $view,
        ]);
    }

    public function addedit(RestaurantRequest $request)
    {
        Log::info('=== Starting '.$this->config['singular_name'].' Add/Edit Process ===', [
            'request_data' => $request->except(['password', 'token']),
            'user_id' => auth()->id(),
        ]);

        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);

            // Handle logo upload
            $logoFile = $request->hasFile('logo') ? $request->file('logo') : null;

            $result = $this->restaurantService->handleAddEdit($validatedData, $id, $logoFile);

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'id' => $result['model']->id,
                    'data' => $result['model'],
                ]);
            }

            return redirect()
                ->route($this->config['full_route_name'].'.edit', ['_model' => $result['model']->id])
                ->with('status', $result['message']);

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

    public function delete(Request $request, Restaurant $_model)
    {
        try {
            DB::beginTransaction();

            // Delete logo file if exists
            if ($_model->logo && Storage::disk('public')->exists($_model->logo)) {
                Storage::disk('public')->delete($_model->logo);
            }

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

        // return Excel::download(new RestaurantExport($params, $filterService), $this->config['plural_key'] . '.xlsx');
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
            // Add any related data lists here if needed
            $data['days_of_week'] = [
                'monday' => t('Monday'),
                'tuesday' => t('Tuesday'),
                'wednesday' => t('Wednesday'),
                'thursday' => t('Thursday'),
                'friday' => t('Friday'),
                'saturday' => t('Saturday'),
                'sunday' => t('Sunday'),
            ];
        }

        return $data;
    }
}
