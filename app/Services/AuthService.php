<?php

namespace App\Services;

use App\Models\System\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

        if (!Hash::check($password, $user->Password)) {
            return ['error' => 'Invalid password'];
        }

        Auth::login($user);

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
        Auth::logout();
    }
}