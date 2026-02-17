

<div id="categoryTable">
    <x-table id="categoryTables" :headers="['Name', 'Description', 'Action']">
        @forelse ($categories as $row)
            <tr>
                <th>{{ $row->name }}</th>
                <td>{{ $row->description }}</td>
                <td>
                    <div class="flex flex-row gap-2 w-full">
                        <x-button color="info" outline>
                            <i class="fa-solid fa-pen-to-square"></i>
                        </x-button>

                        <x-button color="error" outline>
                            <i class="fa-solid fa-trash-can"></i>
                        </x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">No categories found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif
</div>
