<x-modal
    id="update_product_modal"
    title="Update Product"
    :buttons="[
        [
            'label' => 'Update',
            'type' => 'submit',
            'color' => 'warning',
            'form' => 'updateProductForm'
        ]
    ]"
>
    <form method="POST" enctype="multipart/form-data" class="space-y-2" id="updateProductForm">
        @csrf
        @method('PUT')
        <div class="flex justify-center">
            <img id="current_image" class="mask mask-squircle h-24 w-24" src="" alt="No image">

        </div>

        {{-- Category --}}
        <div>
            <label class="label">
                <span class="label-text">Category</span>
            </label>
            <x-select id="category_id" name="category_id" size="sm" >
                @foreach ($categories as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </x-select>
        </div>

        {{-- Name --}}
        <div>
            <label class="label">
                <span class="label-text">Product Name</span>
            </label>
            <x-text-input
                id="name"
                name="name"
                size="sm"
                placeholder="Enter product name"
                required
            />
        </div>

        {{-- Selling Price --}}
        <div>
            <label class="label">
                <span class="label-text">Selling Price</span>
            </label>
            <x-text-input
                id="selling_price"
                name="selling_price"
                type="number"
                step="0.01"
                placeholder="0.00"
                size="sm"
                required
            />
        </div>

        {{-- Image --}}
        <div>
            <label class="label">
                <span class="label-text">Product Image</span>
            </label>
            <x-file-input
                id="image"
                name="image"
                size="sm"
            />
        </div>

    </form>
</x-modal>
