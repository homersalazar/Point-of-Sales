<x-modal
    id="add_permission"
    title="Add Permission"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addPermissionForm'
        ]
    ]"
>
    <form action="{{ route('permissions.create_permission') }}" method="POST" class="space-y-2" id="addPermissionForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Permission Name</span>
            </label>
            <x-text-input
                name="name"
                size="sm"
                placeholder="Enter Permission name"
                required
            />
        </div>

        {{-- Parent Permission --}}
        <x-text-input
            name="parent_id"
            type="hidden"
            size="sm"
            placeholder="Enter Parent Permission ID"
            readonly
            required
        />
    </form>
</x-modal>
