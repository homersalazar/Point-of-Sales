@props([
    'color' => 'primary',
    'checked' => false,
    'label' => null,
    'id' => null,
    'size' => 'sm',
    'disabled' => false
])

<div class="form-control">
    <label class="cursor-pointer label">
        <span class="label-text">{{ $label }}</span>
        <input type="checkbox"
        id="{{ $id }}"
        class="checkbox checkbox-{{ $color }} checkbox-{{ $size }}"
        @if ($checked) checked @endif
        @if ($disabled) disabled @endif
    >
    </label>
</div>
