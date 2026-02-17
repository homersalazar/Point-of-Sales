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
            $categories = Category::paginate($perPage);
        }

        if ($request->ajax()) {
            return view('category.partials.category_table', compact('categories'))->render();
        }

        return view('category.index', compact('categories', 'search', 'perPage'));
    }

    public function create_category(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
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
}
