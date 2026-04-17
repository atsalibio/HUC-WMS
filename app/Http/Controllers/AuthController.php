<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($request->username, $request->password);

        if ($request->expectsJson() || $request->ajax()) {
            if (isset($result['error'])) {
                // Return a 401 Unauthorized or 422 Unprocessable Entity
                // We'll just return success: false with the error string
                $errorMsg = is_array($result['error']) ? implode(' ', $result['error']) : (is_string($result['error']) ? $result['error'] : 'Invalid credentials.');
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 401);
            }

            return response()->json([
                'success' => true,
                'redirect' => route($this->authService->getRedirectRoute())
            ]);
        }

        if (isset($result['error'])) {
            return back()->withErrors($result['error']);
        }

        return redirect()->route($this->authService->getRedirectRoute());
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $healthCenters = DB::table('HealthCenters')->get();
        return view('auth.register', compact('healthCenters'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100|unique:Users,Username',
            'password' => 'required|string|min:6|confirmed',
            'FName' => 'required|string|max:100',
            'MName' => 'nullable|string|max:100',
            'LName' => 'required|string|max:100',
            'Role' => 'required|string',
            'HealthCenterID' => 'nullable|integer|exists:HealthCenters,HealthCenterID',
        ]);

        $data = $request->only(['username', 'password', 'FName', 'MName', 'LName', 'Role', 'HealthCenterID']);
        $data['Username'] = $data['username'];
        $data['Password'] = $data['password'];

        $this->authService->register($data);

        return redirect()->route('login.show')->with('success', 'Account created successfully.');
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('login.show');
    }
}