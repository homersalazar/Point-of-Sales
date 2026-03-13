@extends('layouts.app')

@section('content')
    @include('user.partials.change_role_modal')

    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    {{ ucwords($users->name) }}
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Role:
                    @forelse ($role as $row)
                        <span class="text-secondary">{{ ucwords($row['name']) }}</span>
                    @empty
                        <span>User</span>
                    @endforelse
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-rotate"
                    click="change_role"
                >
                    Change Role
                </x-button>
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
                </x-table>
            </div>
        </div>
    </div>
    <script>
        const chckBox = (permission_id, permission_name) => {
            var checkBox = document.getElementById(`permission_${permission_id}`);

            Swal.fire({
                text: checkBox.checked
                    ? 'Are you sure to assign permission?'
                    : 'Are you sure to revoke permission?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, proceed!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('user.assign_permission', $users->id) }}`,
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
