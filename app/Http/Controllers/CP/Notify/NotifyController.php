<?php

namespace App\Http\Controllers\CP\Notify;

use App\Enums\NotifyType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CP\NotificationRequest;
use App\Models\Guardian;
use App\Models\Member;
use App\Models\Notify;
use App\Models\Student;
use App\Models\TeacherProfile;
use App\Notifications\API\AppNotification;
use App\Services\Filters\NotifyFilterService;
use App\Traits\HijriDateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class NotifyController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    private $_model;

    private $config;

    public function __construct(Notify $_model, NotifyFilterService $filterService)
    {
        $this->config = config('modules.notifications.children.notifies');

        $this->_model = $_model;
        $this->filterService = $filterService;
        Log::info('............... '.$this->config['controller'].' initialized with '.$this->config['singular_name'].' model ...........');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = $this->getCommonData('create');

            return view($data['_view_path'].'index', $data);
        }
        if ($request->isMethod('POST')) {
            $items = $this->_model->query()
                ->with([
                    'notifyTag',
                    'creator',
                ])
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
                    // href="' . route($this->config['full_route_name'] . '.edit', ['_model' => $item->id]) . '"
                    return '<a
                    href="#"
                      class="fw-bold text-gray-800 text-hover-primary">
                         '.($item->getTranslation('title', app()->getLocale()) ?? 'N/A').'
                    </a>';
                })
                ->addColumn('notify_tag', function ($item) {
                    return '<span class="badge badge-light-primary">'.($item->notifyTag->name ?? 'N/A').'</span>';
                })
                ->addColumn('content', function ($item) {
                    $content = $item->getTranslation('content', app()->getLocale()) ?? '';

                    return mb_strlen($content) > 50 ? mb_substr($content, 0, 50).'...' : $content;
                })
                ->addColumn('creator', function ($item) {
                    $creatorName = 'N/A';
                    if ($item->creator) {
                        $creatorName = $item->creator->name ?? ($item->creator->full_name ?? 'N/A');
                    }

                    return '<span class="fw-bold">'.$creatorName.'</span>';
                })
                ->addColumn('recipients', function ($item) {
                    $count = is_array($item->notifiable_ids) ? count($item->notifiable_ids) : 0;

                    return '<span class="badge badge-light-info">'.$count.' '.__('Recipients').'</span>';
                })
                // ->addColumn('action', function ($item) {
                //     try {
                //         return view($this->config['view_path'] . '.actions', [
                //             '_model' => $item,
                //             'config' => $this->config,
                //         ])->render();
                //     } catch (\Exception $e) {
                //         Log::error('Error in getActionButtons', [
                //             'error' => $e->getMessage(),
                //             'model_id' => $item->id
                //         ]);
                //         throw $e;
                //     }
                // })
                ->rawColumns(['title', 'notify_tag', 'creator', 'recipients'/* , 'action' */])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');
        $createView = view(
            $data['_view_path'].'.modals.addedit_modal',
            $data
        )->render();

        return response()->json(['createView' => $createView]);
    }

    /**
     * Get recipients based on the tag type
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipients(Request $request)
    {
        try {
            $tagId = $request->input('tag_id');

            if (! $tagId) {
                return jsonCRMResponse(false, t('Tag ID is required'), 400);
            }

            // Get the notification tag
            $tag = NotifyType::getAllFromDatabase()->find($tagId);
            if (! $tag) {
                return jsonCRMResponse(false, t('Tag not found'), 404);
            }

            $tagType = $tag->constant_name;
            $recipients = [];

            if ($tagType === NotifyType::TEACHERS->getFromDatabase()->constant_name) {
                $recipients = Member::select('id', 'name')
                    // ->where('active', 1)
                    ->teacher()
                    ->orderBy('name')
                    ->get();

                return response()->json([
                    'success' => true,
                    'tag_type' => 'teacher',
                    'recipients' => $recipients,
                    'hide_recipients' => false,
                ]);
            } elseif ($tagType === NotifyType::STUDENTS->getFromDatabase()->constant_name) {
                $recipients = Student::select('id', 'name')
                    // ->where('active', 1)
                    ->orderBy('name')
                    ->get();

                return response()->json([
                    'success' => true,
                    'tag_type' => 'student',
                    'recipients' => $recipients,
                    'hide_recipients' => false,
                ]);
            } else {
                // For other tag types, return empty recipients array
                // and indicate if the field should be hidden
                return response()->json([
                    'success' => true,
                    'tag_type' => 'other',
                    'recipients' => [],
                    'hide_recipients' => true, // Hide the field for other tag types
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching recipients: '.$e->getMessage(), [
                'exception' => $e,
                'tag_id' => $request->input('tag_id'),
            ]);

            return jsonCRMResponse(false, t('Failed to fetch recipients'), 500);
        }
    }

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            '_model' => $this->_model,
            'config' => $this->config,
        ];
        $data['tag_list'] = NotifyType::getAllFromDatabase();
        if (in_array($action, ['index'])) {
        }
        if (in_array($action, ['create', 'edit'])) {
        }

        return $data;
    }

    /**
     * Store and send a new notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(NotificationRequest $request)
    {

        Log::info('Starting notification sending process', [
            'notify_type_id' => $request->tag_id,
            'request_data' => $request->all(),
        ]);

        try {
            // Get notification tag/type
            $notifyType = NotifyType::getAllFromDatabase()->find($request->tag_id);

            // Prepare notification data
            $data = $request->all();

            $data['sender_id'] = auth()->id();
            $data['tag_name'] = $notifyType->getTranslations('name');
            $data['tag_id'] = $notifyType->id;
            $data['creator_id'] = auth()->id();
            $data['creator_type'] = get_class(auth()->user());

            // Process recipient IDs from the form
            $recipientIds = $request->notifiable_ids ?? [];
            $data['notifiable_ids'] = $recipientIds;

            // Create notification record
            $notify = Notify::create($data);

            // Get recipients based on tag type
            $recipients = $this->getRecipientsByType($notifyType, $recipientIds);

            if (empty($recipients)) {
                return jsonCRMResponse(false, t('No recipients found'), 404);
            }

            // Send notifications
            Notification::send($recipients, new AppNotification($data));

            Log::info('Notification process completed', [
                'notify_id' => $notify->id,
                'recipients_count' => count($recipients),
            ]);

            return jsonCRMResponse(true, t('Notification sent successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Error sending notification', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return jsonCRMResponse(false, t('Failed to send notification: ').$e->getMessage(), 500);
        }
    }

    /**
     * Get recipients based on notification type
     *
     * @param  \App\Enums\NotifyType  $notifyType
     * @return array
     */
    private function getRecipientsByType($notifyType, array $recipientIds)
    {
        $recipients = [];
        switch ($notifyType->constant_name) {
            case NotifyType::TEACHERS->value:
                // Get specific teachers by IDs
                $recipients = $this->getTeacherRecipients($recipientIds);
                break;

            case NotifyType::STUDENTS->value:
                // Get specific students' guardians by IDs
                $studentsResult = $this->getStudentRecipients($recipientIds, true);
                $recipients = $studentsResult['recipients'];
                break;

            case NotifyType::ALL_TEACHERS->value:
                // Get all teachers
                $recipients = $this->getAllTeachers();
                break;

            case NotifyType::ALL_STUDENTS->value:
                // Get all students' guardians
                $recipients = $this->getAllStudentGuardians();
                break;

            case NotifyType::BOTH->value:
                // Get both teachers and student guardians
                $teachers = $this->getAllTeachers();
                $guardians = $this->getAllStudentGuardians();
                $recipients = array_merge($teachers, $guardians);
                break;
        }

        return $recipients;
    }

    /**
     * Get all teachers
     *
     * @return array
     */
    private function getAllTeachers()
    {
        return TeacherProfile::with('member')
            ->get()
            ->pluck('member')
            ->filter()
            ->all();
    }

    /**
     * Get all student guardians
     *
     * @return array
     */
    private function getAllStudentGuardians()
    {
        $students = Student::all();
        $guardianIds = $students->pluck('guardian_id')->filter()->unique()->values();

        if ($guardianIds->isEmpty()) {
            return [];
        }

        return Guardian::whereIn('id', $guardianIds)->get()->all();
    }

    /**
     * Get teacher recipients
     *
     * @return array
     */
    private function getTeacherRecipients(array $teacherIds)
    {
        $data = Member::teacher()->whereIn('id', $teacherIds)->get()->all();

        //  $data = TeacherProfile::whereIn('id', $teacherIds)
        //     ->with('member')
        //     ->get()
        //     ->pluck('member')
        //     ->all();
        // dd($data,$teacherIds);
        return $data;
    }

    /**
     * Get student recipients
     *
     * @return array
     */
    private function getStudentRecipients(array $studentIds, bool $notifyGuardians)
    {
        // Get all targeted students
        $students = Student::query()
            ->whereIn('id', $studentIds)
            ->get();

        if ($students->isEmpty()) {
            return [
                'recipients' => [],
                'stats' => [],
            ];
        }

        // Initialize empty recipients array
        $recipients = [];

        // Get guardians for notifications
        if ($notifyGuardians) {
            // Get students with valid guardian IDs
            $guardianIds = $students->pluck('guardian_id')->filter()->unique()->values();

            if ($guardianIds->isNotEmpty()) {
                $guardians = Guardian::whereIn('id', $guardianIds)->get();
                $recipients = $guardians->all();
            }
        }

        return [
            'recipients' => $recipients,
            'stats' => [
                'recipients_count' => count($recipients),
            ],
        ];
    }
}
