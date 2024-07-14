<button type="{{ $type ?? 'button' }}"
  {{ $attributes->class(['rounded-md px-3 py-2 font-medium transition-colors', $getVariant]) }}>
  {{ $slot }}
</button>
