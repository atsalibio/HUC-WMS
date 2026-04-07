@extends('layouts.app')

@section('content')
@php
    $stats = $stats ?? [
        'pending_reqs' => 0,
        'pending_pos' => 0,
        'low_stock' => 0,
        'pending_patient_reqs' => 0,
    ];
    $recentRequisitions = $recentRequisitions ?? collect();
    $user = $user ?? Auth::user();

    $userName = trim((optional($user)->FName ?? '') . ' ' . (optional($user)->LName ?? '')) ?: 'User';
    $activeRole = optional($user)->Role ?? 'N/A';
@endphp

<div class="space-y-8 animate-fade-in">
    <!-- Hero Section -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 text-white p-8 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight mb-2">Welcome Back, {{ $userName }}! 👋</h2>
                <p class="text-slate-300 font-medium max-w-lg">Manage your pharmacy operations with ease. Here's what's happening today in the <span class="text-teal-400 font-bold">{{ $activeRole }}</span> dashboard.</p>
            </div>
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-teal-500 px-5 py-3 text-sm font-black uppercase tracking-wider shadow-lg shadow-teal-500/30 hover:bg-teal-600" href="{{ route('page.show', ['page' => 'requisitions']) }}">Requisitions</a>
                <a class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-5 py-3 text-sm font-black uppercase tracking-wider hover:bg-white/20" href="{{ route('page.show', ['page' => 'procurement-orders']) }}">Procurement</a>
            </div>
        </div>

        <div class="absolute top-0 right-0 -mt-12 -mr-12 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Inventory Requisitions -->
        <div class="cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1" onclick="location.href='{{ route('page.show', ['page' => 'requisitions']) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 text-blue-600 flex items-center justify-center rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Inventory Requisitions</span>
            </div>
            <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_reqs'] }}</div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Requests</p>
        </div>

        <!-- Procurement Orders -->
        <div class="cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1" onclick="location.href='{{ route('page.show', ['page' => 'procurement-orders']) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-500/10 text-teal-600 flex items-center justify-center rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-[10px] font-black text-teal-500 uppercase tracking-widest">Procurement Orders</span>
            </div>
            <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_pos'] }}</div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending POs</p>
        </div>

        <!-- Low Stock -->
        <div class="cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1" onclick="location.href='{{ route('page.show', ['page' => 'inventory']) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Low Stock Batches</span>
            </div>
            <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['low_stock'] }}</div>
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Critical Alert</p>
        </div>

        <!-- Patient Requisitions -->
        <div class="cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1" onclick="location.href='{{ route('page.show', ['page' => 'patient_requisitions_hp']) }}'">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 text-purple-600 flex items-center justify-center rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Patient Requisitions</span>
            </div>
            <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_patient_reqs'] }}</div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Approvals</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Recent Requisitions -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Recent Requisitions</h3>
                    <p class="text-sm text-slate-500">Monitoring latest inventory requests</p>
                </div>
                <a href="{{ route('page.show', ['page' => 'requisitions']) }}" class="text-sm font-bold text-teal-600 hover:text-teal-700">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3">Req No.</th>
                            <th class="px-6 py-3">Health Center</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($recentRequisitions as $req)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">{{ $req->RequisitionNumber ?? $req->id }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $req->healthCenter->Name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $req->StatusType === 'Pending' ? 'bg-amber-100 text-amber-700' : ($req->StatusType === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700')}}">{{ $req->StatusType ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-slate-500">No requests today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pendingPOs->count() > 0)
        <!-- Pending Procurement Orders -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden animate-fade-in">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Procurement Queue</h3>
                    </div>
                    <p class="text-sm text-slate-500">Orders awaiting approval</p>
                </div>
                <a href="{{ route('page.show', ['page' => 'procurement-orders']) }}" class="text-sm font-bold text-teal-600 hover:text-teal-700">Process All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3">PO Number</th>
                            <th class="px-6 py-3">Supplier</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($pendingPOs as $po)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer" onclick="location.href='{{ route('page.show', ['page' => 'procurement-orders']) }}'">
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">{{ $po->PONumber }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $po->supplier->Name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ optional($po->PODate)->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($pendingPatientReqs->count() > 0)
        <!-- Pending Patient Approvals -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden animate-fade-in">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Patient Verification</h3>
                    </div>
                    <p class="text-sm text-slate-500">Prescriptions awaiting pharmacist verification</p>
                </div>
                <a href="{{ route('page.show', ['page' => 'patient_requisitions_hp']) }}" class="text-sm font-bold text-rose-600 hover:text-rose-700">Verify Now →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Health Center</th>
                            <th class="px-6 py-3">Ref No.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($pendingPatientReqs as $pReq)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer" onclick="location.href='{{ route('page.show', ['page' => 'patient_requisitions_hp']) }}'">
                                <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">{{ $pReq->patient->FName }} {{ $pReq->patient->LName }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">{{ $pReq->healthCenter->Name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $pReq->RequisitionNumber }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection