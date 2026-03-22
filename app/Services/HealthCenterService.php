<?php

namespace App\Services\System;

use App\Models\System\HealthCenter;
use App\Models\System\User;

class HealthCenterService
{
    public function getAll()
    {
        return HealthCenter::orderBy('Name')->get();
    }

    public function getName($id)
    {
        $hc = HealthCenter::find($id);
        return $hc ? $hc->Name : null;
    }

    public function switch(User $user, $healthCenterId = null)
    {
        if ($healthCenterId === 'none' || is_null($healthCenterId)) {
            $user->HealthCenterID = null;
        } else {
            $exists = HealthCenter::where('HealthCenterID', $healthCenterId)->exists();
            if (!$exists) return false;
            $user->HealthCenterID = $healthCenterId;
        }

        return $user->save();
    }
}