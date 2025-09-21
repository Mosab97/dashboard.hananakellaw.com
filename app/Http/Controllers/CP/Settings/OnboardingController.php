<?php

namespace App\Http\Controllers\CP\Settings;

use App\Enums\BankType;
use App\Enums\PaymentMethod;
use App\Enums\SubscriptionDuration;
use App\Enums\SubscriptionStatus;
use App\Enums\SubscriptionUserType;
use App\Enums\WalletType;
use App\Enums\WeekDays;
use App\Exports\ProgramExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CP\OnboardingRequest;
use App\Models\Member;
use App\Models\Onboarding;
use App\Models\Principal;
use App\Models\SubscriptionPricing;
use App\Services\Filters\OnboardingFilterService;
use App\Traits\HijriDateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OnboardingController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    private $_model;

    private $config;

    public function __construct(Onboarding $_model, OnboardingFilterService $filterService)
    {
        $this->config = config('modules.settings.children.onboardings');

        $this->_model = $_model;
        $this->filterService = $filterService;
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
                ->editColumn('title', function ($item) {
                    return '<a href="'.route($this->config['full_route_name'].'.edit', ['_model' => $item->id]).'"  class="fw-bold text-gray-800 text-hover-primary">
                         '.$item->title.'
                    </a>';
                })
                ->editColumn('description', function ($item) {
                    return mb_substr($item->description, 0, 50).(mb_strlen($item->description) > 50 ? '...' : '');
                })
                ->addColumn('image', function ($item) {
                    return $item->image ?
                        '<img src="'.asset($item->image).'" alt="'.$item->title.'" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">' :
                        '<span class="badge badge-light-danger">No Image</span>';
                })
                ->addColumn('order', function ($item) {
                    return '<span class="badge badge-light-primary">'.$item->order.'</span>';
                })
                ->addColumn('is_active', function ($item) {
                    $color = $item->is_active ? 'success' : 'danger';
                    $status = $item->is_active ? __('Active') : __('Inactive');

                    return '<span class="badge badge-light-'.$color.'">'.$status.'</span>';
                })
                ->addColumn('action', function ($item) {
                    try {
                        return view($this->config['view_path'].'.actions', [
                            '_model' => $item,
                            'config' => $this->config,
                        ])->render();
                    } catch (Exception $e) {
                        Log::error('Error in getActionButtons', [
                            'error' => $e->getMessage(),
                            'model_id' => $item->id,
                        ]);
                        throw $e;
                    }
                })
                ->rawColumns(['title', 'image', 'order', 'is_active', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');
        $createView = view(
            $data['_view_path'].'.addedit',
            $data
        )->render();

        return $createView;
    }

    public function edit(Request $request, Onboarding $_model)
    {
        $data = $this->getCommonData('edit');

        // $data['audits'] = $this->_model->audits()->with('user')->orderByDesc('created_at')->get();
        // $data['attachmentAudits'] = Audit::whereHasMorph('auditable', Attachment::class, function ($query) use ($_model) {
        //     $query->where('attachable_type', get_class($this->_model))
        //         ->where('attachable_id', $_model->id)->withTrashed();
        // })->with('user')->orderByDesc('created_at')->get();
        $data['_model'] = $_model;
        // dd($data);
        $createView = view(
            $data['_view_path'].'.addedit',
            $data
        )->render();

        return $createView;
    }

    public function details(Request $request, Onboarding $_model)
    {
        $data = $this->getCommonData();
        $data['_model'] = $_model;
        // dd($data);
        $view = view(
            $data['_view_path'].'.modals.details',
            $data
        )->render();

        return response()->json([
            'status' => true,
            'createView' => $view,
        ]);
    }

    /**
     * Add or edit an onboarding screen
     *
     * @return \Illuminate\Http\Response
     */
    public function addedit(OnboardingRequest $request)
    {
        Log::info('=== Starting '.$this->config['singular_name'].' Add/Edit Process ===', [
            'request_data' => $request->except(['token']),
            'user_id' => auth()->id(),
        ]);

        try {
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();

            // Extract ID from the request using the configured id_field
            $id = $request->input($this->config['id_field']);
            $isUpdate = ! empty($id);

            // Handle image upload if a new file is provided
            if ($request->hasFile('image')) {
                $validatedData['image'] = uploadImage($request->file('image'), $this->config['upload_path']);
            }

            // Handle image removal if requested
            if ($request->has('image_remove') && $request->input('image_remove') == '1') {
                $validatedData['image'] = null;

                // If updating, also delete the existing image
                if ($isUpdate) {
                    $onboarding = $this->_model->findOrFail($id);
                    if ($onboarding->image) {
                        deleteFile($onboarding->image);
                    }
                }
            }

            if ($isUpdate) {
                // UPDATE FLOW
                $onboarding = $this->_model->findOrFail($id);

                // If removing image and we have an existing one, delete the file
                if (isset($validatedData['image']) && $validatedData['image'] === null && $onboarding->image) {
                    $deleteResult = deleteFile($onboarding->image);
                    Log::info('Delete file result', ['message' => $deleteResult]);
                }

                // Update the record
                $onboarding->update($validatedData);
            } else {
                // CREATE FLOW
                // Create new record
                $onboarding = $this->_model->create($validatedData);
            }

            DB::commit();

            $message = $isUpdate
                ? t($this->config['singular_name'].' has been updated successfully!')
                : t($this->config['singular_name'].' has been added successfully!');

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'id' => $onboarding->id,
                    'data' => $onboarding,
                ]);
            }

            return redirect()
                ->route($this->config['full_route_name'].'.edit', ['_model' => $onboarding->id])
                ->with('status', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in '.$this->config['singular_name'].' add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request_data' => $request->except(['token']),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'errors' => [],
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([])
                ->with('error', $e->getMessage());
        }
    }

    public function delete(Request $request, Onboarding $_model)
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
                $this->config['_id'] => $_model->id,
                'error' => $e->getMessage(),
            ]);

            return jsonCRMResponse(false, 'An error occurred while deleting the '.$this->config['singular_name'].'. Please try again.', 500);
        }
    }

    public function export(Request $request)
    {
        $params = $request->all();
        $filterService = $this->filterService;

        return Excel::download(new ProgramExport($params, $filterService), $this->config['p_lcf'].'.xlsx');
    }

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            '_model' => $this->_model,
            'config' => $this->config,
        ];
        $data['members_list'] = Member::school()->get();
        $data['user_type_list'] = SubscriptionUserType::getAllFromDatabase();
        $data['wallet_type_list'] = WalletType::getAllFromDatabase();
        $data['bank_type_list'] = BankType::getAllFromDatabase();
        $data['duration_list'] = SubscriptionDuration::getAllFromDatabase();
        $data['status_list'] = SubscriptionStatus::getAllFromDatabase();
        $data['payment_method_list'] = PaymentMethod::getAllFromDatabase();
        if (in_array($action, ['index'])) {
            // $data['job_vacancies_list'] = JobVacancy::get();
        }
        if (in_array($action, ['create', 'edit'])) {

            $data['days_list'] = WeekDays::getAllFromDatabase();
            $data['principals'] = Principal::get();
        }

        return $data;
    }

    /**
     * Get pricing for a user type and duration
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPricing(Request $request)
    {
        $userTypeId = $request->input('user_type_id');
        $durationId = $request->input('duration_id');

        if (! $userTypeId || ! $durationId) {
            return response()->json(['success' => false, 'message' => 'Missing required parameters'], 400);
        }

        try {
            // Try to get a pricing record from the database
            $pricing = SubscriptionPricing::where('user_type_id', $userTypeId)
                ->where('duration_id', $durationId)
                ->where('active', true)
                ->first();

            if ($pricing) {
                return response()->json([
                    'success' => true,
                    'price' => $pricing->price,
                    'whatsapp_message_limit' => $pricing->whatsapp_message_limit ?? 100,
                    'user_type_id' => $userTypeId,
                    'duration_id' => $durationId,
                ]);
            }

            // If no pricing found, calculate a default price
            // Get the user type and duration constants
            $userType = \App\Models\Constant::find($userTypeId);
            $duration = \App\Models\Constant::find($durationId);

            if (! $userType || ! $duration) {
                return response()->json(['success' => false, 'message' => 'Invalid user type or duration'], 404);
            }

            // Default prices if no pricing record found
            $defaultPrices = [
                'teacher' => [
                    'monthly' => 50.00,
                    'quarterly' => 135.00,
                    'yearly' => 480.00,
                ],
                'school' => [
                    'monthly' => 150.00,
                    'quarterly' => 405.00,
                    'yearly' => 1440.00,
                ],
            ];

            // Default WhatsApp message limits
            $defaultMessageLimits = [
                'teacher' => [
                    'monthly' => 100,
                    'quarterly' => 300,
                    'yearly' => 1200,
                ],
                'school' => [
                    'monthly' => 300,
                    'quarterly' => 900,
                    'yearly' => 3600,
                ],
            ];

            // Get from default prices
            $userTypeKey = $userType->constant_name;
            $durationKey = $duration->constant_name;

            $price = $defaultPrices[$userTypeKey][$durationKey] ?? 0.00;
            $messageLimit = $defaultMessageLimits[$userTypeKey][$durationKey] ?? 100;

            return response()->json([
                'success' => true,
                'price' => $price,
                'whatsapp_message_limit' => $messageLimit,
                'user_type_id' => $userTypeId,
                'duration_id' => $durationId,
                'note' => 'Using default pricing',
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting pricing: '.$e->getMessage(), [
                'user_type_id' => $userTypeId,
                'duration_id' => $durationId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['success' => false, 'message' => 'Error getting pricing information'], 500);
        }
    }
}
