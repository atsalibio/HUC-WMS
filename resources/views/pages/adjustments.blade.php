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
                :class="adjustTab === 'recall'
                    ? 'bg-red-500 text-white shadow-lg shadow-red-500/30 scale-[1.03]'
                    : 'text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10'"
                class="flex items-center gap-2.5 px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest transition-all duration-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in disposals" :key="log.DisposalID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.QuantityDisposed"></td>
                                        <td class="px-4 py-4 text-xs uppercase font-black tracking-widest text-red-500" x-text="log.DisposalType || 'Disposal'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.DisposalDate).toLocaleDateString()"></td>
                                    </tr>
                                </template>
                                <template x-if="disposals.length === 0">
                                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No disposal history yet.</td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Recall Tab Content -->
        <div x-show="adjustTab === 'recall'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2">

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
                            <div class="grid gap-4">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Source Health Center Batch</label>
                                <select x-model="returnObj.hcBatch" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                    <option value="">Select item...</option>

                                    <template x-for="batch in hcBatches" :key="batch.HCBatchID">
                                        <option :value="batch.HCBatchID" @change="returnObj.maxQuantity = batch.QuantityOnHand" x-text="`${batch.item.ItemName} : ${batch.HCBatchNumber != 0 ? batch.HCBatchNumber : ('Batch: ' + batch.BatchID)}`"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Qty to Dispose</label>
                                    <input type="number" x-model="returnObj.maxQuantity" max="returnObj.maxQuantity" min="1" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white" placeholder="1">
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
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Health Center</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in returns" :key="log.ReturnID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.batch?.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.QuantityReturned"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.healthCenter?.Name || '—'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.ReturnDate).toLocaleDateString()"></td>
                                    </tr>
                                </template>
                                <template x-if="returns.length === 0">
                                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No return history yet.</td></tr>
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
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Quantity Adjustment (+/-)</label>
                                <input type="number" x-model="correction.quantity" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="e.g. -5 or 10">
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
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Item</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reason</th>
                                    <th class="px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <template x-for="log in corrections" :key="log.AdjustmentID">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-4 text-xs font-bold text-slate-700 dark:text-slate-200" x-text="log.batch?.item?.ItemName || 'Unknown'"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400" x-text="log.AdjustmentQuantity"></td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400 truncate" x-text="log.Reason"></td>
                                        <td class="px-4 py-4 text-xs text-slate-400" x-text="new Date(log.AdjustmentDate).toLocaleDateString()"></td>
                                    </tr>
                                </template>
                                <template x-if="corrections.length === 0">
                                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-[10px] uppercase tracking-widest">No correction history yet.</td></tr>
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
</div>

<script>
function adjustmentManager() {
    return {
        activeTab: 'history',
        adjustTab: 'disposal',
        items: @json($items),
        inventory: @json($inventory),
        hcBatches: @json($hcBatches),
        requisitions: @json($requisitions),
        history: @json($history),
        disposals: @json($disposals),
        returns: @json($returns),
        corrections: @json($corrections),

        disposal: { itemId: '', batchId: '', quantity: 1, disposalType: 'Damaged', remarks: '', photo: null },
        filteredDisposalBatches: [],

        correction: { itemId: '', batchId: '', quantityCorrected: 1, quantity: 1, reason: ''},
        filteredCorrectionBatches: [],

        returnObj: {hcBatch: '', reason: '', photo: null, quantity: 1, maxQuantity: 1},
        filteredReturnItems: [],
        filteredRecallItems: [],

        init() {
            //
        },

        updateDisposalBatches() {
            this.disposal.batchId = '';
            this.filteredDisposalBatches = this.inventory.filter(b => b.ItemID == this.disposal.itemId);
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

            console.log('Submitting return with data:', this.returnObj);

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
                if (result.success) { alert('Return Request submitted!'); location.reload(); }
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
                if (result.success) { alert('Correction applied!'); location.reload(); }
                else { alert('Error: ' + result.message); }
            } catch (e) { alert('Connection error'); }
        }
    }
}
</script>
@endsection
