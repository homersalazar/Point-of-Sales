<x-modal
    id="update_user_modal"
    title="Update User"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateUserForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateUserForm">
        @csrf
        @method('PUT')
        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Full Name</span>
            </label>
            <x-text-input
                id="name"
                name="name"
                size="sm"
                placeholder="Enter Full name"
                required
            />
        </div>

        {{-- Email --}}
        <div>
            <label class="label">
                <span class="label-text">Email</span>
            </label>
            <x-text-input
                name="email"
                id="email"
                placeholder="Enter Email"
                size="sm"
                type="email"
            />
        </div>

    </form>
</x-modal>
