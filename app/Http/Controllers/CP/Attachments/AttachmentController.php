<?php

namespace App\Http\Controllers\CP\Attachments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\AttachmentRequest;
use App\Models\Attachment;
use App\Models\TeacherProfile;
use App\Services\Filters\AttachmentFilterService;
use App\Traits\HijriDateTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AttachmentController extends Controller
{
    use HijriDateTrait;

    protected $filterService;

    private $_model;

    private $config;

    public function __construct(Attachment $_model, AttachmentFilterService $filterService)
    {
        $this->config = config('modules.attachments');

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
                ->with([
                    'attachable',
                    'attachment_type',
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
                ->addColumn('file_name', function ($item) {
                    return '<a href="'.route($this->config['full_route_name'].'.edit', [
                        '_model' => $item->id,
                    ]).'"  class="fw-bold text-gray-800 text-hover-primary">
                     '.$item->file_name.'
                </a>';
                })

                ->addColumn('file_icon', function ($item) {
                    $iconClass = $this->getFileIconClass($item->file_extension);
                    $fileUrl = asset($item->file_path);

                    return '<div class="d-flex align-items-center">
                        <a href="'.$fileUrl.'" target="_blank" title="'.t('View File').'">
                            <i class="'.$iconClass.' fs-2x me-2 cursor-pointer"></i>
                        </a>
                    </div>';
                })

                ->addColumn('file_details', function ($item) {
                    $output = '';
                    if ($item->file_type) {
                        $output .= '<div><strong>'.t('Type').':</strong> '.$item->file_type.'</div>';
                    }
                    if ($item->file_size) {
                        $output .= '<div><strong>'.t('Size').':</strong> '.formatFileSize($item->file_size).'</div>';
                    }
                    if ($item->file_extension) {
                        $output .= '<div><strong>'.t('Extension').':</strong> '.strtoupper($item->file_extension).'</div>';
                    }

                    return $output ?: '<span class="text-muted">N/A</span>';
                })
                ->addColumn('attachment_type', function ($item) {
                    if (! $item->attachment_type) {
                        return '<span class="text-muted">N/A</span>';
                    }

                    return '<span class="badge badge-light-primary">'.$item->attachment_type->name.'</span>';
                })
                ->addColumn('source', function ($item) {
                    if (! $item->source) {
                        return '<span class="text-muted">N/A</span>';
                    }

                    return '<span>'.$item->source.'</span>';
                })
                ->addColumn('related_to', function ($item) {
                    if (! $item->attachable_type || ! $item->attachable_id) {
                        return '<span class="text-muted">N/A</span>';
                    }

                    $modelName = class_basename($item->attachable_type);

                    if ($item->attachable) {
                        $name = method_exists($item->attachable, 'getName')
                            ? $item->attachable->getName()
                            : (property_exists($item->attachable, 'name')
                                ? $item->attachable->name
                                : 'ID: '.$item->attachable_id);

                        return '<span><strong>'.$modelName.':</strong> '.$name.'</span>';
                    }

                    return '<span>'.$modelName.' (ID: '.$item->attachable_id.')</span>';
                })
                ->addColumn('downloads', function ($item) {
                    // If you track downloads, you can display the count here
                    return '<span class="badge badge-light-info">0</span>';
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
                ->rawColumns(['file_name', 'file_icon', 'file_details', 'attachment_type', 'source', 'related_to', 'downloads', 'action'])
                ->make(true);
        }
    }

    /**
     * Get file icon class based on extension
     *
     * @param  string  $extension
     * @return string
     */
    private function getFileIconClass($extension)
    {
        $extension = strtolower($extension);

        $iconMap = [
            'pdf' => 'fa fa-file-pdf text-danger',
            'doc' => 'fa fa-file-word text-primary',
            'docx' => 'fa fa-file-word text-primary',
            'xls' => 'fa fa-file-excel text-success',
            'xlsx' => 'fa fa-file-excel text-success',
            'ppt' => 'fa fa-file-powerpoint text-warning',
            'pptx' => 'fa fa-file-powerpoint text-warning',
            'jpg' => 'fa fa-file-image text-info',
            'jpeg' => 'fa fa-file-image text-info',
            'png' => 'fa fa-file-image text-info',
            'gif' => 'fa fa-file-image text-info',
            'zip' => 'fa fa-file-archive text-secondary',
            'rar' => 'fa fa-file-archive text-secondary',
            'txt' => 'fa fa-file-alt text-dark',
            'mp3' => 'fa fa-file-audio text-primary',
            'mp4' => 'fa fa-file-video text-danger',
            'mov' => 'fa fa-file-video text-danger',
        ];

        return $iconMap[$extension] ?? 'fa fa-file text-muted';
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

    public function edit(Request $request, Attachment $_model)
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

    /**
     * Add or edit an attachment record
     *
     * @return \Illuminate\Http\Response
     */
    public function addedit(AttachmentRequest $request)
    {
        Log::info('=== Starting '.$this->config['singular_name'].' Add/Edit Process ===', [
            'request_data' => $request->except(['token', 'file']),
            'user_id' => auth()->id(),
        ]);

        try {
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();

            // Extract ID from the request using the configured id_field
            $id = $request->input($this->config['id_field']);
            $isUpdate = ! empty($id);

            // // Set default values if not provided
            // $validatedData['file_type'] = $validatedData['file_type'] ?? null;
            // $validatedData['file_size'] = $validatedData['file_size'] ?? null;
            // $validatedData['file_extension'] = $validatedData['file_extension'] ?? null;

            if ($isUpdate) {
                // UPDATE FLOW
                $attachment = $this->_model->findOrFail($id);

                // Check if file needs to be removed
                if ($request->has('file_remove') && $request->input('file_remove') == 1) {
                    // Store old file info for deletion
                    $oldPath = $attachment->file_path;
                    $oldName = $attachment->file_hash;

                    // Delete the existing file
                    if ($oldPath) {
                        Log::info('Attempting to delete file (remove flag)', [
                            'old_path' => $oldPath,
                            'old_name' => $oldName,
                        ]);
                        $deleteResult = deleteFile($oldPath, $oldName);
                        Log::info('Delete file result', ['message' => $deleteResult]);
                    }

                    // Clear file-related fields
                    $validatedData['file_path'] = null;
                    $validatedData['file_hash'] = null;
                    $validatedData['file_type'] = null;
                    $validatedData['file_size'] = null;
                    $validatedData['file_extension'] = null;
                }

                // Handle file upload if present
                if ($request->hasFile('file') && $request->file('file')->isValid()) {
                    // Store old file info for potential deletion
                    $oldPath = $attachment->file_path;
                    $oldName = $attachment->file_hash;

                    // Capture file information BEFORE moving the file
                    $file = $request->file('file');
                    $originalFilename = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();
                    $fileExtension = $file->getClientOriginalExtension();

                    // Store the file
                    $path = uploadImage($file, 'attachments');
                    $fileName = basename($path);

                    // Set all file related data
                    $validatedData['file_path'] = $path;
                    $validatedData['file_hash'] = $fileName;
                    $validatedData['file_name'] = $validatedData['file_name'] ?? $originalFilename;
                    $validatedData['file_type'] = $mimeType;
                    $validatedData['file_size'] = $fileSize;
                    $validatedData['file_extension'] = $fileExtension;

                    Log::info('New file uploaded', [
                        'new_path' => $path,
                        'new_name' => $originalFilename,
                        'file_hash' => $fileName,
                        'file_type' => $mimeType,
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                    ]);

                    // Delete old file if different from the new one
                    if ($oldPath && $oldPath !== $path) {
                        Log::info('Attempting to delete old file', [
                            'old_path' => $oldPath,
                            'old_name' => $oldName,
                        ]);
                        $deleteResult = deleteFile($oldPath, $oldName);
                        Log::info('Delete file result', ['message' => $deleteResult]);
                    }
                }

                // Update attachment record
                $attachment->update($validatedData);
            } else {
                // CREATE FLOW
                // Handle file upload
                if ($request->hasFile('file') && $request->file('file')->isValid()) {
                    // Capture file information BEFORE moving the file
                    $file = $request->file('file');
                    $originalFilename = $file->getClientOriginalName();
                    $mimeType = $file->getMimeType();
                    $fileSize = $file->getSize();
                    $fileExtension = $file->getClientOriginalExtension();

                    // Store the file
                    $path = uploadImage($file, 'attachments');
                    $fileName = basename($path);

                    // Set all file related data
                    $validatedData['file_path'] = $path;
                    $validatedData['file_hash'] = $fileName;
                    $validatedData['file_name'] = $validatedData['file_name'] ?? $originalFilename;
                    $validatedData['file_type'] = $mimeType;
                    $validatedData['file_size'] = $fileSize;
                    $validatedData['file_extension'] = $fileExtension;

                    Log::info('New file uploaded', [
                        'new_path' => $path,
                        'new_name' => $originalFilename,
                        'file_hash' => $fileName,
                        'file_type' => $mimeType,
                        'file_size' => $fileSize,
                        'file_extension' => $fileExtension,
                    ]);
                }

                // Create new attachment record
                $attachment = $this->_model->create($validatedData);
            }

            DB::commit();

            $message = $isUpdate
                ? t($this->config['singular_name'].' has been updated successfully!')
                : t($this->config['singular_name'].' has been added successfully!');

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'id' => $attachment->id,
                    'data' => $attachment->load([
                        'attachment_type',
                        'attachable',
                    ]),
                ]);
            }

            return redirect()
                ->route($this->config['full_route_name'].'.edit', [
                    '_model' => $attachment->id,
                ])
                ->with('status', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in '.$this->config['singular_name'].' add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request_data' => $request->except(['token', 'file']),
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
     * Process file upload and update the validated data
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  &$validatedData  Reference to validated data array
     * @return void
     */
    protected function processFileUpload($request, &$validatedData)
    {
        $file = $request->file('file');

        // Generate unique file name using hash
        $fileHash = md5($file->getClientOriginalName().time());
        $extension = $file->getClientOriginalExtension();
        $fileName = $fileHash.'.'.$extension;

        // Store file in the upload path
        $filePath = $file->storeAs($this->config['upload_path'], $fileName, 'public');

        // Update validated data with file details
        $validatedData['file_path'] = $filePath;
        $validatedData['file_hash'] = $fileHash;
        $validatedData['file_extension'] = $extension;
        $validatedData['file_type'] = $file->getMimeType();
        $validatedData['file_size'] = $file->getSize();

        // Use original filename if no custom name provided
        if (empty($validatedData['file_name'])) {
            $validatedData['file_name'] = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }
    }

    public function delete(Request $request, Attachment $_model)
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

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            '_model' => $this->_model,
            'config' => $this->config,
        ];
        if (in_array($action, ['index'])) {
            // $data['job_vacancies_list'] = JobVacancy::get();
        }
        if (in_array($action, ['create', 'edit'])) {

            // $data['grade_levels'] = GradeLevel::getAllFromDatabase();
            // $data['teachers'] = TeacherProfile::where(['school_id' => $school->id])->get();
        }

        return $data;
    }
}
