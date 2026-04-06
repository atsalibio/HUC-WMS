<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\System\User;
use App\Models\System\TransactionLog;
use App\Models\System\SecurityLog;

class SettingsController extends Controller
{
    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'FName' => 'required|string|max:255',
            'MName' => 'nullable|string|max:255',
            'LName' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        /** @var User $user */
        $user->update([
            'FName' => $request->FName,
            'MName' => $request->MName,
            'LName' => $request->LName,
        ]);

        SecurityLog::create([
            'UserID' => $user->UserID,
            'ActionType' => 'Profile Updated',
            'ActionDescription' => 'User updated their personal profile details in settings.',
            'ModuleAffected' => 'Account Settings',
            'IPAddress' => $request->ip(),
            'ActionDate' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->Password)) {
            return response()->json(['success' => false, 'message' => 'Current password does not match.'], 422);
        }

        /** @var User $user */
        $user->update([
            'Password' => $request->newPassword,
        ]);

        SecurityLog::create([
            'UserID' => $user->UserID,
            'ActionType' => 'Password Changed',
            'ActionDescription' => 'User successfully changed their account password in security settings.',
            'ModuleAffected' => 'Security Settings',
            'IPAddress' => $request->ip(),
            'ActionDate' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }

    /**
     * Get logs for the settings activity tab.
     */
    public function getLogs()
    {
        $user = Auth::user();
        
        $securityLogs = SecurityLog::where('UserID', $user->UserID)
            ->whereIn('ActionType', ['Login', 'Logout', 'Profile Updated', 'Password Changed'])
            ->latest('ActionDate')
            ->limit(20)
            ->get();
            
        $transactionLogs = TransactionLog::where('UserID', $user->UserID)
            ->latest('ActionDate')
            ->limit(20)
            ->get();

        return response()->json([
            'security' => $securityLogs,
            'transaction' => $transactionLogs
        ]);
    }
}
