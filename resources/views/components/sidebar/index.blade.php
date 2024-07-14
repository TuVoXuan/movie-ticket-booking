@php
  $sidebarItems = [
      [
          'isActive' => Route::is('dashboard'),
          'href' => route('dashboard'),
          'activeIcon' => 'icons.chart_solid',
          'inactiveIcon' => 'icons.chart_outline',
          'title' => 'Dashboard',
      ],
      [
          'isActive' => Route::is('films.*'),
          'href' => route('films.index'),
          'activeIcon' => 'icons.film_solid',
          'inactiveIcon' => 'icons.film_outline',
          'title' => 'Films',
      ],
      [
          'isActive' => Route::is('artists'),
          'href' => route('artists'),
          'activeIcon' => 'icons.artist_solid',
          'inactiveIcon' => 'icons.artist_outline',
          'title' => 'Artist',
      ],
      [
          'isActive' => Route::is('genres'),
          'href' => route('genres'),
          'activeIcon' => 'icons.category_solid',
          'inactiveIcon' => 'icons.category_outline',
          'title' => 'Generes',
      ],
      [
          'isActive' => Route::is('cinemas'),
          'href' => route('cinemas'),
          'activeIcon' => 'icons.camera_solid',
          'inactiveIcon' => 'icons.camera_outline',
          'title' => 'Cinemas',
      ],
      [
          'isActive' => Route::is('roles'),
          'href' => route('roles'),
          'activeIcon' => 'icons.role_solid',
          'inactiveIcon' => 'icons.role_outline',
          'title' => 'Roles',
      ],
      [
          'isActive' => Route::is('permissions'),
          'href' => route('permissions'),
          'activeIcon' => 'icons.permission_solid',
          'inactiveIcon' => 'icons.permission_outline',
          'title' => 'Permissions',
      ],
      [
          'isActive' => Route::is('users.*'),
          'href' => route('users.index'),
          'activeIcon' => 'icons.user_solid',
          'inactiveIcon' => 'icons.user_outline',
          'title' => 'Users',
      ],
  ];
@endphp
<div class="flex flex-col gap-y-3">
  @foreach ($sidebarItems as $item)
    <x-sidebar.item :is-active="$item['isActive']" :href="$item['href']" :active-icon="$item['activeIcon']"
      :inactive-icon="$item['inactiveIcon']" :title="$item['title']" />
  @endforeach
</div>
