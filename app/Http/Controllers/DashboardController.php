<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $data = $this->dashboardService->getDashboardData();

        $view = 'dashboard';

        $roleMap = [
            'Administrator' => 'admin',
            'Health Center Staff' => 'health',
            'Head Pharmacist' => 'pharmacist',
            'Warehouse Staff' => 'warehouse',
            'Accounting Office User' => 'accounting',
            'CMO/GSO/COA User' => 'cmo'
        ];

        $roleSlug = $roleMap[$data['role']] ?? strtolower(explode(' ', $data['role'])[0]);

        if (view()->exists($roleSlug . '.dashboard')) {
            $view = $roleSlug . '.dashboard';
        }

        return view($view, $data)->with('currentPage', 'dashboard');
    }
}