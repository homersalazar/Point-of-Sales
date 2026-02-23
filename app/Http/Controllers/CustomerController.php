<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Customer::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $customers = $this->customerService->search($search, $perPage);
        } else {
            // No search, return all customers
            $customers = Customer::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('customer.partials.customer_table', compact('customers'))->render();
        }

        return view('customer.index', compact('customers', 'search', 'perPage'));
    }

    public function create_customer(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone'   => ['nullable', 'regex:/^(09|\+639|639)\d{9}$/'],
            'address' => 'nullable|string'
        ]);
        try {
            $this->customerService->store($validated);
            return redirect()->back()->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }


    public function update_customer(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($id),
            ],
            'phone'     => ['nullable', 'regex:/^(09|\+639|639)\d{9}$/'],
            'address' => 'nullable|string',
        ]);

        try {
            $customer = $this->customerService->show($id);

            // Fill new values (does NOT save yet)
            $customer->fill($validated);

            // Check if anything changed
            if ($customer->isDirty()) {

                $customer->save();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Customer updated successfully.'
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


    public function delete_customer($id)
    {
        try {
            $this->customerService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Customer deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
