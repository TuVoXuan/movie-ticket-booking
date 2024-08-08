<?php

namespace App\Http\Controllers;

use App\Helpers\SlugHelper;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Roles/Index', ['roles' => Role::all()]);
    }

    public function store(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|min:1|string'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $code = SlugHelper::convertToSlug($body['name']);
            Role::create([
                'name' => $body['name'],
                'code' => $code
            ]);

            return redirect()->route('roles.index')->with('success', 'Create role successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('roles.index')->with('error', 'And error occurred during create role.');
        }
    }

    public function update(Request $request, string $role)
    {
        try {
            $foundRole = Role::find($role);
            if (!$foundRole) {
                return back()->with('error', 'Role not found.');
            }

            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|min:1|string'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $code = SlugHelper::convertToSlug($body['name']);
            $foundRole->update([
                'name' => $body['name'],
                'code' => $code
            ]);

            return redirect()->route('roles.index')->with('success', 'Update role successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('roles.index')->with('error', 'And error occurred during update role.');
        }
    }

    public function destroy(Request $request, string $role)
    {
        try {
            Role::destroy($role);
            return redirect()->route('roles.index')->with('success', 'Delete role successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('roles.index')->with('error', 'And error occurred during delete role.');
        }
    }
}
