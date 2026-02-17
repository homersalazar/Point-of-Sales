<div class="flex flex-row w-64 mb-2 gap-2">
    <x-select name="per_page" id="perPageSelect" class="w-20">
        @foreach ([10, 25, 50, 100, -1] as $size)
            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                {{ $size == -1 ? 'All' : $size }}
            </option>
        @endforeach
    </x-select>
    <p>entries per page</p>
</div>
