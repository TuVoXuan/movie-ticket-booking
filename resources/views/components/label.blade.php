<label for="{{ $for }}" class="mb-2 text-sm font-medium text-slate-900">
  {{ $slot }}
  @if ($required)
    <span class="">*</span>
  @endif
</label>
