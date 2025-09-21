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
use App\Http\Requests\CP\UltraMsgInstanceRequest;
use App\Models\Member;
use App\Models\Principal;
use App\Models\SubscriptionPricing;
use App\Models\UltraMsgInstance;
use App\Services\Filters\UltraMsgFilterService;
use App\Traits\HijriDateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UltraMsgInstanceController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    private $_model;

    private $config;

    public function __construct(UltraMsgInstance $_model, UltraMsgFilterService $filterService)
    {
        $this->config = config('modules.settings.children.ultramsg');

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
                ->editColumn('name', function ($item) {
                    return '<a href="'.route($this->config['full_route_name'].'.edit', ['_model' => $item->id]).'"  class="fw-bold text-gray-800 text-hover-primary">
                         '.$item->name.'
                    </a>';
                })
                ->editColumn('description', function ($item) {
                    return mb_substr($item->description, 0, 50).(mb_strlen($item->description) > 50 ? '...' : '');
                })
                ->addColumn('token', function ($item) {
                    return '<span class="badge badge-light-primary">'.mb_substr($item->token, 0, 15).'...'.'</span>';
                })
                ->addColumn('instance_id', function ($item) {
                    return '<span class="badge badge-light-info">'.$item->instance_id.'</span>';
                })
                ->addColumn('active', function ($item) {
                    $color = $item->active ? 'success' : 'danger';
                    $status = $item->active ? __('Active') : __('Inactive');

                    return '<span class="badge badge-light-'.$color.'">'.$status.'</span>';
                })
                ->addColumn('priority', function ($item) {
                    return '<span class="badge badge-light-warning">'.$item->priority.'</span>';
                })
                ->addColumn('last_activity', function ($item) {
                    $lastActivity = $item->last_success && (! $item->last_error || $item->last_success > $item->last_error)
                        ? $item->last_success
                        : $item->last_error;

                    if (! $lastActivity) {
                        return '<span class="badge badge-light-secondary">No activity</span>';
                    }

                    $type = $item->last_success && (! $item->last_error || $item->last_success > $item->last_error)
                        ? 'success'
                        : 'error';

                    $color = $type === 'success' ? 'success' : 'danger';
                    $label = $type === 'success' ? 'Success' : 'Error';

                    return '<span class="badge badge-light-'.$color.'">'.$label.': '.$lastActivity->diffForHumans().'</span>';
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
                ->rawColumns(['name', 'token', 'instance_id', 'active', 'priority', 'last_activity', 'action'])
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

    public function edit(Request $request, UltraMsgInstance $_model)
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

    public function details(Request $request, UltraMsgInstance $_model)
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
     * Add or edit an UltraMsg instance
     *
     * @return \Illuminate\Http\Response
     */
    public function addedit(UltraMsgInstanceRequest $request)
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

            if ($isUpdate) {
                // UPDATE FLOW
                $instance = $this->_model->findOrFail($id);

                // Update the record
                $instance->update($validatedData);

                // Log success
                Log::info('UltraMsg instance updated successfully', [
                    'instance_id' => $instance->id,
                    'name' => $instance->name,
                ]);
            } else {
                // CREATE FLOW
                // Create new record
                $instance = $this->_model->create($validatedData);

                // Log success
                Log::info('UltraMsg instance created successfully', [
                    'instance_id' => $instance->id,
                    'name' => $instance->name,
                ]);
            }

            DB::commit();
            // $this->testConnection($instance->id);
            $message = $isUpdate
                ? t($this->config['singular_name'].' has been updated successfully!')
                : t($this->config['singular_name'].' has been added successfully!');

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'id' => $instance->id,
                    'data' => $instance,
                ]);
            }

            return redirect()
                ->route($this->config['full_route_name'].'.edit', ['_model' => $instance->id])
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

    /**
     * Test the UltraMsg instance connection
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection($id)
    {
        try {
            $instance = $this->_model->findOrFail($id);

            // Initialize UltraMsg service with instance credentials
            // This is a placeholder - you'll need to implement the actual test method
            $result = $this->testUltraMsgConnection($instance);

            if ($result['success']) {
                // Update last success timestamp
                $instance->markSuccess();

                return response()->json([
                    'status' => true,
                    'message' => t('Connection successful!'),
                    'data' => $result['data'] ?? null,
                ]);
            } else {
                // Record error
                $instance->recordError($result['message'] ?? t('Unknown error'));

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? t('Connection failed.'),
                    'errors' => [],
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error testing UltraMsg connection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'instance_id' => $id,
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Helper method to test UltraMsg connection
     *
     * @param  UltraMsgInstance  $instance
     * @return array
     */
    private function testUltraMsgConnection($instance)
    {
        Log::info('Testing UltraMsg connection', [
            'instance_id' => $instance->instance_id,
            'token_length' => strlen($instance->token),
        ]);

        try {
            // Endpoint to check instance status - include token as query parameter
            $url = "https://api.ultramsg.com/{$instance->instance_id}/instance/status?token={$instance->token}";

            Log::debug('Making request to UltraMsg API', [
                'url' => "https://api.ultramsg.com/{$instance->instance_id}/instance/status (token hidden)",
                'method' => 'GET',
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'content-type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            Log::debug('UltraMsg API response received', [
                'status_code' => $statusCode,
                'has_error' => ! empty($err),
                'response_length' => strlen($response),
            ]);

            if ($err) {
                Log::error('cURL Error during connection test', [
                    'instance_id' => $instance->instance_id,
                    'error' => $err,
                ]);

                return [
                    'success' => false,
                    'message' => t('Connection error: ').$err,
                ];
            }

            $responseData = json_decode($response, true);

            Log::debug('UltraMsg API response decoded', [
                'instance_id' => $instance->instance_id,
                'status_code' => $statusCode,
                'response_data' => $responseData,
            ]);

            // Check if the response contains expected data for a valid instance
            if ($statusCode == 200 && isset($responseData['status']) && ! isset($responseData['error'])) {
                // Extract a simple status string if possible
                $statusString = 'connected';
                if (isset($responseData['status']['accountStatus']['status'])) {
                    $statusString = $responseData['status']['accountStatus']['status'];
                }

                Log::info('UltraMsg connection test successful', [
                    'instance_id' => $instance->instance_id,
                    'status_string' => $statusString,
                    // Use json_encode for the complete status to avoid array to string conversion
                    'status_details' => json_encode($responseData['status']),
                ]);

                return [
                    'success' => true,
                    'data' => $responseData,
                    'status' => $statusString,
                ];
            } else {
                $errorMessage = $responseData['error'] ?? $responseData['message'] ?? 'Unknown error';

                Log::error('UltraMsg connection test failed', [
                    'instance_id' => $instance->instance_id,
                    'status_code' => $statusCode,
                    'error' => $errorMessage,
                    'response_data' => json_encode($responseData),
                ]);

                return [
                    'success' => false,
                    'message' => t('Connection failed. Error: ').$errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception during UltraMsg connection test', [
                'instance_id' => $instance->instance_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Activate or deactivate an instance
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive($id, Request $request)
    {
        try {
            $instance = $this->_model->findOrFail($id);
            $active = $request->input('active', ! $instance->active);

            $instance->active = (bool) $active;
            $instance->save();

            return response()->json([
                'status' => true,
                'message' => $instance->active ?
                    t('Instance has been activated successfully!') :
                    t('Instance has been deactivated successfully!'),
                'active' => $instance->active,
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling UltraMsg instance status', [
                'error' => $e->getMessage(),
                'instance_id' => $id,
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    public function delete(Request $request, UltraMsgInstance $_model)
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
