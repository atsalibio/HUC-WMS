@extends('layouts.app', ['currentPage' => 'reports'])

@section('content')
    <div class="space-y-6" x-data="reportsManager()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 px-4">
            <div class="space-y-1">
                <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight"
                    x-text="activeTab === 'generate' ? 'Generate Intelligence' : (activeTab === 'preview' ? 'Live Preview' : 'Archived Reports')">
                </h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1.5"
                    x-text="activeTab === 'generate' ? 'Create detailed inventory and financial summaries' : (activeTab === 'preview' ? 'Review your latest generated intelligence' : 'Access previously generated verification documents')">
                </p>
            </div>

            <div
                class="flex gap-4 p-1.5 bg-slate-100 dark:bg-slate-900/50 rounded-2xl border border-slate-200/50 dark:border-slate-800/50">
                <button @click="activeTab = 'generate'"
                    :class="activeTab === 'generate' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                    class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    ⚙️ Generate
                </button>
                <button @click="activeTab = 'preview'"
                    :class="activeTab === 'preview' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                    class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    👀 Live Preview
                </button>
                <button @click="activeTab = 'history'"
                    :class="activeTab === 'history' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-300'"
                    class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    📁 Archives
                </button>
            </div>
        </div>

        <!-- Main Content Container -->
        <div
            class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden min-h-[500px]">

            <!-- Preview Tab Content -->
            <div x-show="activeTab === 'preview'" class="space-y-6" x-transition>
                <template x-if="!selectedReport.Data">
                    <div
                        class="p-20 text-center bg-slate-50 dark:bg-slate-900/50 rounded-[3rem] border border-dashed border-slate-200 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">No Active Preview</p>
                        <p class="text-xs text-slate-500 mt-2">Generate a report to see the live intelligence synthesis
                            here.</p>
                    </div>
                </template>
                <template x-if="selectedReport.Data">
                    <div
                        class="bg-white dark:bg-slate-800 p-10 rounded-[3rem] shadow-xl border border-slate-100 dark:border-slate-700">
                        <div
                            class="flex flex-col md:flex-row justify-between items-start gap-6 mb-10 pb-8 border-b border-slate-50 dark:border-slate-700/30">
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 dark:text-white"
                                    x-text="selectedReport.ReportType"></h3>
                                <p class="text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] mt-1">Sythensis
                                    Preview (Non-Archived)</p>
                            </div>
                            <div class="flex gap-4 w-full md:w-auto">
                                <button @click="downloadPdf(selectedReport.ReportID)"
                                    class="flex-1 md:flex-none px-8 py-3 bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Export PDF
                                </button>
                                <button @click="activeTab = 'history'"
                                    class="flex-1 md:flex-none px-8 py-3 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 19l7-7 7 7M5 5l7 7 7-7"></path>
                                    </svg>
                                    Historical Archives
                                </button>
                            </div>
                        </div>

                        <!-- Valuation Preview -->
                        <template x-if="selectedReport.ReportType === 'Inventory Valuation'">
                            <div class="space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-8 bg-blue-500/5 rounded-3xl border border-blue-500/10">
                                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Total
                                            Valuation</p>
                                        <p class="text-3xl font-black text-slate-800 dark:text-white"
                                            x-text="'₱' + Number(selectedReport.Data.TotalValuation).toLocaleString()"></p>
                                    </div>
                                    <div
                                        class="p-8 bg-slate-50 dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Items Scanned</p>
                                        <p class="text-3xl font-black text-slate-800 dark:text-white"
                                            x-text="selectedReport.Data.TotalActiveItems"></p>
                                    </div>
                                </div>
                                <div class="rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                                            <tr>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase">Category</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-center">Batch
                                                    Count</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-right">Value
                                                    (PHP)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                            <template x-for="(stats, cat) in selectedReport.Data.CategoryBreakdown">
                                                <tr>
                                                    <td class="px-8 py-4 font-bold text-slate-700 dark:text-slate-300"
                                                        x-text="cat"></td>
                                                    <td class="px-8 py-4 text-center font-bold text-slate-600"
                                                        x-text="stats.count"></td>
                                                    <td class="px-8 py-4 text-right font-black text-slate-800 dark:text-white"
                                                        x-text="'₱' + Number(stats.value).toLocaleString()"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <!-- Receipt Confirmation Preview -->
                        <template x-if="selectedReport.ReportType === 'Receipt Confirmation'">
                            <div class="space-y-8">
                                <div class="p-8 bg-blue-500/5 rounded-3xl border border-blue-500/10">
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Supplier
                                        / PO Reference</p>
                                    <p class="text-xl font-black text-slate-800 dark:text-white"
                                        x-text="selectedReport.Data.Supplier + ' (' + selectedReport.Data.PONumber + ')'">
                                    </p>
                                </div>
                                <div class="rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                                            <tr>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase">Description</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-center">
                                                    Ordered</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-center">
                                                    Received</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-right">
                                                    Variance</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                            <template x-for="item in selectedReport.Data.Items">
                                                <tr>
                                                    <td class="px-8 py-4 font-bold text-slate-700 dark:text-slate-300"
                                                        x-text="item.ItemName"></td>
                                                    <td class="px-8 py-4 text-center font-bold text-slate-600"
                                                        x-text="item.Ordered"></td>
                                                    <td class="px-8 py-4 text-center font-bold text-slate-600"
                                                        x-text="item.Received"></td>
                                                    <td class="px-8 py-4 text-right font-black"
                                                        :class="item.Variance > 0 ? 'text-red-500' : 'text-blue-500'"
                                                        x-text="item.Variance"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <!-- Stock Ledger Preview -->
                        <template x-if="selectedReport.ReportType === 'Stock Card Ledger'">
                            <div class="space-y-8">
                                <div
                                    class="flex justify-between items-end p-8 bg-indigo-500/5 rounded-3xl border border-indigo-500/10">
                                    <div>
                                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2">
                                            Item Lifecycle</p>
                                        <p class="text-2xl font-black text-slate-800 dark:text-white"
                                            x-text="selectedReport.Data.ItemName"></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            Closing Balance</p>
                                        <p class="text-3xl font-black text-blue-600"
                                            x-text="selectedReport.Data.CurrentBalance + ' ' + selectedReport.Data.Unit">
                                        </p>
                                    </div>
                                </div>
                                <div class="rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                    <table class="w-full text-left text-[11px]">
                                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                                            <tr>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase">Date</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase">Type / Ref</th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-center">In
                                                </th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-center">Out
                                                </th>
                                                <th class="px-8 py-4 font-black text-slate-400 uppercase text-right">Balance
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                            <template x-for="row in selectedReport.Data.Ledger">
                                                <tr>
                                                    <td class="px-8 py-4 text-slate-600 dark:text-slate-400"
                                                        x-text="new Date(row.date).toLocaleDateString()"></td>
                                                    <td class="px-8 py-4">
                                                        <span class="font-bold text-slate-800 dark:text-white"
                                                            x-text="row.type"></span>
                                                        <span
                                                            class="block text-[9px] text-slate-400 uppercase tracking-tighter"
                                                            x-text="'Ref: ' + row.ref"></span>
                                                    </td>
                                                    <td class="px-8 py-4 text-center font-bold text-blue-600"
                                                        x-text="row.in || '-'"></td>
                                                    <td class="px-8 py-4 text-center font-bold text-red-500"
                                                        x-text="row.out || '-'"></td>
                                                    <td class="px-8 py-4 text-right font-black text-slate-900 dark:text-white"
                                                        x-text="row.balance"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Generate Tab -->
            <div x-show="activeTab === 'generate'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" class="p-10 lg:p-16">
                <div class="max-w-4xl mx-auto space-y-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start text-left">
                        <div class="space-y-6">
                            <div
                                class="w-16 h-16 rounded-[2rem] bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center text-3xl shadow-sm">
                                📊</div>
                            <h4 class="text-xl font-black text-slate-800 dark:text-white leading-tight">Report Configuration
                            </h4>
                            <p class="text-sm font-bold text-slate-500 leading-relaxed">Select the metrics you want to
                                analyze. Our system will compile data across all warehouse and health center nodes.</p>
                        </div>

                        <form @submit.prevent="generateReport" class="space-y-6">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Analytical
                                        Model</label>
                                    <select x-model="formData.reportType"
                                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                        <option value="inventory_valuation">Financial: Inventory Valuation</option>
                                        <option value="receipt_confirmation">Logistical: Receipt Confirmation</option>
                                        <option value="stock_card_ledger">Operational: Stock Card & Ledger</option>
                                    </select>
                                </div>

                                <template x-if="formData.reportType === 'receipt_confirmation'">
                                    <div x-transition class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Source
                                            Procurement Order</label>
                                        <select x-model="formData.poId" required
                                            class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                            <option value="">Select completed PO...</option>
                                            <template x-for="po in completedPOs" :key="po.POID">
                                                <option :value="po.POID" x-text="po.PONumber"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>

                                <template x-if="formData.reportType === 'stock_card_ledger'">
                                    <div x-transition class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Target
                                            Inventory Item</label>
                                        <select x-model="formData.itemId" required
                                            class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                            <option value="">Select item...</option>
                                            <template x-for="item in items" :key="item.ItemID">
                                                <option :value="item.ItemID" x-text="item.ItemName"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>

                                <div>
                                    <label
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Destination
                                        Office</label>
                                    <select x-model="formData.forOffice"
                                        class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                        <option value="accounting">Accounting Office</option>
                                        <option value="cmo">CMO Office</option>
                                        <option value="gso">GSO Office</option>
                                        <option value="coa">COA Office</option>
                                        <option value="pharmacy">Main Pharmacy</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-5 bg-slate-900 dark:bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-2xl shadow-2xl transition-all active:scale-95 disabled:opacity-50"
                                :disabled="loading">
                                <span x-show="!loading">Compile Analytical Data</span>
                                <span x-show="loading">Synthesizing...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- History Tab -->
            <div x-show="activeTab === 'history'" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
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
                                        <span
                                            class="text-[10px] font-black font-mono text-slate-400 group-hover:text-blue-600 transition-colors"
                                            x-text="rpt.ReportID"></span>
                                    </td>
                                    <td class="px-10 py-6">
                                        <p class="text-xs font-black text-slate-800 dark:text-white"
                                            x-text="rpt.ReportType"></p>
                                    </td>
                                    <td class="px-10 py-6">
                                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest"
                                            x-text="rpt.GeneratedForOffice"></p>
                                    </td>
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase"
                                                x-text="rpt.GeneratedByFullName.substring(0,1)"></div>
                                            <p class="text-[11px] font-black text-slate-700 dark:text-slate-200"
                                                x-text="rpt.GeneratedByFullName"></p>
                                        </div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <p class="text-[11px] font-black text-slate-700 dark:text-slate-200"
                                            x-text="new Date(rpt.GeneratedDate).toLocaleDateString()"></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter"
                                            x-text="new Date(rpt.GeneratedDate).toLocaleTimeString()"></p>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <button @click="viewReport(rpt.ReportID)"
                                            class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-blue-600 hover:border-blue-500/30 transition-all">
                                            👁️
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="history.length === 0">
                                <tr>
                                    <td colspan="6"
                                        class="py-20 text-center text-slate-400 font-black text-[10px] uppercase tracking-[0.3em] italic">
                                        No archived reports found</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Report View Modal -->
            <div x-show="showViewModal" style="display: none;"
                class="fixed inset-0 z-[200] grid place-items-center p-4 backdrop-blur-sm bg-slate-1200/60" x-transition.opacity x-cloak @click.self="showViewModal = false">
                <div
                    class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col max-h-[90vh]">
                    <div
                        class="px-8 pt-8 pb-6 border-b border-slate-50 dark:border-slate-700/30 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white"
                                x-text="selectedReport.ReportType"></h2>
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1"
                                x-text="'Generated for ' + selectedReport.GeneratedForOffice + ' • ' + selectedReport.ReportID">
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="downloadPdf(selectedReport.ReportID)"
                                class="px-6 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-blue-500 hover:text-white transition-all text-[10px] font-black uppercase tracking-widest rounded-xl flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Download PDF
                            </button>
                            <button @click="showViewModal = false"
                                class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                        <template x-if="selectedReport.Data">
                            <div class="space-y-8">
                                <!-- Valuation Report View -->
                                <template x-if="selectedReport.ReportType === 'Inventory Valuation'">
                                    <div class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-3xl">
                                                <p
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                    Total Valuation</p>
                                                <p class="text-3xl font-black text-blue-600"
                                                    x-text="'₱' + Number(selectedReport.Data.TotalValuation).toLocaleString()">
                                                </p>
                                            </div>
                                            <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-3xl">
                                                <p
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                    Items Scanned</p>
                                                <p class="text-3xl font-black text-slate-800 dark:text-white"
                                                    x-text="selectedReport.Data.TotalActiveItems"></p>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                            <table class="w-full text-left text-xs">
                                                <thead class="bg-slate-50 dark:bg-slate-900/50">
                                                    <tr>
                                                        <th class="px-6 py-4 font-black text-slate-400 uppercase">Category
                                                        </th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-center">
                                                            Batch Count</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-right">
                                                            Value (PHP)</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                                    <template x-for="(stats, cat) in selectedReport.Data.CategoryBreakdown">
                                                        <tr class="dark:text-slate-300">
                                                            <td class="px-6 py-4 font-bold" x-text="cat"></td>
                                                            <td class="px-6 py-4 text-center" x-text="stats.count"></td>
                                                            <td class="px-6 py-4 text-right font-black"
                                                                x-text="'₱' + Number(stats.value).toLocaleString()"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                <!-- Receipt Confirmation View -->
                                <template x-if="selectedReport.ReportType === 'Receipt Confirmation'">
                                    <div class="space-y-6">
                                        <div class="p-6 bg-slate-50 dark:bg-slate-900 rounded-3xl">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                Supplier / PO</p>
                                            <p class="text-lg font-black text-slate-800 dark:text-white"
                                                x-text="selectedReport.Data.Supplier + ' (' + selectedReport.Data.PONumber + ')'">
                                            </p>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                            <table class="w-full text-left text-xs">
                                                <thead class="bg-slate-50 dark:bg-slate-900/50">
                                                    <tr>
                                                        <th class="px-6 py-4 font-black text-slate-400 uppercase">
                                                            Description</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-center">
                                                            Ordered</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-center">
                                                            Received</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-right">
                                                            Variance</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                                    <template x-for="item in selectedReport.Data.Items">
                                                        <tr class="dark:text-slate-300">
                                                            <td class="px-6 py-4 font-bold" x-text="item.ItemName"></td>
                                                            <td class="px-6 py-4 text-center" x-text="item.Ordered"></td>
                                                            <td class="px-6 py-4 text-center" x-text="item.Received"></td>
                                                            <td class="px-6 py-4 text-right font-black"
                                                                :class="item.Variance > 0 ? 'text-red-500' : 'text-blue-500'"
                                                                x-text="item.Variance"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>

                                <!-- Stock Ledger View -->
                                <template x-if="selectedReport.ReportType === 'Stock Card Ledger'">
                                    <div class="space-y-6">
                                        <div class="flex justify-between items-end">
                                            <div>
                                                <p
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                    Item History</p>
                                                <p class="text-xl font-black text-slate-800 dark:text-white"
                                                    x-text="selectedReport.Data.ItemName"></p>
                                            </div>
                                            <div class="text-right">
                                                <p
                                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                    Closing Balance</p>
                                                <p class="text-2xl font-black text-blue-600"
                                                    x-text="selectedReport.Data.CurrentBalance + ' ' + selectedReport.Data.Unit">
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                                            <table class="w-full text-left text-[11px]">
                                                <thead class="bg-slate-50 dark:bg-slate-900/50">
                                                    <tr>
                                                        <th class="px-6 py-4 font-black text-slate-400 uppercase">Date</th>
                                                        <th class="px-6 py-4 font-black text-slate-400 uppercase">Type / Ref
                                                        </th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-center">
                                                            In</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-center">
                                                            Out</th>
                                                        <th
                                                            class="px-6 py-4 font-black text-slate-400 uppercase text-right">
                                                            Balance</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                                                    <template x-for="row in selectedReport.Data.Ledger">
                                                        <tr class="dark:text-slate-300">
                                                            <td class="px-6 py-4"
                                                                x-text="new Date(row.date).toLocaleDateString()"></td>
                                                            <td class="px-6 py-4">
                                                                <span class="font-bold" x-text="row.type"></span>
                                                                <span class="block text-[9px] text-slate-400 uppercase"
                                                                    x-text="'Ref: ' + row.ref"></span>
                                                            </td>
                                                            <td class="px-6 py-4 text-center font-bold text-blue-600"
                                                                x-text="row.in || '-'"></td>
                                                            <td class="px-6 py-4 text-center font-bold text-red-500"
                                                                x-text="row.out || '-'"></td>
                                                            <td class="px-6 py-4 text-right font-black"
                                                                x-text="row.balance"></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="px-8 pb-8">
                        <button @click="showViewModal = false"
                            class="w-full py-4 bg-slate-900 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 transition-all">Close
                            Viewer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function reportsManager() {
            return {
                activeTab: 'generate',
                loading: false,
                showViewModal: false,
                selectedReport: {},
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
                            // Fetch full data for preview
                            await this.viewReport(result.report.ReportID, true);
                            this.activeTab = 'preview';
                            alert('Report generated and archived!');
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (err) {
                        alert('Connection error');
                    } finally {
                        this.loading = false;
                    }
                },

                async viewReport(id, isPreview = false) {
                    try {
                        const response = await fetch(`/reports/${id}`);
                        const result = await response.json();
                        if (result.success) {
                            this.selectedReport = result.report;
                            // Ensure Data is parsed if it came as string
                            if (typeof this.selectedReport.Data === 'string') {
                                this.selectedReport.Data = JSON.parse(this.selectedReport.Data);
                            }
                            if (!isPreview) {
                                this.showViewModal = true;
                            }
                        }
                    } catch (err) {
                        alert('Could not fetch report details');
                    }
                },

                downloadPdf(id) {
                    window.location.href = `/reports/${id}/export`;
                }
            }
        }
    </script>

@endsection