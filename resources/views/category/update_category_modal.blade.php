<x-modal
    id="update_category_modal"
    title="Update Category"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateCategoryForm'
        ]
    ]"
>
    <form method="POST" class="space-y-2" id="updateCategoryForm">
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
                id="update_description"
                name="description"
                placeholder="Enter Description"
                size="sm"
            />
        </div>
    </form>
</x-modal>
