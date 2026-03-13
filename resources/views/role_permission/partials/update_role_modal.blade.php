<x-modal
    id="update_role_modal"
    title="Update Role"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateRoleForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateRoleForm">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Role Name</span>
            </label>
            <x-text-input
                id="name"
                name="name"
                size="sm"
                placeholder="Enter Role name"
                required
            />
        </div>

    </form>
</x-modal>
