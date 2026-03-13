<x-modal
    id="add_user"
    title="Add User"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addUserForm'
        ]
    ]"
>
    <form action="{{ route('user.create_user') }}" method="POST" class="space-y-2" id="addUserForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Full Name</span>
            </label>
            <x-text-input
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
                type="email"
                size="sm"
                placeholder="Enter Email"
                required
            />
        </div>
    </form>
</x-modal>
