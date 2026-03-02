<x-modal
    id="add_customer"
    title="Add Customer"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addCustomerForm'
        ]
    ]"
>
    <form
        action="{{ route('customer.create_customer') }}"
        method="POST"
        class="space-y-2"
        id="addCustomerForm"
    >
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Name</span>
            </label>
            <x-text-input
                name="name"
                size="sm"
                placeholder="Enter Customer name"
                required
            />
        </div>

        {{-- Email --}}
        <div>
            <label class="label">
                <span class="label-text">Description</span>
            </label>
            <x-text-input
                name="email"
                placeholder="Enter Email"
                size="sm"
                type="email"
            />
        </div>

        {{-- Phone --}}
        <div>
            <label class="label">
                <span class="label-text">Phone</span>
            </label>
            <x-text-input
                name="phone"
                placeholder="Enter Phone"
                size="sm"
                pattern="^(09|\+639|639)\d{9}$"
                maxlength="11"
            />
        </div>

        {{-- Address --}}
        <div>
            <label class="label">
                <span class="label-text">Address</span>
            </label>
            <x-text-area name="address" placeholder="Enter Address">

            </x-text-area>
        </div>
    </form>
</x-modal>
