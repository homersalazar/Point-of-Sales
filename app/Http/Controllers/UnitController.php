<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Handle "All"
        if ($perPage == -1) {
            $perPage = Unit::count();
        }

        // Only filter if search is not empty
        if (!empty($search)) {
            $units = $this->unitService->search($search, $perPage);
        } else {
            // No search, return all units
            $units = Unit::orderBy('name', 'asc')->paginate($perPage);
        }

        if ($request->ajax()) {
            return view('unit.partials.unit_table', compact('units'))->render();
        }

        return view('unit.index', compact('units', 'search', 'perPage'));
    }

    public function create_unit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'abbreviation' => 'nullable|string|max:10|unique:units,abbreviation',
        ]);

        try {
            $this->unitService->store($validated);
            return redirect()->back()->with('success', 'Unit created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }

    public function update_unit(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')->ignore($id), // ✅ ignore current unit
            ],
            'abbreviation' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('units', 'abbreviation')->ignore($id), // ✅ prevent duplicate abbreviation
            ],
        ]);
        try {
            $category = $this->unitService->show($id);

            $changes = [];
            foreach ($validated as $key => $value) {
                if ($category->$key != $value) {
                    $changes[$key] = $value;
                }
            }

            if (!empty($changes)) {
                $this->unitService->update($changes, $id);
                $response = [
                    'status' => 'success',
                    'message' => 'Unit updated successfully.'
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

    public function delete_unit($id)
    {
        try {
            $this->unitService->delete($id);
            $response = [
                'status' => 'success',
                'message' => 'Unit deleted successfully.'
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Something went wrong. Please try again later.'
            ]);
        }
    }
}
