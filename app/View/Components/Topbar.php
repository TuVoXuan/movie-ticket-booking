<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class Topbar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function getTitle(): string
    {
        $currentRouteName = Route::currentRouteName();

        $routeToTitleMap = [
            'dashboard' => 'Dashboard',
            'users.*' => 'Users',
            'films.*' => 'Films',
            'artists.*' => 'Artists',
            'genres.*' => 'Genres',
            'Cinemas.*' => 'Cinemas',
            'Roles.*' => 'Roles',
            'Permissions.*' => 'Permissions',
            'Users.*' => 'Users'
        ];

        foreach ($routeToTitleMap as $routePattern => $title) {
            if (Str::is($routePattern, $currentRouteName)) {
                return $title;
            }
        }

        return ucwords(str_replace('.', ' ', $currentRouteName));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.topbar');
    }
}
