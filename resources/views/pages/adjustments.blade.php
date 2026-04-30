@extends('layouts.app', ['currentPage' => 'adjustments'])

@section('content')
<div class="space-y-6" x-data="adjustmentManager()">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 px-4">
        <div class="space-y-1">
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight"
                x-text="activeTab === 'perform' ? 'Stock Adjustment' : 'Correction Logs'">
            </h3>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1.5"
               x-text="activeTab === 'perform' ? 'Manage disposals and item returns' : 'History of all inventory modifications'"></p>
        </div>

        <div class="inline-flex p-1.5 bg-slate-200/50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800">
            <button @click="activeTab = 'perform'"
                :class="activeTab === 'perform' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                Perform Adjustment
            </button>
            <button @click="activeTab = 'history'"
                :class="activeTab === 'history' ? 'bg-white dark:bg-slate-800 text-blue-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 ml-2">
                History Logs
            </button>
        </div>
    </div>

    <!-- Perform Adjustment Tab Content -->
    <div x-show="activeTab === 'perform'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">

        <!-- Sub-Tab Switcher -->
        <div class="flex gap-2 p-1.5 bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200/60 dark:border-slate-700/60 w-fit">
            <button @click="adjustTab = 'disposal'"
                :class="adjustTab === 'disposal'
                    ? 'bg-red-500 text-white shadow-lg shadow-red-500/30 scale-[1.03]'
                    : 'text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10'"
                class="flex items-center gap-2.5 px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Disposal
            </button>
            <button @click="adjustTab = 'recall'"
                x-show="'{{ Auth::user()->Role }}' === 'Head Pharmacist' || '{{ Auth::user()->HealthCenterID }}'"
                :class="adjustTab === 'recall'
                    ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30 scale-[1.03]'
                    : 'text-slate-400 hover:text-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/10'"
                class="flex items-center gap-2.5 px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Recall
            </button>
            <button @click="adjustTab = 'return'"
                :class="adjustTab === 'return'
                    ? 'bg-blue-500 text-white shadow-lg shadow-blue-500/30 scale-[1.03]'
                    : 'text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10'"
                class="flex items-center gap-2.5 px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Return
            </button>
            <button @click="adjustTab = 'correction'"
                :class="adjustTab === 'correction'
                    ? 'bg-slate-800 dark:bg-blue-600 text-white shadow-lg shadow-slate-800/20 scale-[1.03]'
                    : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                class="flex items-center gap-2.5 px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Audit Correction
            </button>
        </div>

        <!-- Disposal Tab Content -->
        <div x-show="adjustTab === 'disposal'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_372px] gap-8">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20 text-red-500 flex items-center justify-center font-black text-xl">
                            🗑️
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-800 dark:text-white">Inventory Disposal</h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Damaged, Expired, or Lost stock</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitDisposal" class="space-y-6">
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Select Item</label>
                                    <select x-model="disposal.itemId" @change="updateDisposalBatches" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white">
                                        <option value="">Choose item...</option>
                                        <template x-for="item in items" :key="item.ItemID">
                                            <option :value="item.ItemID" x-text="item.ItemName"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Select Batch</label>
                                    <select x-model="disposal.batchId" required :disabled="!disposal.itemId" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white disabled:opacity-50">
                                        <option value="">Choose batch...</option>
                                        <template x-for="batch in filteredDisposalBatches" :key="batch.BatchID">
                                            <option :value="batch.BatchID" x-text="`${batch.BatchID} (${batch.QuantityOnHand} avail)`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Qty to Dispose</label>
                                    <input type="number" x-model="disposal.quantity" min="1" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white" placeholder="1">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Disposal Type</label>
                                    <select x-model="disposal.disposalType" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white">
                                        <option value="Damaged">Damaged</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Lost">Lost</option>
                                        <option value="Recall">Quality Recall</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Upload Proof (Photo)</label>
                                <input type="file" @change="disposal.photo = $event.target.files[0]" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-all cursor-pointer">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-red-500 hover:bg-red-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-red-500/20 transition-all active:scale-95">
                            Finalize Disposal
                        </button>
                    </form>
                </div>

                <aside class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-base font-black text-slate-800 dark:text-white">Recent Disposals</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Latest logged disposal adjustments.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Disposal Ref No</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in disposals" :key="log.DisposalID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.ReferrenceNo"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.StatusType"></td>
                                        <td class="px-4 py-4 text-xs uppercase font-black tracking-widest text-red-500" x-text="log.DisposalType || 'Disposal'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.DisposalDate).toLocaleDateString()"></td>
                                        <td class="px-4 py-4 text-center"><button @click="openDisposalDetail(log)" class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                </template>
                                <template x-if="disposals.length === 0">
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No disposal history yet.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Recall Tab Content -->
        <div x-show="adjustTab === 'recall'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_372px] gap-8">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 text-orange-500 flex items-center justify-center font-black text-xl">
                            ⚠️
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-800 dark:text-white">Product Recall</h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recall products from health centers</p>
                        </div>
                    </div>

                    @if(Auth::user()->Role === 'Head Pharmacist')
                    <form @submit.prevent="submitRecall" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select Item</label>
                                <select x-model="recall.itemId" @change="updateRecallBatches" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-orange-500/20 transition-all dark:text-white">
                                    <option value="">Choose item...</option>
                                    <template x-for="item in items" :key="item.ItemID">
                                        <option :value="item.ItemID" x-text="item.ItemName"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select Batch</label>
                                <select x-model="recall.batchId" @change="getHealthCentersForRecall" required :disabled="!recall.itemId" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-orange-500/20 transition-all dark:text-white disabled:opacity-50">
                                    <option value="">Choose batch...</option>
                                    <template x-for="batch in filteredRecallBatches" :key="batch.BatchID">
                                        <option :value="batch.BatchID" x-text="`${batch.BatchID} (${batch.QuantityOnHand} avail)`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Health Centers with This Batch</label>
                            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 max-h-48 overflow-y-auto">
                                <template x-if="recallHealthCenters.length === 0 && recall.batchId">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 text-center py-4">No health centers have this batch.</p>
                                </template>
                                <template x-if="recallHealthCenters.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="hc in recallHealthCenters" :key="hc.HCBatchID">
                                            <label class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                                <input type="checkbox" :value="hc.HCBatchID" x-model="recall.selectedHCBatches" class="rounded border-slate-300 text-orange-500 focus:ring-orange-500">
                                                <div class="flex-1">
                                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200" x-text="hc.Name"></span>
                                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 ml-2" x-text="`(${hc.QuantityOnHand} units)`"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Recall Reason</label>
                            <textarea x-model="recall.reason" rows="3" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-orange-500/20 transition-all dark:text-white" placeholder="Why is this product being recalled?"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-orange-500/20 transition-all active:scale-95 disabled:opacity-50" :disabled="!recall.batchId || recall.selectedHCBatches.length === 0">
                            Initiate Recall Order
                        </button>
                    </form>
                    @endif

                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] p-6 border border-slate-200/60 dark:border-slate-700/60">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white">Recall Orders</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">All recall orders and their details.</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[10px]">
                                <thead class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Item</th>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Batch</th>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Qty</th>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Reason</th>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Requested By</th>
                                        <th class="px-3 py-3 font-black text-slate-400 uppercase tracking-widest">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                    <template x-for="recall in recalls" :key="recall.RecallOrderID">
                                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                            <td class="px-3 py-3 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="recall.item?.ItemName || 'Unknown'"></td>
                                            <td class="px-3 py-3 text-xs text-slate-500 dark:text-slate-400" x-text="recall.BatchID"></td>
                                            <td class="px-3 py-3 text-xs text-slate-500 dark:text-slate-400" x-text="recall.QuantityOnRecall"></td>
                                            <td class="px-3 py-3 text-xs text-slate-500 dark:text-slate-400" x-text="recall.Reason || 'N/A'"></td>
                                            <td class="px-3 py-3 text-xs text-slate-500 dark:text-slate-400" x-text="recall.user?.Name || 'System'"></td>
                                            <td class="px-3 py-3 text-xs text-slate-400" x-text="new Date(recall.RecallDate).toLocaleDateString()"></td>
                                        </tr>
                                    </template>
                                    <template x-if="recalls.length === 0">
                                        <tr><td colspan="6" class="px-3 py-6 text-center text-slate-400 uppercase tracking-widest">No recall orders yet.</td></tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(Auth::user()->HealthCenterID)
                    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center font-black text-xl">
                                📦
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white">Recall Fulfillment</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fulfill recall orders from your health center inventory.</p>
                            </div>
                        </div>
                        <form @submit.prevent="submitRecallFulfillment" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select Recall Order</label>
                                    <select x-model="recallFulfillment.recallOrderId" @change="updateRecallFulfillmentOrders" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                        <option value="">Choose recall order...</option>
                                        <template x-for="order in availableRecallOrders" :key="order.RecallOrderID">
                                            <option :value="order.RecallOrderID" x-text="`${order.item?.ItemName || 'Unknown'} — ${order.BatchID} (${order.QuantityOnRecall} qty)`"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select Inventory Batch</label>
                                    <select x-model="recallFulfillment.hcBatchId" @change="updateRecallFulfillmentQuantity" required :disabled="filteredFulfillmentHCBatches.length === 0" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white disabled:opacity-50">
                                        <option value="">Choose batch...</option>
                                        <template x-for="batch in filteredFulfillmentHCBatches" :key="batch.HCBatchID">
                                            <option :value="batch.HCBatchID" x-text="`${batch.HCBatchID} (${batch.QuantityOnHand} on hand)`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Quantity Fulfilled</label>
                                    <input type="number" x-model.number="recallFulfillment.quantityFulfilled" min="1" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="Quantity fulfilled">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Upload Photo</label>
                                    <input type="file" @change="recallFulfillment.photo = $event.target.files[0]" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all cursor-pointer">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 bg-blue-500 hover:bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 disabled:opacity-50" :disabled="!recallFulfillment.recallOrderId || !recallFulfillment.hcBatchId || !recallFulfillment.quantityFulfilled">
                                Submit Recall Fulfillment
                            </button>
                        </form>
                        <template x-if="availableRecallOrders.length === 0">
                            <p class="text-xs text-slate-500 dark:text-slate-400">No recall orders are currently available for your inventory.</p>
                        </template>
                    </div>
                    @endif
                </div>

                <aside class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-base font-black text-slate-800 dark:text-white">Recent Recalls</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Latest recall orders.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Quantity</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="recall in recalls" :key="recall.RecallOrderID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="recall.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="recall.BatchID"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="recall.QuantityOnRecall"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(recall.RecallDate).toLocaleDateString()"></td>
                                    </tr>
                                </template>
                                <template x-if="recalls.length === 0">
                                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No recall orders yet.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Returns Tab Content -->
        <div x-show="adjustTab === 'return'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_372px] gap-8">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center font-black text-xl">
                            ↩️
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-800 dark:text-white">Inventory Return</h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Return stock from completed issuances</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitReturn" class="space-y-6">
                        <div class="space-y-5 ">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Select Item</label>
                                    <select x-model="returnObj.itemId" @change="updateReturnBatches" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white">
                                        <option value="">Choose item...</option>
                                        <template x-for="item in items" :key="item.ItemID">
                                            <option :value="item.ItemID" x-text="item.ItemName"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Select Batch</label>
                                    <select x-model="returnObj.hcBatch" required :disabled="!returnObj.itemId" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white disabled:opacity-50">
                                        <option value="">Choose batch...</option>
                                        <template x-for="batch in filteredReturnBatches" :key="batch.HCBatchID">
                                            <option :value="batch.HCBatchID" x-text="`${batch.BatchID} (${batch.QuantityOnHand} avail)`"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Quantity to Return</label>
                                    <input type="number" x-model="returnObj.quantity" min="1" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white" placeholder="1">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Photo Proof</label>
                                    <input type="file" @change="returnObj.photo = $event.target.files[0]" accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-all cursor-pointer">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Return Reason</label>
                                <textarea x-model="returnObj.reason" rows="3" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="Why are these items being returned?"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-blue-500 hover:bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 disabled:opacity-50" :disabled="!returnObj.hcBatch">
                            Process Return
                        </button>
                    </form>
                </div>

                <aside class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-base font-black text-slate-800 dark:text-white">Recent Returns</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Latest inventory return records.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Return Ref No</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Health Center</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in returns" :key="log.ReturnID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.batch?.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.QuantityReturned"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.health_center?.Name || '—'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.ReturnDate).toLocaleDateString()"></td>
                                        <td class="px-4 py-4 text-center"><button @click="openReturnDetail(log)" class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                </template>
                                <template x-if="returns.length === 0">
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No return history yet.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Audit Correction Tab Content -->
        <div x-show="adjustTab === 'correction'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_372px] gap-8">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center font-black text-xl">
                            🔧
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-800 dark:text-white">Audit Correction</h4>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Generic stock level adjustments (Increases or Decreases)</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitCorrection" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-end">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Target Item</label>
                                <select x-model="correction.itemId" @change="updateCorrectionBatches" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                    <option value="">Choose item...</option>
                                    <template x-for="item in items" :key="item.ItemID">
                                        <option :value="item.ItemID" x-text="item.ItemName"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Target Batch</label>
                                <select x-model="correction.batchId" required :disabled="!correction.itemId" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white disabled:opacity-50">
                                    <option value="">Choose batch...</option>
                                    <template x-for="batch in filteredCorrectionBatches" :key="batch.BatchID">
                                        <option :value="batch.BatchID" x-text="`${batch.BatchID} (${batch.QuantityOnHand} avail)`"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Correction</label>
                                <input type="number" x-model="correction.quantity" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="New Value">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Audit Reason</label>
                            <input type="text" x-model="correction.reason" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="e.g. Stock count mismatch">
                        </div>
                        <button type="submit" class="w-full py-4 bg-slate-900 dark:bg-blue-600 hover:scale-[1.02] text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl transition-all active:scale-95">
                            Apply Correction
                        </button>
                    </form>
                </div>

                <aside class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-base font-black text-slate-800 dark:text-white">Recent Corrections</h4>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Latest stock correction entries.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 dark:bg-slate-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Correction Ref No.</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Origin</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in corrections" :key="log.AdjustmentID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.ReferrenceNo"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.WarehouseID ? log.warehouse?.WarehouseName : log.health_center?.Name"></td>
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.StatusType"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400 truncate" x-text="log.batch?.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.AdjustmentDate).toLocaleDateString()"></td>
                                        <td class="px-4 py-4 text-center"><button @click="openCorrectionDetail(log)" class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button></td>
                                    </tr>
                                </template>
                                <template x-if="corrections.length === 0">
                                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No correction history yet.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <!-- History Tab Content -->
    <div x-show="activeTab === 'history'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" class="animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-[3rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Target Item</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Qty ∆</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Reason / Date</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest">Adjusted By</th>
                            <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/50 dark:divide-slate-700/50">
                        <template x-for="log in history" :key="log.AdjustmentID">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg" :class="{
                                        'bg-red-50 text-red-500': log.AdjustmentType === 'Disposal',
                                        'bg-blue-50 text-blue-500': log.AdjustmentType === 'Return',
                                        'bg-blue-50 text-blue-600': log.AdjustmentType === 'Correction'
                                    }" x-text="log.AdjustmentType"></span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-black text-slate-800 dark:text-white" x-text="log.batch?.item?.ItemName || 'Unknown Item'"></p>
                                    <p class="text-[10px] font-bold text-slate-400 font-mono tracking-tighter" x-text="log.batch.BatchNumber"></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-sm font-black" :class="log.AdjustmentQuantity < 0 ? 'text-red-500' : 'text-blue-600'" x-text="log.AdjustmentQuantity"></p>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400" x-text="log.Reason"></p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="new Date(log.AdjustmentDate).toLocaleDateString()"></p>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="px-5 py-2 rounded-full text-[9px] font-black uppercase tracking-widest inline-block"
                                        :class="log ? getStatusClass(log.StatusType) : ''"
                                        x-text="log ? log.StatusType : 'N/A'"></span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 flex items-center justify-center text-[8px] font-black">
                                            HP
                                        </div>
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-200" x-text="log.user?.FName + ' ' + log.user?.LName"></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <template x-if="log.EvidencePath">
                                        <div class="group/img relative w-12 h-12 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm float-right">
                                            <img :src="'/' + log.EvidencePath" class="w-full h-full object-cover">
                                            <a :href="'/' + log.EvidencePath" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template x-if="history.length === 0">
                            <tr><td colspan="6" class="py-20 text-center text-slate-400 font-black text-[10px] uppercase tracking-[0.3em] italic">No adjustment logs found</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Detail Modal -->
    <div x-show="detailModal.isOpen" x-cloak class="fixed inset-0 bg-slate-900/50 dark:bg-slate-900/70 z-50 flex items-center justify-center p-4" @click.self="closeDetailModal()">
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
            <!-- Disposal Detail Modal -->
            <template x-if="detailModal.type === 'disposal' && detailModal.data">
                <div class="p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-6">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">Disposal Details</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View and manage this disposal record</p>
                        </div>
                        <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Item</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.item?.ItemName || 'Unknown'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Quantity Disposed</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.QuantityDisposed"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Disposal Type</label>
                            <p class="text-sm font-bold text-red-500 uppercase" x-text="detailModal.data.DisposalType"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Date</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="new Date(detailModal.data.DisposalDate).toLocaleDateString()"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Remarks</label>
                            <p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailModal.data.Remarks || 'No remarks'"></p>
                        </div>
                        <template x-if="detailModal.data.EvidencePath">
                            <div class="col-span-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Evidence Photo</label>
                                <img :src="'/' + detailModal.data.EvidencePath" class="max-h-64 rounded-xl border border-slate-200 dark:border-slate-700">
                            </div>
                        </template>
                    </div>
                    <template x-if="userRole === 'Head Pharmacist' && detailModal.data.StatusType === 'Pending'">
                        <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button @click="updateTransactionStatus(detailModal.data.DisposalID, 'Approved', 'disposal')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Approve
                            </button>
                            <button @click="updateTransactionStatus(detailModal.data.DisposalID, 'Rejected', 'disposal')" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Reject
                            </button>
                        </div>
                    </template>
                    <template x-if="!userHealthCenterID && detailModal.data.StatusType === 'Approved'">
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button @click="updateTransactionStatus(detailModal.data.DisposalID, 'Completed', 'disposal')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Mark as Completed
                            </button>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Return Detail Modal -->
            <template x-if="detailModal.type === 'return' && detailModal.data">
                <div class="p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-6">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">Return Details</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View and manage this return record</p>
                        </div>
                        <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Item</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.batch?.item?.ItemName || 'Unknown'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Quantity Returned</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.QuantityReturned"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Health Center</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.health_center?.Name || '—'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Date</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="new Date(detailModal.data.ReturnDate).toLocaleDateString()"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Reason</label>
                            <p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailModal.data.Reason || 'No reason provided'"></p>
                        </div>
                        <template x-if="detailModal.data.EvidencePath">
                            <div class="col-span-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Evidence Photo</label>
                                <img :src="'/' + detailModal.data.EvidencePath" class="max-h-64 rounded-xl border border-slate-200 dark:border-slate-700">
                            </div>
                        </template>
                    </div>
                    <template x-if="userRole === 'Head Pharmacist' && detailModal.data.StatusType === 'Pending'">
                        <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button @click="updateTransactionStatus(detailModal.data.ReturnID, 'Approved', 'return')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Approve
                            </button>
                            <button @click="updateTransactionStatus(detailModal.data.ReturnID, 'Rejected', 'return')" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Reject
                            </button>
                        </div>
                    </template>
                    <template x-if="!userHealthCenterID && detailModal.data.StatusType === 'Approved'">
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button @click="updateTransactionStatus(detailModal.data.ReturnID, 'Completed', 'return')" class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Mark as Completed
                            </button>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Correction Detail Modal -->
            <template x-if="detailModal.type === 'correction' && detailModal.data">
                <div class="p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-6">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white">Correction Details</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">View and manage this correction record</p>
                        </div>
                        <button @click="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Item</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.batch?.item?.ItemName || 'Unknown'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Adjustment Quantity</label>
                            <p class="text-sm font-bold" :class="detailModal.data.QuantityCorrected < 0 ? 'text-red-500' : 'text-green-500'" x-text="detailModal.data.QuantityCorrected"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Quantity Before</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="detailModal.data.QuantityBefore"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Date</label>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="new Date(detailModal.data.CorrectionDate).toLocaleDateString()"></p>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Reason</label>
                            <p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailModal.data.Reason || 'No reason provided'"></p>
                        </div>
                    </div>
                    <template x-if="userRole === 'Head Pharmacist' && detailModal.data.StatusType === 'Pending'">
                        <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <button @click="updateTransactionStatus(detailModal.data.CorrectionID, 'Approved', 'correction')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Approve
                            </button>
                            <button @click="updateTransactionStatus(detailModal.data.CorrectionID, 'Rejected', 'correction')" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all">
                                Reject
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function adjustmentManager() {
    return {
        activeTab: 'history',
        adjustTab: 'disposal',
        items: @json($items),
        inventory: @json($inventory),
        requisitions: @json($requisitions),
        history: @json($history),
        disposals: @json($disposals),
        returns: @json($returns),
        corrections: @json($corrections),
        recalls: @json($recalls),

        disposal: { itemId: '', batchId: '', quantity: 1, disposalType: 'Damaged', remarks: '', photo: null },
        filteredDisposalBatches: [],

        correction: { itemId: '', batchId: '', quantityCorrected: 1, quantity: 1, reason: ''},
        filteredCorrectionBatches: [],

        returnObj: {itemId: '', hcBatch: '', reason: '', photo: null, quantity: 1, maxQuantity: 1},
        filteredReturnBatches: [],
        filteredRecallItems: [],

        recall: { itemId: '', batchId: '', selectedHCBatches: [], reason: '' },
        filteredRecallBatches: [],
        recallHealthCenters: [],
        availableRecallOrders: [],
        recallFulfillment: { recallOrderId: '', hcBatchId: '', quantityFulfilled: 0, photo: null },
        filteredFulfillmentHCBatches: [],

        detailModal: { isOpen: false, type: null, data: null },
        userRole: '{{ Auth::user()->Role }}',
        userHealthCenterID: '{{ Auth::user()->HealthCenterID }}' ?? null,

        init() {
            this.refreshRecallFulfillmentOrders();
        },

        updateDisposalBatches() {
            this.disposal.batchId = '';
            this.filteredDisposalBatches = this.inventory.filter(b => b.ItemID == this.disposal.itemId);
        },

        updateReturnBatches() {
            this.returnObj.hcBatch = '';
            this.filteredReturnBatches = this.inventory.filter(b => b.ItemID == this.returnObj.itemId);
            console.log('filtered', this.filteredReturnBatches);
        },

        getStatusClass(status) {
            return {
                'Pending': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                'Approved': 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                'Rejected': 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                'Completed': 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
            }[status] || 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
        },

        updateCorrectionBatches() {
            this.correction.batchId = '';
            this.filteredCorrectionBatches = this.inventory.filter(b => b.ItemID == this.correction.itemId);
        },

        filteredHistoryByType(type) {
            return this.history.filter(log => log.AdjustmentType === type).slice(0, 6);
        },

        async submitDisposal() {
            const formData = new FormData();
            formData.append('batchId', this.disposal.batchId);
            formData.append('quantity', this.disposal.quantity);
            formData.append('disposalType', this.disposal.disposalType);
            formData.append('remarks', this.disposal.remarks);
            if (this.disposal.photo) formData.append('photo', this.disposal.photo);

            try {
                const response = await fetch('/adjustments/disposal', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    alert('Disposal logged!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Validation failed. Check file size/type.'));
                    console.error(result.errors);
                }
            } catch (e) { alert('Connection error'); }
        },

        async submitReturn() {

            console.log("return:", this.returnObj);

            const payload = {
                hcBatchId: this.returnObj.hcBatch,
                reason: this.returnObj.reason,
                photo: this.returnObj.photo,
                quantity: this.returnObj.quantity,
            };

            try {
                const response = await fetch('/adjustments/return', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) { alert('Return Request submitted!'); /*location.reload();*/ }
                else { alert('Error: ' + result.message); }
            } catch (e) { alert(e); }
        },

        async submitCorrection() {
            if (this.correction.quantity == 0) { alert('Adjustment quantity cannot be zero'); return; }

            const payload = {
                batchId: this.correction.batchId,
                quantity: this.correction.quantity,
                reason: this.correction.reason,
                remarks: this.correction.remarks
            };

            try {
                const response = await fetch('/adjustments/correction', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) { alert('Correction applied!'); /*location.reload();*/ }
                else { alert('Error: ' + result.message); }
            } catch (e) { alert(e); }
        },

        updateRecallBatches() {
            this.recall.batchId = '';
            this.recall.selectedHCBatches = [];
            this.recallHealthCenters = [];
            this.filteredRecallBatches = this.inventory.filter(b => b.ItemID == this.recall.itemId);
        },

        refreshRecallFulfillmentOrders() {
            const inventoryBatchIds = this.inventory.map(batch => batch.BatchID);
            this.availableRecallOrders = this.recalls.filter(order => inventoryBatchIds.includes(order.BatchID));
        },

        updateRecallFulfillmentOrders() {
            this.recallFulfillment.hcBatchId = '';
            this.recallFulfillment.quantityFulfilled = 0;

            const order = this.recalls.find(r => r.RecallOrderID == this.recallFulfillment.recallOrderId);
            this.filteredFulfillmentHCBatches = order ? this.inventory.filter(batch => batch.BatchID == order.BatchID) : [];
        },

        updateRecallFulfillmentQuantity() {
            const hcBatch = this.inventory.find(batch => batch.HCBatchID == this.recallFulfillment.hcBatchId);
            this.recallFulfillment.quantityFulfilled = hcBatch ? hcBatch.QuantityOnHand : 0;
        },

        async submitRecallFulfillment() {
            if (!this.recallFulfillment.recallOrderId || !this.recallFulfillment.hcBatchId) {
                alert('Please select a recall order and an inventory batch.');
                return;
            }

            if (this.recallFulfillment.quantityFulfilled < 1) {
                alert('Quantity fulfilled must be at least 1.');
                return;
            }

            const formData = new FormData();
            formData.append('recallOrderId', this.recallFulfillment.recallOrderId);
            formData.append('hcBatchId', this.recallFulfillment.hcBatchId);
            formData.append('quantityFulfilled', this.recallFulfillment.quantityFulfilled);
            if (this.recallFulfillment.photo) {
                formData.append('photo', this.recallFulfillment.photo);
            }

            try {
                const response = await fetch('/adjustments/recall/fulfillment', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Recall fulfillment submitted successfully!');
                    this.recallFulfillment = { recallOrderId: '', hcBatchId: '', quantityFulfilled: 0, photo: null };
                    this.filteredFulfillmentHCBatches = [];
                } else {
                    alert('Error: ' + (result.message || 'Validation failed.'));
                }
            } catch (e) {
                alert('Connection error');
            }
        },

        async getHealthCentersForRecall() {
            if (!this.recall.batchId) return;

            try {
                const response = await fetch(`/adjustments/recall/health-centers/${this.recall.batchId}`);
                const result = await response.json();
                if (result.success) {
                    this.recallHealthCenters = result.healthCenters;
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (e) { alert(e); }
        },

        async submitRecall() {
            if (this.recall.selectedHCBatches.length === 0) { alert('Please select at least one health center'); return; }

            const payload = {
                batchId: this.recall.batchId,
                selectedHCBatches: this.recall.selectedHCBatches,
                reason: this.recall.reason
            };

            try {
                const response = await fetch('/adjustments/recall', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) {
                    alert('Recall order created successfully!');
                    this.recall = { itemId: '', batchId: '', selectedHCBatches: [], reason: '' };
                    this.recallHealthCenters = [];
                    // Optionally refresh recalls list
                    // location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (e) { alert(e); }
        },

        openDisposalDetail(disposal) {
            this.detailModal.type = 'disposal';
            this.detailModal.data = disposal;
            this.detailModal.isOpen = true;
        },

        openReturnDetail(returnRecord) {
            this.detailModal.type = 'return';
            this.detailModal.data = returnRecord;
            this.detailModal.isOpen = true;
        },

        openCorrectionDetail(correction) {
            this.detailModal.type = 'correction';
            this.detailModal.data = correction;
            this.detailModal.isOpen = true;

            console.log(this.detailModal)
        },

        closeDetailModal() {
            this.detailModal.isOpen = false;
            this.detailModal.type = null;
            this.detailModal.data = null;
        },

        async updateTransactionStatus(transactionId, status, type) {
            try {
                const response = await fetch(`/adjustments/${type}/${transactionId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body:
                        JSON.stringify({ status: status })
                });
                const result = await response.json();
                if (result.success) {
                    alert(`Transaction ${status.toLowerCase()}!`);
                    this.closeDetailModal();
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (e) {
                alert('Error: ' + result.message);
            }
        }
    }
}
</script>
@endsection
