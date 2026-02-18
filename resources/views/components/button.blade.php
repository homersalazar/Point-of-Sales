@props([
    'id' => null,
    'type' => 'button',
    'size' => 'sm',
    'color' => 'error',
    'form' => null,
    'icon' => null,
    'outline' => false,
    'click' => null,
])

<button
    @if($click) onclick="document.getElementById('{{ $click }}').click()" @endif
    id="{{ $id }}"
    type="{{ $type }}"
    @if($form) form="{{ $form }}" @endif
    {{ $attributes->class([
        'btn',
        'btn-' . $color,
        'btn-' . $size,
        'btn-outline' => $outline,
    ]) }}
>
    @if ($icon)
        <i class="fa {{ $icon }}"></i>
    @endif
    {{ $slot }}
</button>
