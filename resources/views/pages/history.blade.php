@extends('layouts.app', ['currentPage' => 'history'])

@section('content')
<div class="space-y-6" x-data="historyManager()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="space-y-1">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">System Ledger</h3>
            <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.3em]">Full audit trail of item movements and stock changes</p>
        </div>

        <div class="flex flex-wrap gap-2 p-1.5 bg-slate-200/50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 backdrop-blur-md overflow-x-auto max-w-full">
            <template x-for="tab in tabs" :key="tab.id">
                <button 
                    @click="setActiveTab(tab.id)"
                    :class="activeTab === tab.id ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                    class="px-4 py-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all whitespace-nowrap"
                    x-text="tab.label">
                </button>
            </template>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden min-h-[600px] flex flex-col">
        <!-- Search and Controls -->
        <div class="px-10 py-8 border-b border-slate-50 dark:border-slate-900/40 flex flex-col md:flex-row gap-6 items-center bg-slate-50/30 dark:bg-slate-900/10">
            <div class="relative flex-1 group">
                <span class="absolute inset-y-0 left-6 flex items-center text-slate-300 group-focus-within:text-teal-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Filter ledger entries..." class="w-full pl-16 h-14 rounded-[1.5rem] bg-white dark:bg-slate-900 border-none text-xs font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-teal-500/20 transition-all">
            </div>
            
            <button @click="fetchHistory()" class="px-8 h-14 bg-slate-900 dark:bg-teal-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl transition-all active:scale-95">
                <svg class="w-4 h-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Refresh Data
            </button>
        </div>

        <!-- Table Container -->
        <div class="flex-1 overflow-x-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                        <template x-if="activeTab !== 'summary'">
                            <th class="px-10 py-6">Timestamp</th>
                        </template>
                        <th class="px-10 py-6">Target Entity</th>
                        
                        <template x-if="activeTab === 'summary'">
                            <th class="px-10 py-6">Lifetime Stock In</th>
                            <th class="px-10 py-6 text-center">Ledger Count</th>
                            <th class="px-10 py-6 text-right">Latest Arrival</th>
                        </template>

                        <template x-if="activeTab !== 'summary'">
                            <th class="px-10 py-6 text-center">Qty ∆</th>
                            <th class="px-10 py-6">Activity Context</th>
                            <th class="px-10 py-6">Authorized By</th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                    <template x-if="loading && historyData.length === 0">
                        <tr>
                            <td colspan="10" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-3xl bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-500"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Compiling Transactional Data...</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && filteredData.length === 0">
                        <tr>
                            <td colspan="10" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center gap-4 opacity-40">
                                    <div class="w-16 h-16 rounded-3xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-3xl">📭</div>
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No matching ledger entries</span>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-for="(row, index) in filteredData" :key="index">
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                            <!-- Date -->
                            <template x-if="activeTab !== 'summary'">
                                <td class="px-10 py-6">
                                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-200" x-text="formatDate(row.Date)"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter" x-text="formatTime(row.Date)"></p>
                                </td>
                            </template>

                            <!-- Entity -->
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-lg shadow-sm group-hover:scale-110 transition-transform">📦</div>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 dark:text-white" x-text="row.ItemName || row.Reference"></p>
                                        <template x-if="row.ItemID">
                                            <p class="text-[9px] font-black text-teal-600 uppercase tracking-widest" x-text="'ID: ' + row.ItemID"></p>
                                        </template>
                                    </div>
                                </div>
                            </td>

                            <!-- Summary Views -->
                            <template x-if="activeTab === 'summary'">
                                <td class="px-10 py-6">
                                    <p class="text-lg font-black text-emerald-600 dark:text-emerald-400" x-text="parseInt(row.TotalAdded).toLocaleString()"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Lifetime Units</p>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-[10px] font-black text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800" x-text="row.TotalTransactions + ' entries'"></span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-300" x-text="formatDate(row.LastReceived)"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter" x-text="formatTime(row.LastReceived)"></p>
                                </td>
                            </template>

                            <!-- Detail Views -->
                            <template x-if="activeTab !== 'summary'">
                                <td class="px-10 py-6 text-center">
                                    <span class="text-sm font-black" :class="row.Quantity > 0 ? 'text-teal-500' : 'text-rose-500'" x-text="(row.Quantity > 0 ? '+' : '') + row.Quantity"></span>
                                </td>
                                <td class="px-10 py-6">
                                    <template x-if="row.HealthCenter || row.Patient">
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1" x-text="row.HealthCenter || row.Patient"></p>
                                    </template>
                                    <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 italic" x-text="row.Reason || row.Reference || 'System Record'"></p>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase" x-text="row.User ? row.User.substring(0, 1) : 'S'"></div>
                                        <div>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200" x-text="row.User || 'System (Auto)'"></p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase">Verified</p>
                                        </div>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function historyManager() {
    return {
        activeTab: 'summary',
        tabs: [
            { id: 'summary', label: 'Item Summaries' },
            { id: 'item_additions', label: 'Central Arrivals' },
            { id: 'hc_arrivals', label: 'HC Arrivals' },
            { id: 'warehouse_issuances', label: 'Issuances' },
            { id: 'requisitions', label: 'Requisitions' },
            { id: 'procurement_orders', label: 'Procurement' },
            { id: 'patient_list', label: 'Patient Distributions' },
            { id: 'adjustments', label: 'Adjustments' },
            { id: 'security_log', label: 'Security Log' },
            { id: 'login_log', label: 'Login Log' },
            { id: 'patient_log', label: 'Patient Log' },
            { id: 'inventory_count', label: 'Central Stocks' },
            { id: 'hc_inventory_count', label: 'HC Stocks' }
        ],
        historyData: [],
        searchQuery: '',
        loading: false,

        init() {
            this.fetchHistory();
        },

        setActiveTab(tabId) {
            this.activeTab = tabId;
            this.historyData = [];
            this.fetchHistory();
        },

        async fetchHistory() {
            this.loading = true;
            try {
                const response = await fetch('/history/data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type: this.activeTab })
                });
                const result = await response.json();
                if (result.success) {
                    this.historyData = result.data;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        get filteredData() {
            if (!this.searchQuery) return this.historyData;
            const q = this.searchQuery.toLowerCase();
            return this.historyData.filter(i => 
                (i.ItemName && i.ItemName.toLowerCase().includes(q)) ||
                (i.Reference && i.Reference.toLowerCase().includes(q)) ||
                (i.HealthCenter && i.HealthCenter.toLowerCase().includes(q)) ||
                (i.Patient && i.Patient.toLowerCase().includes(q)) ||
                (i.User && i.User.toLowerCase().includes(q)) ||
                (i.Reason && i.Reason.toLowerCase().includes(q))
            );
        },

        formatDate(d) {
            if (!d) return '-';
            return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        formatTime(d) {
            if (!d) return '';
            return new Date(d).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
@endsection
