<?php

namespace App\Services;

use App\Models\Procurement\Supplier;
use Carbon\Carbon;

class SupplierService
{
    public function getSuppliers(?string $search = null, string $sort = 'Name', string $direction = 'asc'){
        return Supplier::query()
            ->where('IsActive', true)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('Name', 'like', "%{$search}%")
                        ->orWhere('Address', 'like', "%{$search}%")
                        ->orWhere('ContactInfo', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->get();
    }

    public function createSupplier(array $data)
    {
        return Supplier::create([
            'Name' => $data['Name'],
            'Address' => $data['Address'] ?? null,
            'ContactInfo' => $data['ContactInfo'] ?? null,
        ]);
    }

    public function updateSupplier(int $id, array $data)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->update([
            'Name' => $data['Name'],
            'Address' => $data['Address'] ?? null,
            'ContactInfo' => $data['ContactInfo'] ?? null,
            'LastUpdated' => Carbon::now(),
        ]);

        return $supplier->fresh();
    }

    public function deleteSupplier(int $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['IsActive' => false, 'DeletedAt' => Carbon::now()]);

        return $supplier->fresh();
    }
}
