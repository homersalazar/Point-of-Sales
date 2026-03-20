<x-modal
    id="update_supplier_modal"
    title="Update Supplier"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateSupplierForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateSupplierForm">
        @csrf
        @method('PUT')
        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Name</span>
            </label>
            <x-text-input
                id="name"
                name="name"
                size="sm"
                placeholder="Enter Supplier name"
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
                id="email"
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
                id="phone"
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
            <x-text-area id="address" name="address" placeholder="Enter Address">

            </x-text-area>
        </div>
    </form>
</x-modal>
