@php
    $currentPage = $currentPage ?? 'dashboard';
    $userRole = optional($user)->Role ?? 'Health Center Staff';

    $menuItems = [
        ['label' => 'Dashboard', 'id' => 'dashboard', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>', 'roles' => ['Administrator', 'Health Center Staff', 'Head Pharmacist', 'Accounting Office User', 'Warehouse Staff', 'CMO/GSO/COA User']],
        ['label' => 'Patient Requisitions', 'id' => 'hc_patient_requisitions', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', 'roles' => ['Administrator', 'Health Center Staff', 'Head Pharmacist']],
        ['label' => 'Patient Approvals', 'id' => 'patient_requisitions_hp', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 'roles' => ['Administrator', 'Head Pharmacist']],
        ['label' => 'Requisitions', 'id' => 'requisitions', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', 'roles' => ['Administrator', 'Health Center Staff', 'Head Pharmacist']],
        ['label' => 'Procurement Orders', 'id' => 'procurement-orders', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>', 'roles' => ['Administrator', 'Warehouse Staff', 'Head Pharmacist']],
        ['label' => 'Suppliers', 'id' => 'suppliers', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>', 'roles' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff', 'Accounting Office User', 'CMO/GSO/COA User']],
        ['label' => 'Receiving', 'id' => 'receiving', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>', 'roles' => ['Administrator', 'Warehouse Staff']],
        ['label' => 'DPRI Import', 'id' => 'dpri_import', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>', 'roles' => ['Administrator', 'Head Pharmacist']],
        ['label' => 'Main Inventory', 'id' => 'inventory', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>', 'roles' => ['Administrator', 'Warehouse Staff', 'Head Pharmacist', 'Accounting Office User', 'CMO/GSO/COA User']],
        ['label' => 'HC Inventory', 'id' => 'hc_inventory', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>', 'roles' => ['Administrator', 'Health Center Staff', 'Head Pharmacist']],
        ['label' => 'Warehouse', 'id' => 'warehouse', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>', 'roles' => ['Administrator', 'Warehouse Staff']],
        ['label' => 'Issuance', 'id' => 'issuance', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>', 'roles' => ['Administrator', 'Warehouse Staff']],
        ['label' => 'Adjustments', 'id' => 'adjustments', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>', 'roles' => ['Administrator', 'Head Pharmacist']],
        ['label' => 'Reports', 'id' => 'reports', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>', 'roles' => ['Administrator', 'Accounting Office User', 'CMO/GSO/COA User', 'Head Pharmacist', 'Health Center Staff']],
        ['label' => 'System Ledger', 'id' => 'history', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>', 'roles' => ['Administrator', 'Head Pharmacist', 'Warehouse Staff', 'Health Center Staff']],
        ['label' => 'Settings', 'id' => 'settings', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>', 'roles' => ['Administrator', 'Health Center Staff', 'Accounting Office User', 'Warehouse Staff', 'Head Pharmacist', 'CMO/GSO/COA User']],
    ];
@endphp

<aside
    class="bg-slate-900 text-white w-64 h-screen flex flex-col flex-shrink-0 transition-all duration-300 border-r border-slate-800 overflow-hidden relative">
    <div class="h-16 flex-none flex items-center px-6 border-b border-slate-800 bg-slate-900">
        <h1 class="text-lg font-extrabold tracking-tight font-display">
            <span class="text-blue-500">Iloilo City</span> <span class="text-slate-400">WMS</span>
        </h1>
    </div>

    <div id="sidebar-scroll-container" class="flex-1 h-0 min-h-0 overflow-y-auto py-6 scrollbar-hide">
        <nav class="space-y-1.5 px-3">
            @foreach ($menuItems as $item)
                @if (in_array($userRole, $item['roles']))
                    @php
                        $isHC = $userRole === 'Health Center Staff';
                        $isActive = $currentPage === $item['id'];

                        // Role-specific active style
                        $activeClass = $isHC
                            ? 'bg-white/5 border-l-4 border-[' . $theme['accent'] . '] text-white rounded-none'
                            : 'bg-indigo-600/20 text-indigo-400 border-r-4 border-indigo-500 rounded-none';

                        if ($item['id'] === 'dashboard') {
                            $link = route('dashboard');
                        } elseif ($item['id'] === 'suppliers') {
                            $link = route('suppliers.index');
                        } elseif ($item['id'] === 'adjustments') {
                            $link = route('pages.adjustments');
                        } elseif ($item['id'] === 'inventory') {
                            $link = route('pages.inventory');
                        } else {
                            $link = route('page.show', ['page' => $item['id']]);
                        }
                    @endphp
                    <a href="{{ $link }}"
                        class="group flex items-center px-4 py-2.5 text-sm font-semibold transition-all duration-200 
                                       {{ $isActive ? $activeClass : 'text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-xl' }}">
                        <span
                            class="{{ $isActive ? 'text-[' . $theme['accent'] . ']' : 'text-slate-500 group-hover:text-[' . $theme['accent'] . ']' }} mr-3 flex-shrink-0 transition-colors">
                            {!! $item['icon'] !!}
                        </span>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    <div class="flex-none p-4 border-t border-slate-800 bg-slate-900/50 backdrop-blur-md space-y-4">
        <div class="flex items-center p-2 rounded-xl bg-slate-800/30 border border-slate-700/50">
            <div
                class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs border bg-{{ $theme['primary'] }}/20 text-{{ $theme['accent'] }} border-{{ $theme['accent'] }}/30">
                {{ substr(optional($user)->FName ?? 'U', 0, 1) . substr(optional($user)->LName ?? '', 0, 1) }}
            </div>
            <div class="ml-3 overflow-hidden">
                @if($userRole === 'Health Center Staff' && optional($user)->healthCenter)
                    <p
                        class="text-[10px] font-black text-{{ $theme['accent'] }} uppercase tracking-widest leading-none mb-1">
                        {{ $user->healthCenter->Name }}
                    </p>
                @else
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest leading-none mb-1">
                        Authenticated</p>
                @endif
                <p class="text-xs font-semibold text-slate-200 truncate">
                    {{ (optional($user)->FName ?? 'User') . ' ' . (optional($user)->LName ?? '') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full group flex items-center px-4 py-2.5 text-sm font-bold text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all duration-200">
                <span class="mr-3 text-slate-500 group-hover:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                </span>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarContainer = document.getElementById('sidebar-scroll-container');
        if (sidebarContainer) {
            // Restore scroll position
            const scrollPos = localStorage.getItem('sidebarScrollPos');
            if (scrollPos) {
                sidebarContainer.scrollTop = scrollPos;
            }

            // Save scroll position on scroll
            sidebarContainer.addEventListener('scroll', function () {
                localStorage.setItem('sidebarScrollPos', sidebarContainer.scrollTop);
            }, { passive: true });
        }
    });
</script>