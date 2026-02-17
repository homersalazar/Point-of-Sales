@props([
    'id' => null,
    'name' => null,
    'color' => 'ghost',
    'size' => 'sm',
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="file"
    class="file-input file-input-bordered file-input-{{ $color }} file-input-{{ $size }} w-full max-w-{{ $size }}"
/>
