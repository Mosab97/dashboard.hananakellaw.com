<?php

namespace App\Http\Controllers\CP;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\WhyChooseUsRequest;
use App\Models\WhyChooseUs;
use App\Services\Filters\WhyChooseUsFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class WhyChooseUsController extends Controller
{
    protected $filterService;

    private $_model;

    private $config;

    public function __construct(
        WhyChooseUs $_model,
        WhyChooseUsFilterService $filterService,
    ) {
        $this->config = config('modules.why_choose_us');
        $this->_model = $_model;
        $this->filterService = $filterService;

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
                ->editColumn('title', function ($item) {
                    return '<a href="' . route($this->config['full_route_name'] . '.edit', ['_model' => $item->id]) . '" class="fw-bold text-gray-800 text-hover-primary">'
                        . ($item->title ?? 'N/A') . '</a>';
                })
                ->editColumn('image', function ($item) {
                    return '<img src="' . $item->image_path . '" alt="' . $item->title . '" class="img-fluid" style="max-width: 100px; max-height: 100px;">';
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
                ->rawColumns(['title', 'image', 'status', 'action'])
                ->make(true);
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function edit(Request $request, WhyChooseUs $_model)
    {
        $data = $this->getCommonData('edit');
        $data['_model'] = $_model;

        return view($data['_view_path'] . '.addedit', $data);
    }

    public function addedit(WhyChooseUsRequest $request)
    {
        Log::info('=== Starting ' . $this->config['singular_name'] . ' Add/Edit Process ===', [
            'request_data' => $request->except(['password', 'token']),
            'user_id' => auth()->id(),
        ]);

        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);
            $imageFile = $request->hasFile('image') ? $request->file('image') : null;
            // dd($validatedData);
            if ($imageFile) {
                $imagePath = Storage::disk('public')->putFile('why_choose_us', $imageFile);
                $validatedData['image'] = $imagePath;
            }
            if (! empty($id)) {
                $result = WhyChooseUs::findOrFail($id);
                if ($request->has('delete_image')) {
                    if (isset($result->image)) {
                        Storage::disk('public')->delete($result->image);
                    }
                    $validatedData['image'] = null;
                }
                $result->update($validatedData);
            } else {
                $result = $this->_model->create($validatedData);
            }
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => t($this->config['singular_name'] . ' Added Successfully!'),
                    'id' => $result->id,
                    'data' => $result,
                ]);
            }

            return redirect()
                ->route($this->config['full_route_name'] . '.edit', ['_model' => $result->id])
                ->with('status', t($this->config['singular_name'] . ' Added Successfully!'));
        } catch (\Exception $e) {
            Log::error('Error in ' . $this->config['singular_name'] . ' add/edit process', [
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

    public function delete(Request $request, WhyChooseUs $_model)
    {
        try {
            DB::beginTransaction();

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

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->config['view_path'],
            '_model' => $this->_model,
            'config' => $this->config,
        ];

        // Add data lists needed for forms
        if (in_array($action, ['create', 'edit'])) {
        }

        return $data;
    }
}
