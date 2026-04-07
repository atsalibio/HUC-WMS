<?php

namespace App\Http\Controllers;

use App\Models\Procurement\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'Name');
        $direction = $request->get('direction', 'asc');

        $query = Supplier::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('Name', 'LIKE', "%{$search}%")
                  ->orWhere('Address', 'LIKE', "%{$search}%")
                  ->orWhere('ContactInfo', 'LIKE', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy($sort, $direction)->get();

        if ($request->ajax()) {
            return response()->json(['suppliers' => $suppliers]);
        }

        return view('pages.suppliers', [
            'suppliers' => $suppliers,
            'currentPage' => 'suppliers'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Name' => 'required|string|max:200',
            'Address' => 'nullable|string',
            'ContactInfo' => 'nullable|string|max:200',
        ]);

        try {
            $supplier = Supplier::create($data);
            return response()->json(['success' => true, 'supplier' => $supplier]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'Name' => 'required|string|max:200',
            'Address' => 'nullable|string',
            'ContactInfo' => 'nullable|string|max:200',
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update($data);
            return response()->json(['success' => true, 'supplier' => $supplier]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
