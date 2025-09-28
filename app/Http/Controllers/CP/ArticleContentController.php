<?php

namespace App\Http\Controllers\CP;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\ArticleContentRequest;
use App\Models\ArticleContent;
use App\Models\ArticleType;
use App\Services\Filters\ArticleContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Article;

class ArticleContentController extends Controller
{
    protected $filterService;

    private $_model;

    private $config;

    public function __construct(
        ArticleContent $_model,
        ArticleContentFilterService $filterService,
    ) {
        $this->config = config('modules.articles.children.article_contents');
        $this->_model = $_model;
        $this->filterService = $filterService;

        Log::info('............... ' . $this->config['controller'] . ' initialized with ' . $this->config['singular_name'] . ' model ...........');
    }

    public function index(Request $request)
    {
        if ($request->isMethod('POST')) {
            $items = $this->_model->query();
            // ->latest($this->config['table'] . '.updated_at');

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
                    return '<a href="' . route($this->config['full_route_name'] . '.edit', ['_model' => $item->id, 'article' => $item->article_id]) . '" class="fw-bold text-gray-800 text-hover-primary">'
                        . ($item->title ?? 'N/A') . '</a>';
                })
                ->editColumn('features', function ($item) {
                    return collect($item->features)->pluck(app()->getLocale())->toArray() ?? 'N/A';
                })
                ->editColumn('active', function ($item) {
                    return '<span class="badge badge-light-' . ($item->active ? 'success' : 'danger') . '">'
                        . ($item->active ? t('Active') : t('Inactive')) . '</span>';
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
                ->rawColumns(['title', 'features', 'active', 'action'])
                ->make(true);
        }
    }
    public function create(Request $request, Article $article)
    {
        $data = $this->getCommonData('create');
        $data['article'] = $article;
        $createView = view(
            $data['_view_path'] . '.modals.addedit',
            $data
        )->render();
        return response()->json(['createView' => $createView]);
    }

    public function edit(Request $request, Article $article, ArticleContent $_model)
    {
        $data = $this->getCommonData('edit');
        $data['article'] = $article;
        $data['_model']  = $this->_model->where(['article_id' => $article->id])->findOrFail($_model->id);
        $editView = view(
            $data['_view_path'] . '.modals.addedit',
            $data
        )->render();
        return response()->json(['createView' => $editView]);
    }

    public function addedit(ArticleContentRequest $request, Article $article)
    {
        Log::info('=== Starting ' . $this->config['singular_name'] . ' Add/Edit Process ===', [
            'request_data' => $request->except(['password', 'token']),
            'user_id' => auth()->id(),
        ]);

        try {
            $validatedData = $request->validated();
            $id = $request->input($this->config['id_field']);
            $validatedData['article_id'] = $article->id;
            // dd($validatedData);
            if (! empty($id)) {
                $result = ArticleContent::where(['article_id' => $article->id])->findOrFail($id);
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

        }
    }

    public function delete(Request $request, Article $article, ArticleContent $_model)
    {
        try {
            DB::beginTransaction();

            $_model->where(['article_id' => $article->id])->delete();
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
