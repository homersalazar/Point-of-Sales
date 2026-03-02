<x-modal
    id="add_unit"
    title="Add Unit"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addUnitForm'
        ]
    ]"
>
    <form action="{{ route('unit.create_unit') }}" method="POST" class="space-y-2" id="addUnitForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Unit Name</span>
            </label>
            <x-text-input
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
                name="abbreviation"
                placeholder="Enter Abbreviation (e.g., kg, pcs)"
                size="sm"
            />
        </div>
    </form>
</x-modal>
