<?php

namespace App\Http\Controllers;

use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = $this->supplierService->getSuppliers(
                $request->search,
                $request->sort ?? 'Name',
                $request->direction ?? 'asc'
            );

            return response()->json([
                'success' => true,
                'suppliers' => $suppliers
            ]);
        }

        return view('pages.suppliers');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:255',
            'Address' => 'nullable|string|max:1000',
            'ContactInfo' => 'nullable|string|max:255',
        ]);

        $supplier = $this->supplierService->createSupplier($validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully.',
            'supplier' => $supplier
        ]);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:255',
            'Address' => 'nullable|string|max:1000',
            'ContactInfo' => 'nullable|string|max:255',
            'LastUpdated' => 'nullable|date',
        ]);

        $supplier = $this->supplierService->updateSupplier($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully.',
            'supplier' => $supplier
        ]);
    }

    public function destroy(int $id)
    {
        $this->supplierService->deleteSupplier($id);

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully.'
        ]);
    }
}