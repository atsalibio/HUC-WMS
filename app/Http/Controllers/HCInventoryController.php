<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HCInventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hcId = $user->HealthCenterID;
        $role = $user->Role;

        $query = DB::table('HCInventoryBatch as hb')
            ->join('Item as i', 'hb.ItemID', '=', 'i.ItemID')
            ->join('HealthCenters as hc', 'hb.HealthCenterID', '=', 'hc.HealthCenterID');

        // Enforce exclusivity for Health Center Staff
        if ($role === 'Health Center Staff' && $hcId) {
            $query->where('hb.HealthCenterID', $hcId);
        }

        $hc_inventory = $query->where('hb.QuantityOnHand', '>', 0)
            ->select('i.ItemName', 'i.ItemType', 'hb.BatchID', 'hb.QuantityOnHand', 'hb.ExpiryDate', 'hb.ItemID', 'hc.Name as HealthCenterName', 'hc.HealthCenterID')
            ->orderBy('hb.HealthCenterID')
            ->get();

        return view('pages.hc_inventory', compact('hc_inventory', 'hcId'));
    }
}
