@extends('layouts.app')

@section('content')
<div x-data="dashboardManager()" x-init="init()">
    @php
        $stats = $stats ?? [];
        $recentRequisitions = $recentRequisitions ?? collect();
        $pendingPOs = $pendingPOs ?? collect();
        $pendingPatientReqs = $pendingPatientReqs ?? collect();

        $user = $user ?? Auth::user();

        $userName = $ui['userName'];
        $activeRole = $ui['activeRole'];

        $badgeClass = $ui['badgeClass'];
        $pulseClass = $ui['pulseClass'];
        $btnClass = $ui['btnClass'];
        $blur1 = $ui['blur1'];
        $blur2 = $ui['blur2'];
    @endphp

    <div class="space-y-10 animate-fade-in px-2">
        <!-- Hero Section -->
        <div class="relative overflow-hidden rounded-[3rem] text-white p-12 shadow-2xl"
            style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e3a8a 100%);">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-[0.2em] mb-4 {{ $badgeClass }}">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse {{ $pulseClass }}"></span>
                        {{ $activeRole }} Portal
                    </div>
                    <h2 class="text-4xl font-extrabold tracking-tight mb-3 text-white">Welcome Back, {{ $userName }}! 👋
                    </h2>
                    <p class="text-blue-100/90 font-medium max-w-xl text-sm leading-relaxed">
                        @if($activeRole === 'Health Center Staff')
                            Manage your station's requisitions and track deliveries. Your active operations are highlighted
                            below. Keep inventory perfectly aligned with local demand.
                        @elseif($activeRole === 'Head Pharmacist')
                            Verify patient prescriptions, manage health center supply approvals, and oversee clinical
                            dispensing. Ensure critical medicine flows safely.
                        @else
                            Oversee central warehouse operations, review procurement orders, and monitor overall movement.
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a class="inline-flex items-center justify-center gap-2 rounded-[1.5rem] px-8 py-4 text-[10px] font-black uppercase tracking-widest shadow-xl transition-all active:scale-95 text-white {{ $btnClass }}"
                        href="{{ route('page.show', ['page' => 'requisitions']) }}">Manage Requisitions</a>
                    @if($activeRole !== 'Health Center Staff')
                        <a class="inline-flex items-center justify-center gap-2 rounded-[1.5rem] bg-indigo-500 px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-400 transition-all border border-indigo-400/30 text-white"
                            href="{{ route('page.show', ['page' => 'procurement-orders']) }}">Procurement</a>
                    @endif
                </div>
            </div>

            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-80 h-80 rounded-full blur-3xl {{ $blur1 }}"></div>
            <div class="absolute bottom-0 left-0 -mb-16 -ml-16 w-64 h-64 rounded-full blur-3xl {{ $blur2 }}"></div>

            <!-- Decorative Pattern Overlay -->
            <div
                class="absolute inset-0 z-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)]">
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="flex flex-col md:flex-row w-full gap-6">
            <!-- Inventory Requisitions -->
            <div class="flex-1 cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1"
                onclick="location.href='{{ route('page.show', ['page' => 'requisitions']) }}'">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 text-blue-600 flex items-center justify-center rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Inventory
                        Requisitions</span>
                </div>
                <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_reqs'] ?? 0 }}</div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Requests</p>
            </div>

            <!-- Procurement Orders -->
            @if($activeRole !== 'Health Center Staff')
                <div class="flex-1 cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1"
                    onclick="location.href='{{ route('page.show', ['page' => 'procurement-orders']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 flex items-center justify-center rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Procurement Orders</span>
                    </div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_pos'] ?? 0 }}</div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending POs</p>
                </div>

                <!-- Low Stock -->
                <div class="flex-1 cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1"
                    onclick="location.href='{{ route('page.show', ['page' => 'inventory']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Low
                            Stock Batches</span>
                    </div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['low_stock'] ?? 0 }}</div>
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Critical Alert</p>
                </div>
            @endif

            <!-- Patient Requisitions -->
            @if(!in_array($activeRole, ['Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User']))
                <div class="flex-1 cursor-pointer rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 shadow-sm transition-all hover:shadow-xl hover:-translate-y-1"
                    onclick="location.href='{{ route('page.show', ['page' => 'patient_requisitions_hp']) }}'">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-purple-50 dark:bg-purple-500/10 text-purple-600 flex items-center justify-center rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span
                            class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Patient
                            Requisitions</span>
                    </div>
                    <div class="text-4xl font-black text-slate-900 dark:text-white mb-1">{{ $stats['pending_patient_reqs'] ?? 0 }}
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending Approvals</p>
                </div>
            @endif
        </div>

        @if($activeRole !== 'Health Center Staff')
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-2">
                        Critical Inventory Pressure
                    </p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                        Insufficient Inventory Coverage
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                        Items below do not have enough inventory on hand to fulfill approved and pending requisitions.
                    </p>
                </div>

                @if(isset($criticalLowStockItems) && count($criticalLowStockItems) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase tracking-wide text-[10px] font-black">
                                <tr>
                                    <th class="px-6 py-4">Item</th>
                                    <th class="px-6 py-4">On Hand</th>
                                    <th class="px-6 py-4">Required</th>
                                    <th class="px-6 py-4">Shortage</th>
                                    <th class="px-6 py-4">Incoming PO</th>
                                    <th class="px-6 py-4">Remaining Gap</th>
                                    <th class="px-6 py-4">Coverage</th>
                                    <th class="px-6 py-4">Status</th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($criticalLowStockItems as $item)
                                    @php
                                        $required = $item['required_quantity'];
                                        $available = $item['inventory_on_hand'];
                                        $shortage = max($required - $available, 0);
                                        $incomingPO = $item['incoming_po_quantity'] ?? 0;
                                        $remainingShortage = max($shortage - $incomingPO, 0);

                                        $percentage = $required > 0
                                            ? min((($available + $incomingPO) / $required) * 100, 100)
                                            : 0;

                                        if ($incomingPO <= 0) {
                                            $severityLabel = 'Critical';
                                            $severityClass = 'bg-rose-100 text-rose-700';
                                        } elseif ($incomingPO <= $shortage) {
                                            $severityLabel = 'Urgent';
                                            $severityClass = 'bg-amber-100 text-amber-700';
                                        } else {
                                            $severityLabel = 'Low';
                                            $severityClass = 'bg-green-100 text-green-700';
                                        }
                                    @endphp

                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                        <td class="px-6 py-5">
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-white">
                                                    {{ $item['item_name'] }}
                                                </p>
                                                <p class="text-[10px] uppercase tracking-widest font-black text-slate-400 mt-1">
                                                    Inventory Coverage Review
                                                </p>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-300">
                                            {{ $available }}
                                        </td>

                                        <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-300">
                                            {{ $required }}
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-widest">
                                                {{ $shortage }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 font-bold text-slate-700 dark:text-slate-300">
                                            {{ $incomingPO }}
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="px-3 py-1 rounded-full {{ $remainingShortage > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-black uppercase tracking-widest">
                                                {{ $remainingShortage }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-5 min-w-[220px]">
                                            <div class="space-y-2">
                                                <div class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden" >
                                                    <div
                                                        class="h-full rounded-full bg-gradient-to-r from-rose-500 via-amber-500 to-emerald-500"
                                                        style="width: 100%"
                                                    ></div>
                                                </div>


                                                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                                                    <span>0%</span>
                                                    <span>{{ round($percentage) }}%</span>
                                                    <span>100%</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-5">
                                            <span class="px-3 py-1 rounded-full {{ $severityClass }} text-[10px] font-black uppercase tracking-widest">
                                                {{ $severityLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-10 text-center">
                        <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mb-2">
                            No Critical Shortages
                        </p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Current inventory can satisfy all pending and approved demand.
                        </p>
                    </div>
                @endif
            </div>
        @endif

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700">
                <p class="text-[10px] font-black text-cyan-500 uppercase tracking-[0.3em] mb-2">
                    Seasonal Demand Analysis
                </p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">
                    Seasonal Medicine Demand Spikes
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                    Tracks medicines with seasonal demand increases throughout the year.
                </p>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 px-8 py-6 border-b border-slate-200 dark:border-slate-700">
                <form method="GET" class="flex items-center gap-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        Year
                    </label>

                    <select
                        name="trend_year"
                        onchange="this.form.submit()"
                        class="px-5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-4 focus:ring-cyan-500/10"
                    >
                        @foreach($availableTrendYears as $year)
                            <option value="{{ $year }}" {{ $selectedTrendYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="p-8">
                <div class="h-[420px]">
                    <canvas id="seasonalDemandChart"></canvas>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('seasonalDemandChart');

                if (!ctx) return;

                const labels = @json($seasonalDemandMonths);

                const datasets = @json($seasonalDemandDatasets).map(item => ({
                    label: item.label,
                    data: item.data,
                    borderColor: item.color,
                    backgroundColor: item.color,
                    fill: false,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3
                }));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#64748b',
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw} requests`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#64748b',
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.15)'
                                }
                            }
                        }
                    }
                });
            });
        </script>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Recent Requisitions -->
            <div
                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Health Center Requisitions</h3>
                        <p class="text-sm text-slate-500">Monitoring latest inventory requests</p>
                    </div>
                    <a href="{{ route('page.show', ['page' => 'requisitions']) }}"
                        class="text-sm font-bold text-blue-600 hover:text-blue-700">View All ?</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead
                            class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                            <tr>
                                <th class="px-6 py-3">Req No.</th>
                                <th class="px-6 py-3">Health Center</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($recentRequisitions as $req)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer"
                                    data-req="{{ json_encode($req, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG) }}"
                                    @click="openDetailsModal(JSON.parse($el.dataset.req))">
                                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                        {{ $req->RequisitionNumber ?? $req->id }}</td>
                                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $req->healthCenter->Name ?? 'Unknown' }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-black {{ $req->StatusType === 'Pending' ? 'bg-amber-100 text-amber-700' : ($req->StatusType === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700')}}">{{ $req->StatusType ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-500">No requests today.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(isset($pendingPOs) && $pendingPOs->count() > 0)
                <!-- Pending Procurement Orders -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden animate-fade-in">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Procurement Queue</h3>
                            </div>
                            <p class="text-sm text-slate-500">Orders awaiting approval</p>
                        </div>
                        <a href="{{ route('page.show', ['page' => 'procurement-orders']) }}"
                            class="text-sm font-bold text-blue-600 hover:text-blue-700">Process All ?</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead
                                class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                                <tr>
                                    <th class="px-6 py-3">PO Number</th>
                                    <th class="px-6 py-3">Supplier</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($pendingPOs as $po)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer"
                                        onclick="location.href='{{ route('page.show', ['page' => 'procurement-orders']) }}'">
                                        <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">{{ $po->PONumber }}</td>
                                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                            {{ $po->supplier->Name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                                            {{ optional($po->PODate)->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(in_array($activeRole, ['Administrator', 'Head Pharmacist', 'Health Center Staff']))
                <!-- Recent Patient Requisitions -->
                <div
                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden animate-fade-in">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $activeRole === 'Health Center Staff' ? 'Patient Requisitions' : 'Patient Verification' }}</h3>
                            </div>
                            <p class="text-sm text-slate-500">{{ $activeRole === 'Health Center Staff' ? 'Latest dispensation records' : 'Prescriptions awaiting pharmacist verification' }}</p>
                        </div>
                        <a href="{{ route('page.show', ['page' => $activeRole === 'Health Center Staff' ? 'hc_patient_requisitions' : 'patient_requisitions_hp']) }}"
                            class="text-sm font-bold text-rose-600 hover:text-rose-700">{{ $activeRole === 'Health Center Staff' ? 'View All ?' : 'Verify Now ?' }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead
                                class="bg-slate-50 dark:bg-slate-900/50 text-slate-600 dark:text-slate-400 uppercase tracking-wide text-[10px] font-bold">
                                <tr>
                                    <th class="px-6 py-3">Patient</th>
                                    <th class="px-6 py-3">Health Center</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse ($pendingPatientReqs as $pReq)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 cursor-pointer"
                                        onclick="location.href='{{ route('page.show', ['page' => $activeRole === 'Health Center Staff' ? 'hc_patient_requisitions' : 'patient_requisitions_hp']) }}'">
                                        <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                            {{ $pReq->patient->FName }} {{ $pReq->patient->LName }}</td>
                                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                            {{ $pReq->healthCenter->Name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black {{ $pReq->StatusType === 'Pending' ? 'bg-amber-100 text-amber-700' : ($pReq->StatusType === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700')}}">{{ $pReq->StatusType ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-slate-500">No patient requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Requisition Details Modal -->
    <div x-show="showDetailsModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[110] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide"
         x-cloak>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-1200/60" @click="closeDetailsModal()"></div>

        <!-- Modal Panel -->
        <div class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">
            <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 text-left">
                    <!-- Left: Core Info & Items -->
                    <div class="space-y-10">
                        <div class="grid grid-cols-2 gap-8">
                            <div class="col-span-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📍 Destination Station</label>
                                <div class="inline-flex items-center gap-2 px-6 py-3 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-[1.5rem] text-sm font-black uppercase tracking-widest border-2 border-blue-100 dark:border-blue-500/20 shadow-sm">
                                    <span x-text="activeReq.health_center?.Name || 'Unknown Station'"></span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Current Status</label>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" :class="{
                                    'bg-green-50 text-green-600': activeReq.StatusType === 'Approved',
                                    'bg-amber-50 text-amber-600': activeReq.StatusType === 'Pending',
                                    'bg-blue-50 text-blue-600': activeReq.StatusType === 'Completed'
                                }" x-text="activeReq.StatusType"></span>
                            </div>
                        </div>

                        <!-- Item List -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-800 pb-3">Requested Supplies</h3>
                            <div class="space-y-3">
                                <template x-for="item in activeReq.items || []">
                                    <div class="flex justify-between items-center p-5 bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] border border-slate-100 dark:border-slate-800 transition-all hover:bg-white dark:hover:bg-slate-800">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 flex items-center justify-center font-black text-indigo-500 text-xs text-center leading-none">
                                                📦
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="item.item?.ItemName"></p>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="item.QuantityRequested + ' units'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Decision Notes -->
                    <div class="space-y-10">
                        <div class="p-8 bg-slate-50 dark:bg-indigo-900/10 rounded-[2.5rem] border border-indigo-100 dark:border-indigo-800/30">
                            <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-4">Internal Dashboard Note</h3>
                            <p class="text-xs text-slate-500 leading-relaxed italic">
                                Use the full "Supply Requisitions" module for detailed item-level approvals and logistics tracking.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="px-10 py-8 border-t border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20 flex justify-end">
                <button @click="closeDetailsModal()" class="px-10 py-5 bg-slate-900 dark:bg-slate-700 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-xl transition-all active:scale-95">
                    Close Summary
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function dashboardManager() {
    return {
        showDetailsModal: false,
        activeReq: {},
        init() {
            this.$watch('showDetailsModal', value => this.toggleScroll(value));
        },
        toggleScroll(lock) {
            document.documentElement.classList.toggle('modal-lock', lock);
        },
        openDetailsModal(req) {
            console.log("Opening details for Dashboard Req:", req.RequisitionNumber);
            this.activeReq = req;
            this.showDetailsModal = true;
        },
        closeDetailsModal() {
            this.showDetailsModal = false;
        }
    }
}
</script>
@endsection
