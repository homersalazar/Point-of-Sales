<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        if ($perPage == -1) {
            $perPage = Expense::count();
        }

        $expenses = $this->expenseService->paginate($search, $perPage);

        if ($request->ajax()) {
            return view('expense.partials.expense_table', compact('expenses'))->render();
        }

        $expense_categories = $this->expenseCategoryService->getAll();

        return view('expense.index', compact('expenses', 'search', 'perPage', 'expense_categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string'
        ]);

        try {
            $validated['created_by'] = 1;
            $this->expenseService->store($validated);
            return redirect()->back()->with('success', 'Expense created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_status(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:cancelled,completed',
        ]);

        $result = $this->expenseService->updateStatus($id, $request->action);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string'
        ]);

        try {
            $this->expenseService->update($validated, $id);
            return response()->json([
                'status' => 'success',
                'message' => 'Expense updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function delete_expense($id)
    {
        try {
            $this->expenseService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Expense deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    // Expense Category -----------------------------------------------------------------------------------------------------
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
