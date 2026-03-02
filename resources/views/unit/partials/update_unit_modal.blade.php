<x-modal
    id="update_unit_modal"
    title="Update Unit"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateUnitForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateUnitForm">
        @csrf
        @method('PUT')
        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Unit Name</span>
            </label>
            <x-text-input
                id="update_name"
                name="name"
                size="sm"
                placeholder="Enter Unit name"
                required
            />
        </div>

        {{-- Abbreviation --}}
        <div>
            <label class="label">
                <span class="label-text">Abbreviation</span>
            </label>
            <x-text-input
                id="update_abbreviation"
                name="abbreviation"
                size="sm"
                placeholder="Enter Abbreviation"
            />
        </div>
    </form>
</x-modal>
