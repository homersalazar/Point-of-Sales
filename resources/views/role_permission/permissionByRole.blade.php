@extends('layouts.app')

@section('content')
    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    {{ strtoupper($role->name) }}
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage Permission by Role
                </p>
            </div>

        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="flex flex-row w-full gap-5 bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            <div class="flex-[1] text-sm" id="permission_tree"></div>
            <div class="flex-[2]">
                <x-table
                    :headers="['Permission', 'Available Permissions']"
                >
                    @foreach($permissions as $key => $group)
                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50" data-group="{{ $key }}">
                            <td class="px-3 py-2">
                                <b>{{ ucfirst($key) }}</b>
                            </td>
                            <td class="flex flex-row gap-2 px-3 py-2">
                                @foreach($group as $permission)
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        id="permission_{{ $permission->id }}"
                                        value="{{ $permission->id }}"
                                        onclick="chckBox('{{ $role->id }}', '{{ $permission->id }}', '{{ $permission->name }}')"
                                        {{ $role->permissions->contains('id', $permission->id) ? 'checked' : '' }}
                                        class="w-4 h-4 text-teal-600 bg-gray-300 border-gray-300 rounded-sm focus:ring-teal-500 ml-5"
                                    >
                                    &nbsp;{{ ucwords(implode('.', array_slice(explode('.', $permission->name), 1))) }}
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </div>
        </div>
    </div>

    <script>
        const chckBox = (role_id, permission_id, permission_name) => {
            var checkBox = document.getElementById(`permission_${permission_id}`);
            if (checkBox.checked == true) {
                Swal.fire({
                    text: 'Are you sure to assign permission?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/access/assign_permission/${role_id}`,
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { permission_name },
                            success: function (response) {
                                Swal.fire({ title: 'Success!', icon: 'success', text: response.message, timer: 2500, showConfirmButton: false });
                            }
                        });
                    } else {
                        checkBox.checked = false;
                    }
                });
            }else{
                Swal.fire({
                    text: 'Are you sure to revoke permission?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/access/revoke_permission/${role_id}`,
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { permission_name },
                            success: function (response) {
                                Swal.fire({ title: 'Success!', icon: 'success', text: response.message, timer: 2500, showConfirmButton: false });
                            }
                        });
                    } else {
                        checkBox.checked = true;
                    }
                });
            }
        }

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
