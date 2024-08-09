<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with(['permissions'])->get();
        return Inertia::render('Permissions/Index', [
            'permissions' => Permission::select(['id', 'name', 'code'])->get(),
            'roles' => $roles
        ]);
    }

    public function updateRolePermission(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'role' => 'required|numeric|exists:roles,id',
                'added_permissions' => 'present|array',
                'added_permissions.*' => 'present|numeric|exists:permissions,id',
                'deleted_permissions' => 'present|array',
                'deleted_permissions.*' => 'present|numeric|exists:permissions,id'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            if ($request->has('added_permissions')) {
                foreach ($body['added_permissions'] as $permissionId) {
                    RolePermission::create([
                        'role_id' => $body['role'],
                        'permission_id' => $permissionId
                    ]);
                }
            }

            if ($request->has('deleted_permissions')) {
                foreach ($body['deleted_permissions'] as $permissionId) {
                    $rolePermission = RolePermission::where('role_id', '=', $body['role'])
                        ->where('permission_id', '=', $permissionId)->first();
                    if ($rolePermission) {
                        $rolePermission->delete();
                    }
                }
            }

            return redirect()->route('permissions.index')->with('success', 'Update permissions for role successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('permissions.index')->with('error', 'An error occurred during update role permission.');
        }
    }
}
