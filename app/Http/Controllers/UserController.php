<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $pageSize = $request->query('page_size', 10);
        $sort = $request->query('sort');
        $sort_order = $request->query('sort_order', 'asc');
        $role = $request->query('role');

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('account', 'LIKE', '%' . $search . '%');
            })->when($sort, function ($query, $sort) use ($sort_order) {
                $query->orderBy($sort, $sort_order);
            })->when($role, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('code', '=', $role);
                });
            })->paginate($pageSize);

        $roles = Role::select(['id', 'name', 'code'])->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $roles = Role::select(['id', 'name', 'code'])->get();
        return Inertia::render('Users/CreateAndUpdate', ['roles' => $roles]);
    }

    public function store(Request $request)
    {
        try {
            $body = $request->all();
            $validated = Validator::make($body, [
                'name' => 'required|string|min:3',
                'email' => 'required|email|unique:users,email',
                'account' => 'required|string|min:4|unique:users,account',
                'role' => 'required|numeric|exists:roles,id',
                'is_active' => 'required|boolean'
            ]);

            if ($validated->fails()) {
                return back()->withErrors($validated->errors());
            }

            $password = Hash::make('@admin2024');

            $user = User::create([
                'name' => $body['name'],
                'email' => $body['email'],
                'password' => $password,
                'account' => $body['account'],
                'is_active' => $body['is_active']
            ]);

            UserRole::create([
                'role_id' => $body['role'],
                'user_id' => $user->id
            ]);

            return redirect()->route('users.index')->with('success', 'Create user successfully.');
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('users.create')->with('error', 'An error occurred during create user.');
        }
    }

    public function edit(Request $request, string $id)
    {
        try {
            $user = User::with('roles')->find($id);
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User not found.');
            }
            $roles = Role::select(['id', 'name', 'code'])->get();
            return Inertia::render('Users/CreateAndUpdate', [
                'roles' => $roles,
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('users.create')->with('error', 'An error occurred during get user.');
        }
    }
}
