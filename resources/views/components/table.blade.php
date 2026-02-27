@props([
    'id' => null,
    'headers' => []
])

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table {{ $attributes->class(['table', 'table-sm', 'table-bordered', 'table-zebra', 'w-full']) }} id="{{ $id }}">
        <thead class="bg-base-300">
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody id="{{ $id }}-body">
            {{ $slot }}
        </tbody>
    </table>
</div>
