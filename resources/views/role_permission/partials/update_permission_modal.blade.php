<x-modal
    id="update_permission_modal"
    title="Update Permission"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updatePermissionForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updatePermissionForm">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Permission Name</span>
            </label>
            <x-text-input
                id="name"
                name="name"
                size="sm"
                placeholder="Enter Permission name"
                required
            />
        </div>

    </form>
</x-modal>
