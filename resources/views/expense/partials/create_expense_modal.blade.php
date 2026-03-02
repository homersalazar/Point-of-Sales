<x-modal
    id="add_expense"
    title="Add Expense"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addExpenseForm'
        ]
    ]"
>
    <form action="{{ route('expense.create_expense') }}" method="POST" class="space-y-2" id="addExpenseForm">
        @csrf

        {{-- Category --}}
        <div>
            <label class="label">
                <span class="label-text">Expense Category</span>
            </label>
            <x-select name="expense_category_id" size="sm" caption="Select Expense Category" >
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
            <x-text-area name="description" placeholder="Enter Description">

            </x-text-area>
        </div>

    </form>
</x-modal>
