<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\System\HealthCenter;

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

        $hc_inventory = $query
            ->where('hb.QuantityOnHand', '>', 0)
            ->select(
                'hb.HCBatchID',
                'hb.BatchID',
                'hb.ItemID',
                'hb.HealthCenterID',
                'hb.QuantityOnHand',
                'hb.UnitCost',
                'hb.DateReceivedAtHC',
                'hb.LotNumber',
                'i.ItemName',
                'i.ItemType',
                'i.UnitOfMeasure',
                'hc.Name as HealthCenterName'
            )
            ->orderBy('hb.HealthCenterID')
            ->get();

        $healthCenters = HealthCenter::all();
        $userRole = $role;

        return view('pages.hc_inventory', compact('hc_inventory', 'hcId', 'healthCenters', 'userRole'));
    }
}
