<?php

namespace App\Services;

use App\Models\System\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\System\SecurityLog;
use Illuminate\Support\Facades\Request;

class AuthService
{
    public function getUser()
    {
        return Auth::user();
    }
    public function register(array $data)
    {
        return User::create($data);
    }

    public function login($username, $password)
    {
        $user = User::where('Username', $username)->first();
        if (!$user) {
            return ['error' => 'User not found'];
        }

        // 1. Try standard Bcrypt check
        $isValid = false;
        try {
            if (Hash::check($password, $user->Password)) {
                $isValid = true;
            }
        } catch (\Exception $e) {
            // Hash::check might throw an exception if the format is fundamentally wrong
        }

        // 2. Try legacy SHA-256 check if Bcrypt failed
        if (!$isValid) {
            $legacyHash = hash('sha256', $password);
            if ($user->Password === $legacyHash) {
                $isValid = true;
                
                // Upgrade to Bcrypt for future logins
                $user->Password = $password; // This triggers the setPasswordAttribute mutator (Bcrypt)
                $user->save();
            }
        }

        if (!$isValid) {
            return ['error' => 'Invalid password'];
        }

        Auth::login($user);
        
        SecurityLog::create([
            'UserID' => $user->UserID,
            'ActionType' => 'Login',
            'ActionDescription' => 'User logged in successfully via web gateway.',
            'ModuleAffected' => 'Authentication',
            'IPAddress' => Request::ip(),
            'ActionDate' => now()
        ]);

        return ['success' => true, 'user' => $user];
    }

    public function getRedirectRoute()
    {
        $user = Auth::user();

        if (!$user) {
            return 'login.show';
        }

        return match ($user->Role) {
            'Administrator' => 'admin.dashboard',
            'Head Pharmacist' => 'pharmacist.dashboard',
            'Health Center Staff' => 'health.dashboard',
            'Warehouse Staff' => 'warehouse.dashboard',
            'Accounting Office User' => 'accounting.dashboard',
            'CMO/GSO/COA User' => 'cmo.dashboard',
            default => 'dashboard',
        };
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            SecurityLog::create([
                'UserID' => $user->UserID,
                'ActionType' => 'Logout',
                'ActionDescription' => 'User session terminated successfully.',
                'ModuleAffected' => 'Authentication',
                'IPAddress' => Request::ip(),
                'ActionDate' => now()
            ]);
        }
        Auth::logout();
    }
}