@props([
    'id' => null,
    'name' => null,
    'color' => null,
    'size' => 'sm',
    'caption' => null,
    'onchange' => null
])
<select
    id="{{ $id }}"
    name="{{ $name }}"
    {{ $attributes->class([
        'select',
        'select-' . $size,
        'select-' . $color,
        'select-bordered w-full',
    ]) }}
    @if($onchange) onchange="{{ $onchange }}" @endif

>
    @if ($caption)
        <option value="" disabled selected>{{ $caption }}</option>
    @endif
    {{ $slot }}
</select>
