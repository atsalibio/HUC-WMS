@extends('layouts.app', ['currentPage' => 'hc-inventory'])

@section('content')
    <div class="space-y-6" x-data="hcInventoryManager">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div class="space-y-1">
                <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight">Health Center
                    Depot</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Localized Stock Tracking for
                    your facility</p>
            </div>

            <div class="flex items-center gap-4">
                <div
                    class="px-5 py-3 bg-teal-50 dark:bg-teal-900/20 border border-teal-100 dark:border-teal-800/50 rounded-2xl flex items-center gap-3">
                    <span class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-teal-600 uppercase tracking-widest">Live Inventory Sync</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div
            class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div
                class="px-10 py-8 border-b border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/10 flex items-center justify-between">
                <div class="relative group flex-1 max-w-md">
                    <span
                        class="absolute inset-y-0 left-6 flex items-center text-slate-300 group-focus-within:text-teal-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="search" placeholder="Filter local stock..."
                        class="w-full pl-14 h-12 bg-white dark:bg-slate-900 border-none rounded-2xl text-xs font-bold shadow-sm focus:ring-2 focus:ring-teal-500/20 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                            <th class="px-10 py-6">Medical Entity</th>
                            <th class="px-10 py-6">Health Center</th>
                            <th class="px-10 py-6">Batch ID</th>
                            <th class="px-10 py-6 text-center">In Stock</th>
                            <th class="px-10 py-6">Expiry Timeline</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                        <template x-for="item in filteredInventory" :key="item.BatchID">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-lg">
                                            💊</div>
                                        <div>
                                            <p class="text-xs font-black text-slate-800 dark:text-white"
                                                x-text="item.ItemName"></p>
                                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest"
                                                x-text="item.ItemType"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-2">
                                        <template x-if="item.HealthCenterID == hcId">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.6)]"></span>
                                        </template>
                                        <span class="text-[11px] font-black tracking-tight"
                                            :class="item.HealthCenterID == hcId ? 'text-teal-600' : 'text-slate-600 dark:text-slate-400'"
                                            x-text="item.HealthCenterName"></span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <span
                                        class="px-3 py-1 bg-slate-100 dark:bg-slate-900 text-[10px] font-black font-mono text-slate-500 rounded-lg group-hover:bg-white transition-all shadow-sm"
                                        x-text="item.BatchID"></span>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <p class="text-lg font-black text-slate-800 dark:text-white tabular-nums"
                                        x-text="item.QuantityOnHand"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Units</p>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-[11px] font-bold"
                                        :class="isExpiringSoon(item.ExpiryDate) ? 'text-red-500' : 'text-slate-600 dark:text-slate-400'"
                                        x-text="formatDate(item.ExpiryDate)"></p>
                                    <template x-if="isExpiringSoon(item.ExpiryDate)">
                                        <p class="text-[9px] font-black text-red-400 uppercase tracking-widest">Action
                                            Required</p>
                                    </template>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button @click="viewDetails(item)"
                                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-teal-600 hover:border-teal-500/30 transition-all">
                                        👁️
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="hc_inventory.length === 0">
                            <tr>
                                <td colspan="6"
                                    class="py-20 text-center text-slate-300 font-black text-[10px] uppercase tracking-[0.4em] italic">
                                    No local inventory record detected</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Details Modal -->
        <template x-if="showDetailsModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
                <!-- Backdrop with separate transition -->
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showDetailsModal = false"></div>

                <!-- Modal Content -->
                <div
                    class="bg-white dark:bg-slate-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl shadow-black/20 overflow-hidden border border-slate-200/60 dark:border-slate-700/50 pointer-events-auto flex flex-col animate-premium-in"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                    <div
                        class="px-8 pt-8 pb-6 border-b border-slate-200/60 dark:border-slate-700/40 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white" x-text="selectedItem.ItemName">
                            </h2>
                            <p
                                class="text-[10px] font-black text-teal-600 dark:text-teal-400 uppercase tracking-widest mt-1">
                                Local Stock
                                Detail</p>
                        </div>
                        <button @click="showDetailsModal = false"
                            class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                                <p
                                    class="text-[8px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">
                                    Batch ID</p>
                                <p class="text-xs font-black text-slate-800 dark:text-slate-100 font-mono"
                                    x-text="selectedItem.BatchID"></p>
                            </div>
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                                <p
                                    class="text-[8px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">
                                    Category</p>
                                <p class="text-xs font-black text-teal-600 dark:text-teal-400 uppercase"
                                    x-text="selectedItem.ItemType"></p>
                            </div>
                        </div>
                        <div
                            class="p-6 bg-teal-50 dark:bg-teal-900/20 rounded-3xl border border-teal-100 dark:border-teal-800/50 text-center">
                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-[0.2em] mb-2">Available
                                Quantity</p>
                            <p class="text-4xl font-black text-teal-600 tracking-tighter"
                                x-text="selectedItem.QuantityOnHand"></p>
                            <p class="text-[10px] font-black text-teal-600/60 uppercase">Units currently in depot</p>
                        </div>
                        <div
                            class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-800/50 flex justify-between items-center">
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                Expiry Date</p>
                            <p class="text-sm font-black text-slate-800 dark:text-slate-100 font-mono"
                                x-text="formatDate(selectedItem.ExpiryDate)"></p>
                        </div>
                    </div>
                    <div class="px-8 pb-8">
                        <button @click="showDetailsModal = false"
                            class="w-full py-4 bg-slate-900 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 transition-all">Close</button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('hcInventoryManager', () => ({
                    hc_inventory: @json($hc_inventory),
                    hcId: @json($hcId),
                    search: '',
                    showDetailsModal: false,
                    selectedItem: null,

                    get filteredInventory() {
                        if (!this.search) return this.hc_inventory;
                        const q = this.search.toLowerCase();
                        return this.hc_inventory.filter(i =>
                            i.ItemName.toLowerCase().includes(q) ||
                            i.BatchID.toString().toLowerCase().includes(q) ||
                            i.HealthCenterName.toLowerCase().includes(q)
                        );
                    },

                    viewDetails(item) {
                        this.selectedItem = item;
                        this.showDetailsModal = true;
                    },
                    formatDate(dateStr) {
                        if (!dateStr || dateStr === 'N/A') return 'N/A';
                        const date = new Date(dateStr);
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    },
                    isExpiringSoon(dateStr) {
                        if (!dateStr || dateStr === 'N/A') return false;
                        const expiry = new Date(dateStr);
                        const now = new Date();
                        const diff = expiry - now;
                        return diff < (1000 * 60 * 60 * 24 * 90);
                    }
                }));
            });
        </script>
    @endpush
@endsection