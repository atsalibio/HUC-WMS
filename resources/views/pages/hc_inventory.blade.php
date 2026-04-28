@extends('layouts.app', ['currentPage' => 'hc-inventory'])

@section('content')
    <div class="space-y-6" x-data="hcInventoryManager">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 px-4">
            <div class="space-y-1">
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.4em]">Facility Stock</p>
                <h1 class="text-5xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight leading-none">HC Depot</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium max-w-md mt-2">
                    Aggregated stock view per medical item for your health center.
                </p>
            </div>
            <div class="flex items-center gap-4">
                @if(in_array(Auth::user()->Role, ['Administrator', 'Head Pharmacist']))
                <div class="relative">
                    <select x-model="selectedHCId"
                        class="appearance-none pl-5 pr-10 h-12 bg-white dark:bg-slate-800 border-none rounded-2xl text-blue-600 dark:text-blue-400 font-black text-[10px] uppercase tracking-widest shadow-lg focus:ring-2 focus:ring-blue-500/20 cursor-pointer">
                        <option value="">All Health Centers</option>
                        @foreach($healthCenters as $hc)
                            <option value="{{ $hc->HealthCenterID }}">{{ $hc->Name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-blue-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @endif
                <div class="px-5 py-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50 rounded-2xl flex items-center gap-3">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Live Inventory Sync</span>
                </div>
            </div>
        </div>

        <!-- Controls Row -->
        <div class="flex flex-col md:flex-row gap-4 mb-6 px-1">
            <div class="relative group flex-1 max-w-md">
                <span class="absolute inset-y-0 left-5 flex items-center text-slate-300 group-focus-within:text-blue-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" x-model="search" placeholder="Search items..."
                    class="w-full pl-14 h-12 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold shadow-sm focus:ring-2 focus:ring-blue-500/20 transition-all">
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <select x-model="sortBy"
                        class="appearance-none pl-5 pr-10 h-12 bg-white dark:bg-slate-800 border-none rounded-2xl text-indigo-600 dark:text-indigo-400 font-black text-[10px] uppercase tracking-widest shadow-sm focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                        <option value="name">Name</option>
                        <option value="expiry">Expiry Date</option>
                        <option value="received">Date Received</option>
                        <option value="stock">Stock Level</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <button @click="sortDir = sortDir === 'asc' ? 'desc' : 'asc'"
                    class="h-12 w-12 flex items-center justify-center bg-white dark:bg-slate-800 border-none rounded-2xl text-indigo-600 dark:text-indigo-400 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sortDir === 'desc' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="px-5 h-12 flex items-center bg-white dark:bg-slate-800 rounded-2xl shadow-sm">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <span x-text="sortedGrouped.length"></span> items
                        <template x-if="selectedHCId">
                            <span x-text="' · ' + (selectedHCName || '')" class="text-blue-500"></span>
                        </template>
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.15em] font-black text-slate-400 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/20">
                            <th class="w-14 px-4 py-5"></th>
                            <th class="px-5 py-5">Medical Entity</th>
                            <th class="px-5 py-5">Category</th>
                            <th class="px-5 py-5 text-center">Total Stock</th>
                            <th class="px-5 py-5">Earliest Expiry</th>
                            <th class="px-5 py-5">Last Received</th>
                            <th class="px-5 py-5 text-center">Batches</th>
                            <th class="px-5 py-5 text-right pr-8">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hc-inventory-body">
                        {{-- Alpine renders rows here via x-for --}}
                    </tbody>
                </table>

                {{-- Alpine-rendered rows (outside table to avoid tbody nesting) --}}
                {{-- We instead render inside tbody via Alpine directly --}}
            </div>
        </div>

        {{-- Row template rendered by Alpine into #hc-inventory-body --}}
        <template x-if="sortedGrouped.length === 0">
            <div></div>{{-- placeholder --}}
        </template>

        <!-- Details Modal -->
        <div x-show="showDetailsModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm bg-slate-900/60"
            x-cloak @click.self="showDetailsModal = false">

            <div class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200/60 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">
                <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-start bg-slate-50/50 dark:bg-slate-900/20">
                    <div>
                        <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] mb-1">HC Stock Detail</p>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white" x-text="selectedGroup?.ItemName"></h2>
                        <p class="text-xs font-bold text-slate-400 mt-1" x-text="(selectedGroup?.ItemType ?? '') + ' · ' + (selectedGroup?.UnitOfMeasure ?? '')"></p>
                    </div>
                    <button @click="showDetailsModal = false" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-10 py-6">
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="p-5 bg-blue-50 dark:bg-blue-900/20 rounded-3xl border border-blue-100 dark:border-blue-800/50 text-center">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-1">Total Stock</p>
                            <p class="text-3xl font-black text-blue-600" x-text="selectedGroup?.TotalStock.toLocaleString()"></p>
                            <p class="text-[9px] font-black text-blue-500/60 uppercase">units in depot</p>
                        </div>
                        <div class="p-5 bg-slate-50 dark:bg-slate-900/50 rounded-3xl border border-slate-100 dark:border-slate-800/50 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Batches</p>
                            <p class="text-3xl font-black text-slate-700 dark:text-white" x-text="selectedGroup?.batches.length"></p>
                            <p class="text-[9px] font-black text-slate-400 uppercase">active lots</p>
                        </div>
                        <div class="p-5 rounded-3xl border text-center"
                            :class="isExpiringSoon(selectedGroup?.EarliestExpiry) ? 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800/50' : 'bg-slate-50 dark:bg-slate-900/50 border-slate-100 dark:border-slate-700'">
                            <p class="text-[9px] font-black uppercase tracking-widest mb-1"
                                :class="isExpiringSoon(selectedGroup?.EarliestExpiry) ? 'text-red-500' : 'text-slate-400'">Earliest Expiry</p>
                            <p class="text-sm font-black"
                                :class="isExpiringSoon(selectedGroup?.EarliestExpiry) ? 'text-red-600' : 'text-slate-700 dark:text-white'"
                                x-text="formatDate(selectedGroup?.EarliestExpiry)"></p>
                            <template x-if="isExpiringSoon(selectedGroup?.EarliestExpiry)">
                                <p class="text-[9px] font-black text-red-400 uppercase">⚠ Expiring soon</p>
                            </template>
                        </div>
                    </div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Batch / Issuance Listing</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                        <template x-for="(batch, i) in (selectedGroup?.batches ?? [])" :key="batch.HCBatchID">
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800/50 hover:border-blue-200 dark:hover:border-blue-800/50 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-xl font-black text-[10px] flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500"
                                        x-text="i + 1"></div>
                                    <div>
                                        <p class="text-[11px] font-black text-slate-700 dark:text-white" x-text="'HC Batch #' + batch.HCBatchNumber"></p>
                                        <p class="text-[9px] font-bold text-slate-400"
                                            x-text="'Received: ' + formatDate(batch.DateReceivedAtHC) + (batch.LotNumber ? ' · Lot: ' + batch.LotNumber : '')"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black tabular-nums"
                                        :class="batch.QuantityOnHand <= 0 ? 'text-red-400' : 'text-slate-800 dark:text-white'"
                                        x-text="batch.QuantityOnHand.toLocaleString() + ' units'"></p>
                                    <p class="text-[9px] font-bold"
                                        :class="isExpiringSoon(batch.ExpiryDate) ? 'text-red-400' : 'text-slate-400'"
                                        x-text="'Exp: ' + formatDate(batch.ExpiryDate)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="px-10 pb-10">
                    <button @click="showDetailsModal = false"
                        class="w-full py-4 bg-slate-900 dark:bg-slate-700 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 dark:hover:bg-slate-600 transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('hcInventoryManager', () => ({
                    hc_inventory: @json($hc_inventory),
                    hcId: @json($hcId ?? null),
                    userRole: @json($userRole ?? ''),
                    healthCenters: @json($healthCenters ?? []),
                    selectedHCId: '',
                    search: '',
                    sortBy: 'name',
                    sortDir: 'asc',
                    expanded: [],
                    showDetailsModal: false,
                    selectedGroup: null,

                    init() {
                        // HC Staff: lock to their health center
                        if (this.hcId && this.userRole === 'Health Center Staff') {
                            this.selectedHCId = String(this.hcId);
                        }
                        this.$nextTick(() => this.renderRows());
                        this.$watch('sortedGrouped', () => this.renderRows());
                        this.$watch('expanded', () => this.renderRows());
                    },

                    get selectedHCName() {
                        if (!this.selectedHCId) return '';
                        const hc = this.healthCenters.find(h => String(h.HealthCenterID) === String(this.selectedHCId));
                        return hc ? hc.Name : '';
                    },

                    get groupedInventory() {
                        // Filter raw rows by selected HC first
                        let rows = this.hc_inventory;
                        if (this.selectedHCId) {
                            rows = rows.filter(r => String(r.HealthCenterID) === String(this.selectedHCId));
                        }

                        const groups = {};
                        rows.forEach(row => {
                            const id = row.ItemID;
                            if (!groups[id]) {
                                groups[id] = {
                                    ItemID: id,
                                    ItemName: row.ItemName ?? '—',
                                    ItemType: row.ItemType ?? '—',
                                    UnitOfMeasure: row.UnitOfMeasure ?? '—',
                                    HealthCenterName: row.HealthCenterName ?? '—',
                                    TotalStock: 0,
                                    EarliestExpiry: null,
                                    LastReceived: null,
                                    batches: []
                                };
                            }
                            const g = groups[id];
                            g.TotalStock += Number(row.QuantityOnHand ?? 0);
                            g.batches.push(row);
                            if (row.ExpiryDate && (!g.EarliestExpiry || new Date(row.ExpiryDate) < new Date(g.EarliestExpiry))) {
                                g.EarliestExpiry = row.ExpiryDate;
                            }
                            if (row.DateReceivedAtHC && (!g.LastReceived || new Date(row.DateReceivedAtHC) > new Date(g.LastReceived))) {
                                g.LastReceived = row.DateReceivedAtHC;
                            }
                        });

                        // Sort batches inside each group by DateReceivedAtHC (oldest first)
                        Object.values(groups).forEach(g => {
                            g.batches.sort((a, b) => new Date(a.DateReceivedAtHC) - new Date(b.DateReceivedAtHC));
                        });

                        return Object.values(groups);
                    },

                    get filteredGrouped() {
                        if (!this.search) return this.groupedInventory;
                        const q = this.search.toLowerCase();
                        return this.groupedInventory.filter(g =>
                            g.ItemName.toLowerCase().includes(q) ||
                            g.ItemType.toLowerCase().includes(q)
                        );
                    },

                    get sortedGrouped() {
                        const arr = [...this.filteredGrouped];
                        const dir = this.sortDir === 'asc' ? 1 : -1;
                        arr.sort((a, b) => {
                            if (this.sortBy === 'name') return dir * a.ItemName.localeCompare(b.ItemName);
                            if (this.sortBy === 'expiry') {
                                if (!a.EarliestExpiry) return 1;
                                if (!b.EarliestExpiry) return -1;
                                return dir * (new Date(a.EarliestExpiry) - new Date(b.EarliestExpiry));
                            }
                            if (this.sortBy === 'received') {
                                if (!a.LastReceived) return 1;
                                if (!b.LastReceived) return -1;
                                return dir * (new Date(b.LastReceived) - new Date(a.LastReceived));
                            }
                            if (this.sortBy === 'stock') return dir * (b.TotalStock - a.TotalStock);
                            return 0;
                        });
                        return arr;
                    },

                    renderRows() {
                        const tbody = document.getElementById('hc-inventory-body');
                        if (!tbody) return;
                        tbody.innerHTML = '';

                        if (this.sortedGrouped.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-300 font-black text-[10px] uppercase tracking-[0.4em] italic">No local inventory record detected</td></tr>`;
                            return;
                        }

                        this.sortedGrouped.forEach(group => {
                            const isExpanded = this.expanded.includes(group.ItemID);
                            const chevronClass = isExpanded
                                ? 'rotate-90 bg-blue-100 dark:bg-blue-900/30 text-blue-500'
                                : 'bg-slate-100 dark:bg-slate-900 text-slate-400';
                            const stockClass = group.TotalStock === 0
                                ? 'text-red-500'
                                : group.TotalStock <= 50 ? 'text-amber-500' : 'text-slate-800 dark:text-white';
                            const expiryClass = this.isExpiringSoon(group.EarliestExpiry)
                                ? 'text-red-500' : 'text-slate-600 dark:text-slate-400';
                            const actionWarning = this.isExpiringSoon(group.EarliestExpiry)
                                ? '<p class="text-[9px] font-black text-red-400 uppercase tracking-widest">⚠ Action Required</p>' : '';

                            // Parent row
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-all cursor-pointer border-b border-slate-100 dark:border-slate-700/50';
                            tr.dataset.itemId = group.ItemID;
                            tr.innerHTML = `
                                <td class="w-14 px-4 py-5">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center transition-all duration-200 mx-auto ${chevronClass}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-500 font-black text-sm shrink-0">${group.ItemName.charAt(0)}</div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-black text-slate-800 dark:text-white">${group.ItemName}</p>
                                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">${group.UnitOfMeasure}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-black text-[9px] uppercase tracking-widest rounded-xl whitespace-nowrap">${group.ItemType}</span>
                                </td>
                                <td class="px-5 py-5 text-center">
                                    <p class="text-xl font-black tabular-nums leading-tight ${stockClass}">${group.TotalStock.toLocaleString()}</p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">units</p>
                                </td>
                                <td class="px-5 py-5">
                                    <p class="text-xs font-bold ${expiryClass}">${this.formatDate(group.EarliestExpiry)}</p>
                                    ${actionWarning}
                                </td>
                                <td class="px-5 py-5">
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400">${this.formatDate(group.LastReceived)}</p>
                                </td>
                                <td class="px-5 py-5 text-center">
                                    <span class="w-8 h-8 inline-flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-900 text-[11px] font-black text-slate-600 dark:text-slate-300">${group.batches.length}</span>
                                </td>
                                <td class="px-5 py-5 text-right pr-8">
                                    <button data-view="${group.ItemID}" class="view-btn w-9 h-9 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-blue-600 hover:border-blue-500/30 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            `;

                            tr.addEventListener('click', (e) => {
                                if (!e.target.closest('.view-btn')) this.toggleExpand(group.ItemID);
                            });
                            tr.querySelector('.view-btn').addEventListener('click', (e) => {
                                e.stopPropagation();
                                this.viewGroupDetails(group);
                            });
                            tbody.appendChild(tr);

                            // Batch rows when expanded
                            if (isExpanded) {
                                group.batches.forEach((batch, bi) => {
                                    const btr = document.createElement('tr');
                                    btr.className = 'bg-blue-50/30 dark:bg-blue-900/5 border-b border-blue-50 dark:border-blue-900/20';
                                    const batchExpiry = this.isExpiringSoon(batch.ExpiryDate) ? 'text-red-500' : 'text-slate-500 dark:text-slate-400';
                                    const statusClass = batch.QuantityOnHand <= 0
                                        ? 'bg-red-50 text-red-400 dark:bg-red-900/20'
                                        : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400';
                                    const statusLabel = batch.QuantityOnHand <= 0 ? 'Depleted' : 'Active';
                                    const lotLabel = batch.LotNumber ? ` · ${batch.LotNumber}` : '';
                                    btr.innerHTML = `
                                        <td class="w-14 px-4 py-4">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-300 mx-auto"></div>
                                        </td>
                                        <td class="px-5 py-4" colspan="2">
                                            <div class="ml-12 flex items-center gap-2">
                                                <span class="w-5 h-5 inline-flex items-center justify-center rounded-md bg-blue-100 dark:bg-blue-900/40 text-blue-500 font-black text-[9px]">${bi + 1}</span>
                                                <span class="font-mono text-[10px] font-black text-slate-500 dark:text-slate-400">${batch.HCBatchNumber}${lotLabel}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <p class="text-sm font-black tabular-nums ${batch.QuantityOnHand <= 0 ? 'text-red-400' : 'text-slate-700 dark:text-slate-300'}">${Number(batch.QuantityOnHand).toLocaleString()}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-[11px] font-bold ${batchExpiry}">${this.formatDate(batch.ExpiryDate)}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">${this.formatDate(batch.DateReceivedAtHC)}</p>
                                        </td>
                                        <td class="px-5 py-4 text-center"><span class="text-slate-300 text-xs">—</span></td>
                                        <td class="px-5 py-4 text-right pr-8">
                                            <span class="text-[9px] font-black px-2.5 py-1 rounded-xl uppercase tracking-widest ${statusClass}">${statusLabel}</span>
                                        </td>
                                    `;
                                    tbody.appendChild(btr);
                                });
                            }
                        });
                    },

                    toggleExpand(itemId) {
                        if (this.expanded.includes(itemId)) {
                            this.expanded = this.expanded.filter(id => id !== itemId);
                        } else {
                            this.expanded = [...this.expanded, itemId];
                        }
                    },

                    viewGroupDetails(group) {
                        this.selectedGroup = group;
                        this.showDetailsModal = true;
                    },

                    formatDate(dateStr) {
                        if (!dateStr || dateStr === 'N/A') return 'N/A';
                        return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    },

                    isExpiringSoon(dateStr) {
                        if (!dateStr || dateStr === 'N/A') return false;
                        return (new Date(dateStr) - new Date()) < (1000 * 60 * 60 * 24 * 90);
                    }
                }));
            });
        </script>
    @endpush
@endsection
