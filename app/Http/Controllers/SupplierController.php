<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Supplier::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $suppliers = $this->supplierService->search($search, $perPage);
        } else {
            // No search, return all suppliers
            $suppliers = Supplier::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('supplier.partials.supplier_table', compact('suppliers'))->render();
        }

        return view('supplier.index', compact('suppliers', 'search', 'perPage'));
    }

    public function create_supplier(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email',
            'phone'   => ['nullable', 'regex:/^(09|\+639|639)\d{9}$/'],
            'address' => 'nullable|string'
        ]);
        try {
            $this->supplierService->store($validated);
            return redirect()->back()->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_supplier(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($id),
            ],
            'phone'     => ['nullable', 'regex:/^(09|\+639|639)\d{9}$/'],
            'address' => 'nullable|string',
        ]);

        try {
            $supplier = $this->supplierService->show($id);

            // Fill new values (does NOT save yet)
            $supplier->fill($validated);

            // Check if anything changed
            if ($supplier->isDirty()) {

                $supplier->save();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Supplier updated successfully.'
                ], 200);
            }

            return response()->json([
                'status'  => 'info',
                'message' => 'No changes were made.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function delete_supplier($id)
    {
        try {
            $this->supplierService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Supplier deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
