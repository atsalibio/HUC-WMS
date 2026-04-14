@extends('layouts.app', ['currentPage' => 'history'])

@section('content')
<div class="space-y-6" x-data="historyManager()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="space-y-1">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">System Ledger</h3>
            <p class="text-slate-500 font-bold text-[10px] uppercase tracking-[0.3em]">Full audit trail of item movements and stock changes</p>
        </div>

        <div class="relative group">
            <select 
                x-model="activeTab" 
                @change="fetchHistory()"
                class="appearance-none px-10 py-4 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-black text-[10px] uppercase tracking-widest rounded-2xl border-2 border-slate-100 dark:border-slate-700 shadow-xl focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer min-w-[240px]">
                <template x-for="tab in tabs" :key="tab.id">
                    <option :value="tab.id" x-text="tab.label"></option>
                </template>
            </select>
            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-blue-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden min-h-[600px] flex flex-col">
        <!-- Search and Controls -->
        <div class="px-10 py-8 border-b border-slate-50 dark:border-slate-900/40 flex flex-col md:flex-row gap-6 items-center bg-slate-50/30 dark:bg-slate-900/10">
            <div class="relative flex-1 group">
                <span class="absolute inset-y-0 left-6 flex items-center text-slate-300 group-focus-within:text-blue-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Filter ledger entries..." class="w-full pl-16 h-14 rounded-[1.5rem] bg-white dark:bg-slate-900 border-none text-xs font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-blue-500/20 transition-all">
            </div>
            
            <button @click="fetchHistory()" class="px-8 h-14 bg-slate-900 dark:bg-blue-600 text-white rounded-[1.5rem] font-black text-[10px] uppercase tracking-widest flex items-center gap-3 shadow-xl transition-all active:scale-95">
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
                        <template x-if="activeTab !== 'summary'">
                            <th class="px-10 py-6" x-text="['inventory_count', 'hc_inventory_count', 'summary', 'hc_arrivals'].includes(activeTab) ? 'Auditor / System' : (activeTab === 'login_log' ? 'User Account' : 'Performed By')"></th>
                        </template>
                        <th class="px-10 py-6" x-text="['login_log', 'security_log', 'patient_log', 'audit_log'].includes(activeTab) ? 'Action Type' : (['procurement_orders', 'requisitions'].includes(activeTab) ? 'Order Reference' : (activeTab === 'patient_list' ? 'Items Dispensed' : 'Target Resource'))"></th>
                        
                        <template x-if="activeTab === 'summary'">
                            <th class="px-10 py-6">Lifetime Stock In</th>
                        </template>
                        <template x-if="activeTab === 'summary'">
                            <th class="px-10 py-6 text-center">Ledger Count</th>
                        </template>
                        <template x-if="activeTab === 'summary'">
                            <th class="px-10 py-6 text-right">Latest Arrival</th>
                        </template>

                        <template x-if="activeTab !== 'summary' && !['login_log', 'security_log', 'patient_log', 'audit_log', 'item_additions', 'hc_arrivals'].includes(activeTab)">
                            <th class="px-10 py-6 text-center">Quantity</th>
                        </template>
                        <template x-if="activeTab !== 'summary'">
                            <th class="px-10 py-6" x-text="['login_log', 'security_log', 'patient_log', 'audit_log'].includes(activeTab) ? 'Context details' : (['patient_list'].includes(activeTab) ? 'Patient & Destination' : 'Location / Activity Reason')"></th>
                        </template>
                        <template x-if="activeTab === 'login_log'">
                            <th class="px-10 py-6 text-center">Device Info</th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                    <template x-if="loading && historyData.length === 0">
                        <tr>
                            <td colspan="10" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
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

                            <!-- User / Performed By (Moved here) -->
                            <template x-if="activeTab !== 'summary'">
                                <td class="px-10 py-6 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 shrink-0 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase" x-text="row.User ? row.User.substring(0, 1) : 'S'"></div>
                                        <div>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200" x-text="row.User || 'System Audit'"></p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase" x-text="['login_log', 'security_log', 'patient_log', 'audit_log'].includes(activeTab) ? 'Authenticated Session' : 'Verified Record'"></p>
                                        </div>
                                    </div>
                                </td>
                            </template>

                            <!-- Entity -->
                            <td class="px-10 py-6 align-middle">
                                <div class="flex items-center gap-4">
                                    <template x-if="!['login_log', 'security_log', 'patient_log', 'audit_log'].includes(activeTab)">
                                        <div class="w-10 h-10 shrink-0 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-lg shadow-sm group-hover:scale-110 transition-transform" x-text="activeTab === 'patient_list' ? '🏥' : (['procurement_orders', 'requisitions'].includes(activeTab) ? '📄' : '📦')"></div>
                                    </template>
                                    <template x-if="['login_log', 'security_log', 'patient_log', 'audit_log'].includes(activeTab)">
                                        <div class="w-10 h-10 shrink-0 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-lg shadow-sm group-hover:scale-110 transition-transform" x-text="activeTab === 'login_log' ? '👤' : (activeTab === 'patient_log' ? '❤️' : (activeTab === 'audit_log' ? '📝' : '🛡️'))"></div>
                                    </template>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 dark:text-white" x-text="row.ItemName || row.Reference"></p>
                                        <template x-if="row.ItemID">
                                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest" x-text="'ID: ' + row.ItemID"></p>
                                        </template>
                                        <template x-if="row.BatchID">
                                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mt-0.5" x-text="'Batch: ' + row.BatchID"></p>
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
                            </template>
                            <template x-if="activeTab === 'summary'">
                                <td class="px-10 py-6 text-center">
                                    <span class="px-4 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-[10px] font-black text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800" x-text="row.TotalTransactions + ' entries'"></span>
                                </td>
                            </template>
                            <template x-if="activeTab === 'summary'">
                                <td class="px-10 py-6 text-right">
                                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-300" x-text="formatDate(row.LastReceived)"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter" x-text="formatTime(row.LastReceived)"></p>
                                </td>
                            </template>

                            <!-- Detail Views -->
                            <template x-if="activeTab !== 'summary' && !['login_log', 'security_log', 'patient_log', 'audit_log', 'item_additions', 'hc_arrivals'].includes(activeTab)">
                                <td class="px-10 py-6 text-center align-middle">
                                    <span class="text-sm font-black" :class="(row.Quantity || 0) > 0 ? 'text-blue-500' : ((row.Quantity || 0) < 0 ? 'text-rose-500' : 'text-slate-400')" x-text="((row.Quantity || 0) > 0 ? '+' : '') + parseFloat(row.Quantity || 0).toLocaleString()"></span>
                                </td>
                            </template>
                            <template x-if="activeTab !== 'summary'">
                                <td class="px-10 py-6 max-w-sm align-middle">
                                    <template x-if="row.HealthCenter || row.Patient">
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1" x-text="row.HealthCenter || row.Patient"></p>
                                    </template>
                                    <p class="text-[11px] font-bold text-slate-600 dark:text-slate-400 italic" x-text="row.Reason || row.Reference || 'System Record'"></p>
                                </td>
                            </template>
                            <!-- Device Info (only login) -->
                            <template x-if="activeTab === 'login_log'">
                                <td class="px-10 py-6 align-middle text-center">
                                    <div class="inline-flex px-3 py-1.5 rounded-xl border shadow-sm mx-auto" :class="row.Device === 'Mobile' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-slate-50 text-slate-600 border-slate-200'">
                                        <span class="text-[9px] font-black tracking-widest uppercase" x-text="row.Device || 'Desktop'"></span>
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
            { id: 'hc_inventory_count', label: 'HC Stocks' },
            { id: 'audit_log', label: 'General Audit Log' }
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
