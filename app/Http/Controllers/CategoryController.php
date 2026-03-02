<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Category::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $categories = $this->categoryService->search($search, $perPage);
        } else {
            // No search, return all categories
            $categories = Category::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('category.partials.category_table', compact('categories'))->render();
        }

        return view('category.index', compact('categories', 'search', 'perPage'));
    }

    public function create_category(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        try {
            $this->categoryService->store($validated);
            return redirect()->back()->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_category(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
            'description' => 'nullable|string',
        ]);

        try {
            $category = $this->categoryService->show($id);

            $changes = [];
            foreach ($validated as $key => $value) {
                if ($category->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            if (!empty($changes)) {
                $this->categoryService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Category updated successfully.'
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

    public function delete_category($id)
    {
        try {
            $this->categoryService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Category deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
