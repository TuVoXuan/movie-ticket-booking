<a href="{{ $href }}" @class([
    'flex items-center gap-x-2 px-3 py-2 rounded-md cursor-pointer transition-all ease-linear',
    'bg-[#5E5FEF] text-white' => $isActive,
    'bg-white text-black hover:bg-[#5E5FEF]/20' => !$isActive,
])>
  <div class="h-6 w-6">
    @if ($isActive)
      <x-dynamic-component :component="$activeIcon" />
    @else
      <x-dynamic-component :component="$inactiveIcon" />
    @endif
  </div>

  <span @class(['font-medium' => $isActive])>{{ $title }}</span>
</a>
