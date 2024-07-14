@if ($type !== 'textarea')
  <input x-ref="input-{{ $name }}" type="{{ $type }}"
    placeholder="{{ $placeholder }}" name="{{ $name }}"
    value="{{ old($name, $value) }}" id="{{ $name }}"
    @class([
        'w-full rounded-md border-0 py-1.5 px-2.5 text-sm ring-1 placeholder:text-slate-400 focus:ring-2 focus:ring-dark-purple focus:outline-none',
        'ring-slate-300' => !$errors->has($name),
        'ring-red-300' => $errors->has($name),
    ]) />
@else
  <textarea name="{{ $name }}" id="{{ $name }}" cols="30"
    rows="8" @class([
        'w-full rounded-md border-0 py-1.5 px-2.5 text-sm ring-1 placeholder:text-slate-400 focus:ring-2 focus:ring-dark-purple focus:outline-none',
    
        'ring-slate-300' => !$errors->has($name),
        'ring-red-300' => $errors->has($name),
    ])>{{ old($name, $value) }}</textarea>
@endif
