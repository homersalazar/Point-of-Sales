@props([
    'status' => 'Pending',
])

@php
    $classes = match($status) {
        'pending'   => 'bg-yellow-100 text-yellow-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'completed' => 'bg-green-100 text-green-700',
        default     => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "px-3 py-1 rounded-lg text-center font-semibold text-sm $classes"
]) }}>
    {{ ucwords($status) }}
</span>
