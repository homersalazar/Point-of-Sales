<x-modal
    id="add_role"
    title="Add Role"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addRoleForm'
        ]
    ]"
>
    <form action="{{ route('roles.create_role') }}" method="POST" class="space-y-2" id="addRoleForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Role Name</span>
            </label>
            <x-text-input
                name="name"
                size="sm"
                placeholder="Enter Role name"
                required
            />
        </div>
    </form>
</x-modal>
