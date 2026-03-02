

<div id="unitTable">
    <x-table  :headers="['Name', 'Abbreviation', 'Action']">
        @forelse ($units as $row)
            <tr>
                <th>{{ $row->name }}</th>
                <td>{{ $row->abbreviation }}</td>
                <td>
                    <div class="flex flex-row gap-2 w-full">
                        <x-button
                            color="info"
                            outline
                            onclick="update_unit('{{ $row->id }}', '{{ $row->name }}', '{{ $row->abbreviation }}')"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </x-button>

                        <x-button
                            color="error"
                            outline
                            onclick="delete_unit('{{ $row->id }}')"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">No units found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($units instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $units->links() }}
        </div>
    @endif
</div>

<script>
    const update_unit = (unit_id, unit_name, unit_abbreviation) => {
        // Open modal
        document.getElementById('update_unit_modal').checked = true;

        // Fill inputs
        document.getElementById('update_name').value = unit_name;
        document.getElementById('update_abbreviation').value = unit_abbreviation;

        // Handle form submit
        $("#updateUnitForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/unit/update_unit/${unit_id}`,
                method: "POST",
                data: formData,
                success: function(data){
                    Swal.fire({
                    title: data.status === 'success' ? 'Success!' : 'Info!',
                        text: data.message,
                        icon: data.status,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr){
                    let message = 'An error occurred while updating the category.';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            });
        });
    }

    const delete_unit = id => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                    $.ajax({
                    url: `/unit/delete_unit/${id}`,
                    method:"POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'delete'
                    },
                    success:function(data){
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        let message = 'An error occurred while deleting the event.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        else if (xhr.responseText) {
                            try {
                                let parsed = JSON.parse(xhr.responseText);
                                if (parsed.message) {
                                    message = parsed.message;
                                }
                            } catch (e) {
                                message = xhr.responseText;
                            }
                        }
                        Swal.fire({
                            title: "Info!",
                            text: message,
                            icon: "info",
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                });
            }
        });
    }
</script>
