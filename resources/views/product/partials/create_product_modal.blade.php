<x-modal
    id="add_product"
    title="Add Product"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'addProductForm'
        ]
    ]"
>
    <form action="{{ route('product.create_product') }}" method="POST" enctype="multipart/form-data" class="space-y-2" id="addProductForm">
        @csrf

        {{-- Category --}}
        <div>
            <label class="label">
                <span class="label-text">Category</span>
            </label>
            <x-select name="category_id" size="sm" caption="Select Category" >
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
                name="image"
                size="sm"
            />
        </div>
    </form>
</x-modal>
