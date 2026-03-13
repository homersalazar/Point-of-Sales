<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{

    protected $roleService;
    protected $permissionService;

    public function __construct(RoleService $roleService, PermissionService $permissionService)
    {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $permissions = $this->permissionService->getAll();
        $permissionData = [];

        foreach ($permissions as $item) {
            $permissionData[] = [
                'id' => $item->id,
                'parent' => $item->parent_id ?: '#',
                'text' => $item->name,
            ];
        }
        return view('role_permission.index', compact('permissionData'));
    }

    public function getRole(Request $request)
    {
        $roles = $this->roleService->dataTable($request);
        return response()->json($roles);
    }

    public function create_role(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);
        try {
            $this->roleService->store($validated);
            return redirect()->back()->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_role(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$id,
        ]);

        try {
            $role = $this->roleService->show($id);

            $changes = [];
            foreach ($validated as $key => $value) {
                if ($role->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            if (!empty($changes)) {
                $this->roleService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Role updated successfully.'
                ];
            } else {
                $response = [
                    'status' => 'info',
                    'message' => 'No changes were made.'
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function permissionByRole($id)
    {
        $role = $this->roleService->getRoleById($id);
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
        return view('role_permission.permissionByRole', compact('permissionData', 'role'))->with('permissions', $custom_permission);
    }

    // Permission
    public function create_permission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'parent_id' => 'required|integer',
        ]);

        try {
            $this->permissionService->store($validated);
            return redirect()->back()->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_permission(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$id,
        ]);

        try {
            $permission = $this->permissionService->show($id);

            $changes = [];
            foreach ($validated as $key => $value) {
                if ($permission->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            if (!empty($changes)) {
                $this->permissionService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Permission updated successfully.'
                ];
            } else {
                $response = [
                    'status' => 'info',
                    'message' => 'No changes were made.'
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function assign_permission(Request $request, $id)
    {
        try {
            $role = $this->roleService->show($id);
            $permission_name = $request->input('permission_name');
            if ($role && $permission_name) {
                if ($role->permissions->contains('name', $permission_name)) {
                    $role->revokePermissionTo($permission_name);
                    $message = 'Permission removed from the role.';
                } else {
                    $role->givePermissionTo($permission_name);
                    $message = 'Permission added to the role.';
                }
                return response()->json(['message' => $message], 200);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function revoke_permission(Request $request, $id)
    {
        try {
            $role = $this->roleService->show($id);
            $permission_name = $request->input('permission_name');

            if ($role && $permission_name) {
                $role->revokePermissionTo($permission_name);
                $message = 'Permission removed to the role.';
                return response()->json(['message' => $message], 200);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
