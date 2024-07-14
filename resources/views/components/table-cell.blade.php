@if ($type === 'th')
  <th {{ $attributes->class(['p-3 bg-slate-100 font-medium border-[1px]']) }}
    style="{{ $getStyles }}">
    {{ $column['headerName'] }}
  </th>
@else
  <td {{ $attributes->class(['p-3 border-[1px]']) }} style="{{ $getStyles }}">
    @if (isset($column['href']))
      <a href="{{ $column['href'] }}"
        class="text-blue-400 underline">{{ $slot }}</a>
    @else
      {{ $slot }}
    @endif
  </td>
@endif
