<x-modal
    id="add_category"
    title="Add Category"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addCategoryForm'
        ]
    ]"
>
    <form action="{{ route('category.create_category') }}" method="POST" class="space-y-2" id="addCategoryForm">
        @csrf

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Category Name</span>
            </label>
            <x-text-input
                name="name"
                size="sm"
                placeholder="Enter Category name"
                required
            />
        </div>

        {{-- Description --}}
        <div>
            <label class="label">
                <span class="label-text">Description</span>
            </label>
            <x-text-input
                name="description"
                placeholder="Enter Description"
                size="sm"
                required
            />
        </div>
    </form>
</x-modal>
