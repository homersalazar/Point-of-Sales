<?php

namespace App\Http\Controllers;

use App\Models\Expense_category;
use App\Services\ExpenseCategoryService;
use App\Services\ExpenseService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $expenseService;
    protected $expenseCategoryService;

    public function __construct(
        ExpenseService $expenseService,
        ExpenseCategoryService $expenseCategoryService
    ) {
        $this->expenseService = $expenseService;
        $this->expenseCategoryService = $expenseCategoryService;
    }

    // Expense Category
    public function exp_category(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Expense_category::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $exp_categories = $this->expenseCategoryService->search($search, $perPage);
        } else {
            // No search, return all exp_categories
            $exp_categories = Expense_category::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('expense.partials.expense_category_table', compact('exp_categories'))->render();
        }

        return view('expense.exp_category', compact('exp_categories', 'search', 'perPage'));
    }

    public function create_exp_category(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        try {
            $this->expenseCategoryService->store($validated);
            return redirect()->back()->with('success', 'Expense Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_exp_category(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
        ]);

        try {
            $category = $this->expenseCategoryService->show($id);

            $changes = [];
            foreach ($validated as $key => $value) {
                if ($category->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            if (!empty($changes)) {
                $this->expenseCategoryService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Expense Category updated successfully.'
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

    public function delete_expense_category($id)
    {
        try {
            $this->expenseCategoryService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Expense Category deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
