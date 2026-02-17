@props([
    'id' => null,
    'title' => null,
    'buttons' => [],
    'size' => 'md',
    'form' => null
])

<input type="checkbox" id="{{ $id }}" class="modal-toggle" />

<div class="modal" role="dialog">
    <div class="modal-box max-w-{{ $size }} relative">

        <h4 class="font-bold text-lg mb-2">
            {{ $title }}
        </h4>

        {{ $slot }}

        <div class="modal-action">

            @if(!empty($buttons))
                <div class="flex items-center gap-2">
                    @foreach($buttons as $btn)
                        <button
                            @isset($btn['form']) form="{{ $btn['form'] }}" @endisset
                            type="{{ $btn['type'] ?? 'button' }}"
                            class="btn btn-{{ $btn['color'] }} btn-sm"
                            @isset($btn['onclick']) onclick="{{ $btn['onclick'] }}" @endisset
                        >
                            {{ $btn['label'] ?? 'Button' }}
                        </button>
                    @endforeach
                </div>
            @endif

            <label for="{{ $id }}" class="btn btn-ghost btn-sm">
                Close
            </label>

        </div>
    </div>
</div>
