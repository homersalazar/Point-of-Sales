@props([
    'placeholder' => null,
    'id' => null,
    'name' => null,
    'required' => false,
    'disabled' => false,
    'color' => null,
])

<textarea
    id="{{ $id }}"
    name="{{ $name }}"
    class="textarea textarea-{{ $color }} textarea-bordered w-full" placeholder="{{ $placeholder }}"
    @if ($required) required @endif
    @if ($disabled) disabled @endif
>{{ $slot }}</textarea>
