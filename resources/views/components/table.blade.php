@php
  $users = [
      [
          'id' => 1,
          'name' => 'John Doe',
          'age' => 25,
          'phone' => '123-456-7890',
          'address' => 'Tokyo Japan',
      ],
      [
          'id' => 2,
          'name' => 'Jane Smith',
          'age' => 30,
          'phone' => '234-567-8901',
          'address' => 'Tokyo Japan',
      ],
      [
          'id' => 3,
          'name' => 'Alice Johnson',
          'age' => 28,
          'phone' => '345-678-9012',
          'address' => 'Tokyo Japan',
      ],
      [
          'id' => 4,
          'name' => 'Bob Brown',
          'age' => 35,
          'phone' => '456-789-0123',
          'address' => 'Tokyo Japan',
      ],
  ];

  // columns is an array has many value
  // $columns = [
  //     [
  //         'headerName' => '',
  //         'field' => 'id',
  //         'width' => 50,
  //         'align' => 'center',
  //     ],
  //     [
  //         'headerName' => 'Name',
  //         'field' => 'name',
  //         'minWidth' => 150,
  //         'href' => route('dashboard'),
  //     ],
  //     [
  //         'headerName' => 'Actions',
  //         'minWidth' => 120,
  //         'align' => 'center',
  //         'actions' => [
  //             'edit' => [
  //                 'icon' => 'icons.pen_solid',
  //                 'href' => route('dashboard'),
  //                 'class' =>
  //                     'flex items-center justify-center h-8 w-8 rounded-full hover:bg-blue-200 transition-all ease-liner text-blue-500',
  //             ],
  //             'delete' => [
  //                 'icon' => 'icons.trash_solid',
  //                 'class' =>
  //                     'flex items-center justify-center h-8 w-8 rounded-full hover:bg-red-200 transition-all ease-liner text-red-500',
  //             ],
  //         ],
  //     ],
  // ];

  $columns = [
      [
          'headerName' => '',
          'field' => 'id',
          'width' => 50,
          'align' => 'center',
      ],
      [
          'headerName' => 'Name',
          'field' => 'name',
          'minWidth' => 150,
          'href' => route('dashboard'),
      ],
      [
          'headerName' => 'Age',
          'field' => 'age',
          'minWidth' => 50,
          'align' => 'right',
      ],
      [
          'headerName' => 'Phone',
          'field' => 'phone',
          'minWidth' => 200,
          'align' => 'right',
      ],
      [
          'headerName' => 'Address',
          'field' => 'address',
          'minWidth' => 1000,
      ],
      [
          'headerName' => 'Actions',
          'minWidth' => 120,
          'align' => 'center',
          'actions' => [
              'edit' => [
                  'icon' => 'icons.pen_solid',
                  'href' => route('dashboard'),
                  'class' =>
                      'flex items-center justify-center h-8 w-8 rounded-full hover:bg-blue-200 transition-all ease-liner text-blue-500',
              ],
              'delete' => [
                  'icon' => 'icons.trash_solid',
                  'class' =>
                      'flex items-center justify-center h-8 w-8 rounded-full hover:bg-red-200 transition-all ease-liner text-red-500',
              ],
          ],
      ],
  ];
@endphp

<div class="overflow-x-auto">
  <table class="table-fixed">
    <thead>
      <tr>
        @foreach ($columns as $column)
          <x-table-cell type="th" :$column />
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($users as $user)
        <tr>
          @foreach ($columns as $column)
            @if (!isset($column['actions']))
              {{-- render normal cell of each row --}}
              <x-table-cell type='td' :$column>
                {{ $user[$column['field']] }}
              </x-table-cell>
            @else
              {{-- render action cell of each row --}}
              <x-table-cell type='td' :$column
                class="flex items-center justify-center gap-x-3">
                @if (isset($column['actions']['edit']))
                  <a href="{{ $column['actions']['edit']['href'] }}"
                    class="{{ $column['actions']['edit']['class'] }}">
                    <div class='h-4 w-4'>
                      <x-dynamic-component :component="$column['actions']['edit']['icon']" />
                    </div>
                  </a>
                @endif

                @if (isset($column['actions']['delete']))
                  <button class="{{ $column['actions']['delete']['class'] }}">
                    <div class='h-4 w-4'>
                      <x-dynamic-component :component="$column['actions']['delete']['icon']" />
                    </div>
                  </button>
                @endif
              </x-table-cell>
            @endif
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
