<x-modal
    id="add_exp_category"
    title="Add Expense Category"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addExpenseCategoryForm'
        ]
    ]"
>
    <form action="{{ route('expense.create_exp_category') }}" method="POST" class="space-y-2" id="addExpenseCategoryForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Expense Category Name</span>
            </label>
            <x-text-input
                name="name"
                size="sm"
                placeholder="Enter Expense Category name"
                required
            />
        </div>
    </form>
</x-modal>
