@props([
    'id' => null,
    'name' => null,
    'color' => 'ghost',
    'size' => 'sm',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'disabled' => false,
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $value }}"
    placeholder="{{ $placeholder }}"
    @if ($required) required @endif
    @if ($disabled) disabled @endif
    {{ $attributes->class(['input', "input-{$color}", "input-{$size}", 'w-full', 'input-bordered']) }}
/>
