<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $userService;
    protected $permissionService;
    protected $roleService;

    public function __construct(
        UserService $userService,
        PermissionService $permissionService,
        RoleService $roleService
    )
    {
        $this->userService = $userService;
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    public function index()
    {
        return view('user.index');
    }

    public function getUsers(Request $request)
    {
        $users = $this->userService->dataTable($request);
        return response()->json($users);
    }

    public function create_user(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
        ]);

        try {
            // Add default password
            $validated['password'] = bcrypt('password'); // or Hash::make('password')

            // Store the user
            $user = $this->userService->store($validated);

            // Assign role
            $user->assignRole('Cashier'); // Make sure 'Cashier' exists in your roles table

            return redirect()->back()->with('success', 'User created successfully with default password.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_user(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($id),
            ]
        ]);

        try {
            $user = $this->userService->show($id);

            // Fill new values (does NOT save yet)
            $user->fill($validated);

            // Check if anything changed
            if ($user->isDirty()) {

                $user->save();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'User updated successfully.'
                ], 200);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'No changes were made.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function assign_access($id)
    {
        try {
            $users = $this->userService->show($id);
            $role = $users->roles;
            $role_permission = $this->permissionService->getAllPermissions();

            $custom_permission = [];
            foreach ($role_permission as $permission) {
                $key = substr($permission->name, 0, strpos($permission->name, "."));
                $custom_permission[$key][] = $permission;
            }

            $permissions = $this->permissionService->sanitizePermissionName();
            $permissionData = [];
            foreach ($permissions as $item) {
                $permissionData[] = [
                    'id' => $item->id,
                    'parent' => $item->parent_id ?: '#',
                    'text' => $item->name,
                ];
            }

            $rolesList = $this->roleService->getAll();

            return view('user.assign_access', compact('permissionData', 'role', 'users', 'rolesList'))
                ->with('permissions', $custom_permission);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function change_role(Request $request)
    {
        try {
            $users = $this->userService->show($request->user_id);
            $users->syncRoles($request->role);
            return redirect()->back()->with('success', 'Role changed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function assign_permission(Request $request, $id)
    {
        try {
            $users = $this->userService->show($id);
            $permission_name = $request->input('permission_name');

            if ($users && $permission_name) {
                if ($users->permissions->contains('name', $permission_name)){
                    $users->revokePermissionTo($permission_name);
                    $message = 'Permission removed from the user.';
                } else {
                    $users->givePermissionTo($permission_name);
                    $message = 'Permission added to the user.';
                }
                return response()->json(['message' => $message], 200);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        return $this->userService->login($credentials);
    }

    public function logout()
    {
        $this->userService->logout(); // just logs the user out
        return redirect('/')->with('success', 'Logged out successfully!');
    }
}
