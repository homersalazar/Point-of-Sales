@props([
    'src' => null,
    'title' => null,
    'description' => null
])

<div {{ $attributes->class(['card', 'card-compact', 'bg-base-100', 'w-full', 'shadow-xl', 'hover:scale-105 transition-transform duration-300', 'hover:ring-2 hover:ring-base-200']) }}>
    @if ($src)
        <figure>
            <img src="{{ $src }}" alt="{{ $title }}" />
        </figure>
    @endif
    <div class="py-2.5 px-3">
        <h2 class="card-title text-base">{{ $title }}</h2>
        <p class="text-sm text-neutral">{{ $description }}</p>
    </div>
</div>
