@extends('layouts.app', ['currentPage' => 'adjustments'])

@section('content')
<div class="space-y-6" x-data="adjustmentManager()">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="space-y-1">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1" 
                x-text="activeTab === 'perform' ? 'Stock Adjustment' : 'Correction Logs'">
            </h3>
            <p class="text-slate-500 font-bold text-xs uppercase tracking-widest" 
               x-text="activeTab === 'perform' ? 'Manage disposals and item returns' : 'History of all inventory modifications'"></p>
        </div>

        <div class="inline-flex p-1.5 bg-slate-200/50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800">
            <button @click="activeTab = 'perform'" 
                :class="activeTab === 'perform' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                Perform Adjustment
            </button>
            <button @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-xl scale-105' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 shadow-sm'"
                class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 ml-2">
                History Logs
            </button>
        </div>
    </div>

    <!-- Perform Adjustment Tab Content -->
    <div x-show="activeTab === 'perform'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Disposal Section -->
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
                                <select x-model="disposal.reason" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500/20 transition-all dark:text-white">
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

            <!-- Returns Section -->
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
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Source Requisition</label>
                            <select x-model="returnObj.requisitionId" @change="updateReturnItems" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white">
                                <option value="">Select source...</option>
                                <template x-for="req in requisitions" :key="req.RequisitionID">
                                    <option :value="req.RequisitionID" x-text="`${req.RequisitionNumber} - ${req.health_center?.Name}`"></option>
                                </template>
                            </select>
                        </div>

                        <div x-show="returnObj.requisitionId" x-transition class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Select Items to Return</label>
                            <template x-for="item in filteredReturnItems" :key="item.id">
                                <div class="flex items-center gap-4 p-4 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                                    <input type="checkbox" x-model="item.selected" class="w-5 h-5 rounded-lg border-none bg-white dark:bg-slate-800 text-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-slate-700 dark:text-slate-300" x-text="item.name"></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase" x-text="'Batch: ' + item.batchId"></p>
                                    </div>
                                    <input type="number" x-model="item.qty" :max="item.issuedQty" min="1" class="w-20 bg-white dark:bg-slate-800 border-none rounded-xl px-3 py-2 text-center text-xs font-black dark:text-white">
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Return Reason</label>
                            <textarea x-model="returnObj.reason" rows="3" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-blue-500/20 transition-all dark:text-white" placeholder="Why are these items being returned?"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-500 hover:bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 disabled:opacity-50" :disabled="!returnObj.requisitionId">
                        Process Return
                    </button>
                </form>
            </div>
        </div>

        <!-- Manual Correction Section -->
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden p-10 space-y-8 mt-12">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/20 text-teal-600 flex items-center justify-center font-black text-xl">
                    🔧
                </div>
                <div>
                    <h4 class="text-lg font-black text-slate-800 dark:text-white">Audit Correction</h4>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Generic stock level adjustments (Increases or Decreases)</p>
                </div>
            </div>

            <form @submit.prevent="submitCorrection" class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Target Item</label>
                    <select x-model="correction.itemId" @change="updateCorrectionBatches" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white">
                        <option value="">Choose item...</option>
                        <template x-for="item in items" :key="item.ItemID">
                            <option :value="item.ItemID" x-text="item.ItemName"></option>
                        </template>
                    </select>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Target Batch</label>
                    <select x-model="correction.batchId" required :disabled="!correction.itemId" class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white disabled:opacity-50">
                        <option value="">Choose batch...</option>
                        <template x-for="batch in filteredCorrectionBatches" :key="batch.BatchID">
                            <option :value="batch.BatchID" x-text="`${batch.BatchID} (${batch.QuantityOnHand} avail)`"></option>
                        </template>
                    </select>
                </div>
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Quantity Adjustment (+/-)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="correction.quantity" required class="flex-1 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white" placeholder="e.g. -5 or 10">
                        <button type="submit" class="px-8 py-4 bg-slate-900 dark:bg-teal-600 hover:scale-[1.02] text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-xl transition-all active:scale-95">
                            Apply
                        </button>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Audit Reason</label>
                    <input type="text" x-model="correction.reason" required class="w-full bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-teal-500/20 transition-all dark:text-white" placeholder="e.g. Stock count mismatch during annual audit">
                </div>
            </form>
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
                                        'bg-teal-50 text-teal-600': log.AdjustmentType === 'Correction'
                                    }" x-text="log.AdjustmentType"></span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-black text-slate-800 dark:text-white" x-text="log.batch?.item?.ItemName || 'Unknown Item'"></p>
                                    <p class="text-[10px] font-bold text-slate-400 font-mono tracking-tighter" x-text="'Batch: ' + log.BatchID"></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-sm font-black" :class="log.QuantityAdjusted < 0 ? 'text-red-500' : 'text-teal-600'" x-text="log.QuantityAdjusted"></p>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-400" x-text="log.Reason"></p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="new Date(log.created_at).toLocaleDateString()"></p>
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
                                        <a :href="'/storage/' + log.EvidencePath" target="_blank" class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-teal-600 hover:border-teal-500/30 transition-all">
                                            🖼️
                                        </a>
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
        activeTab: 'perform',
        items: @json($items),
        inventory: @json($inventory),
        requisitions: @json($requisitions),
        history: @json($history),
        
        disposal: { itemId: '', batchId: '', quantity: 1, reason: 'Damaged', remarks: '', photo: null },
        filteredDisposalBatches: [],

        correction: { itemId: '', batchId: '', quantity: 0, reason: '', remarks: '' },
        filteredCorrectionBatches: [],
        
        returnObj: { requisitionId: '', reason: '', items: [] },
        filteredReturnItems: [],

        init() {
            //
        },

        updateDisposalBatches() {
            this.disposal.batchId = '';
            this.filteredDisposalBatches = this.inventory.filter(b => b.ItemID == this.disposal.itemId);
        },

        updateCorrectionBatches() {
            this.correction.batchId = '';
            this.filteredCorrectionBatches = this.inventory.filter(b => b.ItemID == this.correction.itemId);
        },

        updateReturnItems() {
            const req = this.requisitions.find(r => r.RequisitionID == this.returnObj.requisitionId);
            if (req && req.items) {
                this.filteredReturnItems = req.items.map(i => ({
                    id: i.IssuanceItemID || Math.random(),
                    name: i.item?.ItemName || 'Item',
                    batchId: i.BatchID || 'N/A',
                    issuedQty: i.QuantityIssued || i.QuantityRequested,
                    qty: i.QuantityIssued || i.QuantityRequested,
                    selected: true
                }));
            } else {
                this.filteredReturnItems = [];
            }
        },

        async submitDisposal() {
            const formData = new FormData();
            formData.append('batchId', this.disposal.batchId);
            formData.append('quantity', this.disposal.quantity);
            formData.append('reason', this.disposal.reason);
            formData.append('remarks', this.disposal.remarks);
            if (this.disposal.photo) formData.append('photo', this.disposal.photo);

            try {
                const response = await fetch('/adjustments/disposal', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const result = await response.json();
                if (result.success) { alert('Disposal logged!'); location.reload(); }
                else { alert('Error: ' + result.message); }
            } catch (e) { alert('Connection error'); }
        },

        async submitReturn() {
            const selected = this.filteredReturnItems.filter(i => i.selected);
            if (selected.length === 0) { alert('Select items to return'); return; }

            const payload = {
                requisitionId: this.returnObj.requisitionId,
                reason: this.returnObj.reason,
                items: selected.map(i => ({ batchId: i.batchId, quantity: i.qty }))
            };

            try {
                const response = await fetch('/adjustments/return', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.success) { alert('Return processed!'); location.reload(); }
                else { alert('Error: ' + result.message); }
            } catch (e) { alert('Connection error'); }
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
