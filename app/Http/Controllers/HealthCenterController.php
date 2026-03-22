<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HealthCenterService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class HealthCenterController extends Controller
{
    protected $healthCenterService;
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function dashboard()
    {
        $user = $this->authService->getUser();
        $currentPage = 'dashboard';

        return view('healthcenter.dashboard', compact('user', 'currentPage'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'healthCenterId' => 'nullable|string', 
        ]);

        $user = Auth::user();
        $hcId = $request->input('healthCenterId');

        $success = $this->healthCenterService->switch($user, $hcId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Health center updated.' : 'Invalid health center ID.'
        ]);
    }

    public function index()
    {
        $centers = $this->healthCenterService->getAll();
        return response()->json($centers);
    }
}