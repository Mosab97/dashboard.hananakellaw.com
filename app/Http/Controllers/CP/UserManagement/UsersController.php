<?php

namespace App\Http\Controllers\CP\UserManagement;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CP\UserRequest;
use App\Models\User;
use App\Services\Filters\UserFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    protected $filterService;

    private $_model;

    public function __construct(User $_model, UserFilterService $filterService)
    {
        $this->_model = $_model;
        $this->filterService = $filterService;
        Log::info('............... '.$this->_model::ui['controller_name'].' initialized with '.$this->_model::ui['s_ucf'].' model ...........');
    }

    protected function getCommonData($action = null)
    {
        $data = [
            '_view_path' => $this->_model::ui['view'],
            '_model' => $this->_model,
        ];
        if (in_array($action, ['index'])) {
            // $data['job_vacancies_list'] = JobVacancy::get();
        }
        if (in_array($action, ['create', 'edit'])) {
        }

        return $data;
    }

    public function index(Request $request)
    {
        $data = $this->getCommonData('index');
        if ($request->isMethod('GET')) {
            $data['roles'] = Role::where('name', '!=', 'super-admin')->get();

            return view($data['_view_path'].'index', $data);
        }
        if ($request->isMethod('POST')) {
            $items = $this->_model->query()->with(['branch', 'roles', 'roles.permissions', 'permissions'])
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'super-admin');
                })

                ->select('users.*')
                ->latest($this->_model::ui['table'].'.updated_at');

            if ($request->input('params')) {
                $this->filterService->applyFilters($items, $request->input('params'));
            }

            // return $items->get();
            return DataTables::eloquent($items)

                ->editColumn('created_at', function ($item) {
                    if ($item->created_at) {
                        return [
                            'display' => e(
                                $item->created_at->format('m/d/Y h:i A')
                            ),
                            'timestamp' => $item->created_at->timestamp,
                        ];
                    }
                })

                ->addColumn('action', function ($item) {
                    return $item->action_buttons;
                })
                ->escapeColumns([])
                ->make();
        }
    }

    public function create(Request $request)
    {
        $data = $this->getCommonData('create');
        $data['id'] = $data['role'] = null;
        $data['roles'] = Role::where('name', '!=', 'super-admin')->with('permissions')->get();
        $data['permissions'] = Permission::all();
        $data['earnedRole'] = [];
        $data['earnedPermissions'] = [];

        $createView = view(
            $this->_model::ui['view'].'addedit_modal',
            $data
        )->render();

        return response()->json(['createView' => $createView]);
    }

    public function edit(Request $request, User $_model)
    {
        $data = $this->getCommonData('edit');
        $_model->load('roles');
        $data['roles'] = Role::where('name', '!=', 'super-admin')->with('permissions')->get();
        $data['permissions'] = Permission::all();
        $data['earnedRole'] = $_model->roles->pluck('name')->toArray();
        $data['earnedPermissions'] = $_model->permissions->pluck('name')->toArray();

        $data['_model'] = $_model;

        // dd($data);
        $createView = view(
            $this->_model::ui['view'].'.addedit_modal',
            $data
        )->render();

        return response()->json(['createView' => $createView]);
    }

    public function addedit(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->user_id;
            $user = $id ? User::findOrFail($id) : new User;

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'active' => $request->boolean('active'),
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar') && $request->avatar != 'undefined') {
                Log::info('Processing avatar upload');
                $oldAvatar = $id ? $user->avatar : null;

                $data['avatar'] = uploadImage($request->file('avatar'), 'users');

                if ($oldAvatar) {
                    deleteFile($oldAvatar, 'users');
                }

                Log::info('New avatar uploaded', ['path' => $data['avatar']]);
            }

            $user->fill($data);
            $user->save();

            // $this->syncRolesAndPermissions($user, $request);
            $user->assignRole('admin');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $id ? t('User updated successfully') : t('User added successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in User add/edit process', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function syncRolesAndPermissions($user, $request)
    {
        if ($request->has('custom_permissions')) {
            $rolePermissions = [];
            if ($request->has('role_name')) {
                $role = Role::with('permissions')->where('name', $request->role_name)->first();
                $rolePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];
            }

            $selectedPermissions = collect($request->custom_permissions);
            $extraPermissions = $selectedPermissions->diff($rolePermissions);
            $user->syncPermissions($extraPermissions->toArray());
        } else {
            $user->syncPermissions([]);
        }

        if ($request->has('role_name')) {
            $user->syncRoles([$request->role_name]);
        } else {
            $user->syncRoles([]);
        }
    }

    private function handleError($request, $message, $errors = [])
    {
        Log::warning('Handling error response', [
            'message' => $message,
            'errors' => $errors,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => false,
                'message' => $message,
                'errors' => ['name' => [$message]], // Change to specific field error
            ], 422);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['name' => $message]) // Attach error to specific field
            ->with('error', null); // Don't set general error message
    }

    public function delete(Request $request, User $_model)
    {
        try {
            if ($_model->hasRole('super-admin')) {
                return jsonCRMResponse(false, t('Super admin cannot be deleted'));
            }

            DB::beginTransaction();

            // Soft delete preparation
            $_model->syncRoles([]);
            $_model->email = $_model->email.'_'.uniqid();
            $_model->save();

            $_model->delete();

            DB::commit();

            Log::info($_model::ui['s_ucf'].' deleted successfully', [
                $_model::ui['_id'] => $_model->id,
            ]);

            return jsonCRMResponse(true, t($_model::ui['s_ucf'].' deleted successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting '.$_model::ui['s_ucf'], [
                $_model::ui['_id'] => $_model->id,
                'error' => $e->getMessage(),
            ]);

            return jsonCRMResponse(false, t('Error deleting user'), 500);
        }
    }

    public function export(Request $request)
    {
        $params = $request->all();
        $filterService = $this->filterService;

        return Excel::download(new UsersExport($params, $filterService), $this->_model::ui['p_lcf'].'.xlsx');
    }
}
