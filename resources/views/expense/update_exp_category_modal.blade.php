<x-modal
    id="update_exp_category_modal"
    title="Update expense Category"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateExpenseCategoryForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateExpenseCategoryForm">
        @csrf
        @method('PUT')
        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Category Name</span>
            </label>
            <x-text-input
                id="update_name"
                name="name"
                size="sm"
                placeholder="Enter Expense Category name"
                required
            />
        </div>
    </form>
</x-modal>
