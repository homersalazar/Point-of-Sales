@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-5 w-full">
        {{-- <x-page-title title="Role per employee">
                <x-create-button
                    type="button"
                    title="Change Role"
                    form="createForm"
                    id="createModal"
                />

        </x-page-title> --}}
        <div class="font-medium text-lg">
            <div class="font-medium text-lg">
                <label for="">Employee name:</label>
                    {{ ucwords($users->name) }}
            </div>
            <div class="font-medium text-lg">
                <label for="">Role:</label>
                @forelse ($role as $row)
                    <span>{{ ucwords($row['name']) }}</span>
                @empty
                    <span>User</span>
                @endforelse
            </div>
        </div>
        <div class="flex md:flex-row flex-col gap-5 w-full">
            <div id="permission_tree" class="text-sm w-full md:w-1/4"></div>

            <div class="relative overflow-x-auto overflow-y-hidden w-full md:w-3/4">
                <table class="w-full text-sm" style="text-align: left !important;">
                    <thead class="text-xs uppercase bg-gray-200">
                        <tr>
                            <th class="px-4 py-3">Permission</th>
                            <th class="px-4 py-3">Available Permissions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $key => $group)
                            <tr
                                class="bg-white border-b border-gray-200 hover:bg-gray-50"
                                data-group="{{ $key }}"
                            >
                                <td class="px-3 py-2">
                                    <b>{{ ucfirst($key) }}</b>
                                </td>
                                <td class="flex flex-row gap-2 px-3 py-2">
                                    @foreach($group as $permission)
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            id="permission_{{ $permission->id }}"
                                            onclick="chckBox('{{ $permission->id }}', '{{ $permission->name }}')"
                                            {{ in_array($permission['id'], $users->permissions->pluck('id')->concat($role->pluck('permissions.*.id')->flatten())->toArray()) ? 'checked' : '' }}
                                            type="checkbox"
                                            value="{{ $permission->id }}"
                                            class="w-4 h-4 text-teal-600 bg-gray-300 border-gray-300 rounded-sm focus:ring-teal-500 ml-5"
                                        >
                                        &nbsp;{{ ucwords(implode('.', array_slice(explode('.', $permission->name), 1))) }}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const chckBox = (permission_id, permission_name) => {
            var checkBox = document.getElementById(`permission_${permission_id}`);

            Swal.fire({
                text: checkBox.checked
                    ? 'Are you sure to assign permission?'
                    : 'Are you sure to remove permission?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, proceed!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: ``,
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { permission_name },
                        success: function (response) {
                            Swal.fire({
                                title: 'Success!',
                                icon: 'success',
                                text: response.message,
                                timer: 2500,
                                showConfirmButton: false
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                title: 'Error!',
                                icon: 'error',
                                text: xhr.responseJSON?.message || 'Something went wrong',
                            });
                            // revert checkbox state on error
                            checkBox.checked = !checkBox.checked;
                        }
                    });
                } else {
                    checkBox.checked = !checkBox.checked;
                }
            });
        };

        $(document).ready(function () {
            $("#permission_tree").jstree({
                'core': {
                    'data': @json($permissionData),
                },
                "plugins": ["state", "sort", "unique"],
            });

            $('#permission_tree').on('select_node.jstree', function (e, data) {
                const selectNodeName = data.node.text;
                const childNodes = data.node.children_d.map(childId => data.instance.get_node(childId).text);
                const nodesToSearch = [selectNodeName, ...childNodes].map(s => s.toLowerCase());

                // Loop through table rows
                $('table tbody tr').each(function () {
                    const permissionText = $(this).find('td:first').text().toLowerCase();
                    const match = nodesToSearch.some(node => permissionText.includes(node));
                    $(this).toggle(match);
                });
            });

        });
    </script>
@endsection
