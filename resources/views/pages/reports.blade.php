@extends('layouts.app', ['currentPage' => 'reports'])

@section('content')
<div class="space-y-6" x-data="reportsManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="space-y-1">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1" 
                x-text="activeTab === 'generate' ? 'Generate Intelligence' : 'Archived Reports'">
            </h3>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]"
               x-text="activeTab === 'generate' ? 'Create detailed inventory and financial summaries' : 'Access previously generated verification documents'"></p>
        </div>

        <div class="inline-flex p-1.5 bg-slate-200/50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800">
            <button @click="activeTab = 'generate'" 
                :class="activeTab === 'generate' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                New Report
            </button>
            <button @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 ml-2">
                Archives
            </button>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden min-h-[500px]">
        
        <!-- Generate Tab -->
        <div x-show="activeTab === 'generate'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" class="p-10 lg:p-16">
            <div class="max-w-4xl mx-auto space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start text-left">
                    <div class="space-y-6">
                        <div class="w-16 h-16 rounded-[2rem] bg-teal-50 dark:bg-teal-900/20 text-teal-600 flex items-center justify-center text-3xl shadow-sm">📊</div>
                        <h4 class="text-xl font-black text-slate-800 dark:text-white leading-tight">Report Configuration</h4>
                        <p class="text-sm font-bold text-slate-500 leading-relaxed">Select the metrics you want to analyze. Our system will compile data across all warehouse and health center nodes.</p>
                    </div>

                    <form @submit.prevent="generateReport" class="space-y-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Analytical Model</label>
                                <select x-model="formData.reportType" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white">
                                    <option value="inventory_valuation">Financial: Inventory Valuation</option>
                                    <option value="receipt_confirmation">Logistical: Receipt Confirmation</option>
                                    <option value="stock_card_ledger">Operational: Stock Card & Ledger</option>
                                </select>
                            </div>

                            <template x-if="formData.reportType === 'receipt_confirmation'">
                                <div x-transition class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Source Procurement Order</label>
                                    <select x-model="formData.poId" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white">
                                        <option value="">Select completed PO...</option>
                                        <template x-for="po in completedPOs" :key="po.POID">
                                            <option :value="po.POID" x-text="po.PONumber"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>

                            <template x-if="formData.reportType === 'stock_card_ledger'">
                                <div x-transition class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Target Inventory Item</label>
                                    <select x-model="formData.itemId" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white">
                                        <option value="">Select item...</option>
                                        <template x-for="item in items" :key="item.ItemID">
                                            <option :value="item.ItemID" x-text="item.ItemName"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Destination Office</label>
                                <select x-model="formData.forOffice" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white">
                                    <option value="accounting">Accounting Office</option>
                                    <option value="cmo">CMO Office</option>
                                    <option value="gso">GSO Office</option>
                                    <option value="coa">COA Office</option>
                                    <option value="pharmacy">Main Pharmacy</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-5 bg-slate-900 dark:bg-teal-600 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-2xl shadow-2xl transition-all active:scale-95 disabled:opacity-50" :disabled="loading">
                            <span x-show="!loading">Compile Analytical Data</span>
                            <span x-show="loading">Synthesizing...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div x-show="activeTab === 'history'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                            <th class="px-10 py-6">Reference ID</th>
                            <th class="px-10 py-6">Analytical Model</th>
                            <th class="px-10 py-6">Destination</th>
                            <th class="px-10 py-6">Compiled By</th>
                            <th class="px-10 py-6">Timestamp</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                        <template x-for="rpt in history" :key="rpt.ReportID">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                                <td class="px-10 py-6">
                                    <span class="text-[10px] font-black font-mono text-slate-400 group-hover:text-teal-600 transition-colors" x-text="rpt.ReportID"></span>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-xs font-black text-slate-800 dark:text-white" x-text="rpt.ReportType"></p>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest" x-text="rpt.GeneratedForOffice"></p>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase" x-text="rpt.GeneratedByFullName.substring(0,1)"></div>
                                        <p class="text-[11px] font-black text-slate-700 dark:text-slate-200" x-text="rpt.GeneratedByFullName"></p>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-[11px] font-black text-slate-700 dark:text-slate-200" x-text="new Date(rpt.GeneratedDate).toLocaleDateString()"></p>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter" x-text="new Date(rpt.GeneratedDate).toLocaleTimeString()"></p>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-teal-600 hover:border-teal-500/30 transition-all">
                                        👁️
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="history.length === 0">
                            <tr><td colspan="6" class="py-20 text-center text-slate-400 font-black text-[10px] uppercase tracking-[0.3em] italic">No archived reports found</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function reportsManager() {
    return {
        activeTab: 'generate',
        loading: false,
        completedPOs: @json($completedPOs),
        items: @json($items),
        history: @json($history),
        formData: {
            reportType: 'inventory_valuation',
            forOffice: 'accounting',
            poId: '',
            itemId: ''
        },

        async generateReport() {
            this.loading = true;
            try {
                const response = await fetch('/reports/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                const result = await response.json();
                if (result.success) {
                    this.history.unshift(result.report);
                    this.activeTab = 'history';
                    alert('Report generated and archived!');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (err) {
                alert('Connection error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
