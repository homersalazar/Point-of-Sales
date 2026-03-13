@extends('layouts.app')

@section('content')
    @include('role_permission.partials.create_role_modal')
    @include('role_permission.partials.update_role_modal')

    @include('role_permission.partials.create_permission_modal')
    @include('role_permission.partials.update_permission_modal')

    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Role & Permissions
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage your roles and permissions.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_role"
                >
                    Add Role
                </x-button>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="flex flex-row w-full gap-5 bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">

            <!-- Permission Tree: 2/3 width -->
            <div class="flex-[1] text-sm" id="permission_tree"></div>

            <!-- Role Table: 1/3 width -->
            <div class="flex-[2]">
                @include('role_permission.partials.role_table')
            </div>

        </div>
    </div>
    <script>
        $(function () {
            $('#permission_tree').jstree({
                'core': {
                    'data': @json($permissionData),
                },
                "plugins": ["state", "contextmenu", "sort"],
                'contextmenu': {
                    items: function (node) {
                        var menu = {};
                            menu.add = {
                                label: 'Create',
                                action: function () {
                                    document.getElementById('add_permission').checked = true;
                                    document.querySelector('#addPermissionForm input[name="parent_id"]').value = node.id;
                                }
                            };

                            menu.edit = {
                                label: 'Rename',
                                action: function () {
                                    document.getElementById('update_permission_modal').checked = true;
                                    document.querySelector('#updatePermissionForm input[name="name"]').value = node.text;
                                    $("#updatePermissionForm").off("submit").on("submit", function (e) {
                                        e.preventDefault();
                                        const formData = $(this).serialize();
                                        $.ajax({
                                            url: `/access/update_permission/${node.id}`,
                                            method: "POST",
                                            data: formData,
                                            success: function (data) {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: data.message,
                                                    icon: 'success',
                                                    showConfirmButton: false,
                                                    timer: 3000
                                                }).then(() => {
                                                    window.location.reload();
                                                });
                                            },
                                            error: function (xhr) {
                                                let message = 'An error occurred while updating the event.';
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
                                    });
                                }
                            };
                        return menu;
                    }
                }
            });
        });
    </script>
@endsection
