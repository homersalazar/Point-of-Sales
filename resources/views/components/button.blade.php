@props([
    'id' => null,
    'type' => 'button',
    'size' => 'sm',
    'color' => 'error',
    'form' => null,
    'icon' => null,
    'outline' => false,
    'onclick' => null,
])

<button
    @if($onclick) onclick="document.getElementById('{{ $onclick }}').click()" @endif
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
