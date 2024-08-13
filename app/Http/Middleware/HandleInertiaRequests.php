<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $permissions = null;
        if (isset($user)) {
            // Eager load roles and permissions
            $userWithPermissions =  $user->load('roles.permissions:id,code,name');

            // Get all permissions from the user's roles
            $permissions = $userWithPermissions->roles->flatMap(function ($role) {
                return $role->permissions;
            })->unique('id');
        }


        return array_merge(parent::share($request), [
            'success' => session('success'),
            'error' => session('error'),
            'query' => $request->query(),
            'curUser' => $user,
            'permissions' => $permissions
        ]);
    }
}
