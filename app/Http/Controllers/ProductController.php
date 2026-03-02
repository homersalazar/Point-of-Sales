<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Product::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $products = $this->productService->search($search, $perPage);
        } else {
            // No search, return all products
            $products = Product::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('product.partials.product_table', compact('products'))->render();
        }

        $categories = $this->categoryService->getAll();

        return view('product.index', compact('products', 'search', 'perPage', 'categories'));
    }

    public function create_product(Request $request)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name'), // ✅ prevents duplicate names
            ],
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'image'         => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $filename = time() . '.' . $uploadedFile->extension(); // unique filename
                $path = 'public/product/';

                $uploadedFile->storeAs($path, $filename);

                // Add filename to validated data
                $validated['image'] = $filename;
            }
            $this->productService->store($validated);

            return redirect()->back()->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_product(Request $request, $id)
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'name'          => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($id), // ✅ ignore current product
            ],
            'cost_price'    => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // optional for update
        ]);

        try {
            $product = $this->productService->show($id);

            $changes = [];

            // Check for changes in text/number fields
            foreach ($validated as $key => $value) {
                if ($key === 'image') continue; // skip image for now
                if ($product->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $filename = time() . '.' . $uploadedFile->extension();
                $path = 'public/product/';
                $uploadedFile->storeAs($path, $filename);

                $changes['image'] = $filename; // add image to changes
            }

            if (!empty($changes)) {
                $this->productService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Product updated successfully.'
                ];
            } else {
                $response = [
                    'status' => 'info',
                    'message' => 'No changes were made.'
                ];
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function delete_product($id)
    {
        try {
            $this->productService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Product deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
