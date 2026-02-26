<x-modal
    id="update_expense_modal"
    title="Update Expense"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateExpenseForm'
        ]
    ]"
>
    <form method="POST" enctype="multipart/form-data" class="space-y-2" id="updateExpenseForm">
        @csrf
        @method('PUT')

        {{-- Category --}}
        <div>
            <label class="label">
                <span class="label-text">Expense Category</span>
            </label>
            <x-select id="expense_category_id" name="expense_category_id" size="sm" caption="Select Expense Category" >
                @foreach ($expense_categories as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </x-select>
        </div>

        {{-- Amount --}}
        <div>
            <label class="label">
                <span class="label-text">Amount</span>
            </label>
            <x-text-input
                id="amount"
                name="amount"
                type="number"
                step="0.01"
                placeholder="0.00"
                size="sm"
                required
            />
        </div>

        {{-- Expense Date --}}
        <div>
            <label class="label">
                <span class="label-text">Expense Date</span>
            </label>
            <x-text-input
                id="expense_date"
                type="date"
                name="expense_date"
                size="sm"
                required
            />
        </div>

        {{-- Description --}}
        <div>
            <label class="label">
                <span class="label-text">Description</span>
            </label>
            <x-text-area id="description" name="description" placeholder="Enter Description">

            </x-text-area>
        </div>

    </form>
</x-modal>
