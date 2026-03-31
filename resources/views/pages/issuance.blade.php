@extends('layouts.app')

@section('content')
<div x-data="issuanceManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-12">
        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] mb-2">Inventory Outbound</p>
        <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">Stock Issuance</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">Fulfill approved health center requisitions and manage physical stock movement from main storage.</p>
    </div>

    <!-- Active Requisitions -->
    <div class="space-y-6 mb-12">
        <div class="flex items-center gap-3 ml-2">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Awaiting Fulfillment</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($approvedRequisitions as $req)
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-all group flex flex-col">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Requisition</span>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white group-hover:text-indigo-500 transition-colors">{{ $req->RequisitionNumber }}</h3>
                    </div>
                    <div class="px-3 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest">
                        {{ $req->StatusType }}
                    </div>
                </div>

                <div class="space-y-4 flex-1">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center text-slate-400 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Destination</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $req->healthCenter->Name }}</p>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Item Breakdown</p>
                        <ul class="space-y-2">
                            @foreach($req->items as $item)
                            <li class="flex justify-between text-xs">
                                <span class="font-bold text-slate-600 dark:text-slate-400">{{ $item->item->ItemName }}</span>
                                <span class="text-slate-400">× {{ $item->QuantityRequested }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <button 
                    data-req="{{ json_encode($req, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG) }}"
                    @click="openIssuModal(JSON.parse($el.dataset.req))"
                    class="mt-8 w-full py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-indigo-500/20 active:scale-95">
                    Prepare Issuance
                </button>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-700">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">All Clear</h3>
                <p class="text-sm text-slate-400 italic">No approved requisitions waiting for fulfillment.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Issuance Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-8 backdrop-blur-md"
         x-cloak>
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/80" @click="closeModal()"></div>

            <!-- Modal Content Wrapper -->
            <div class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 pointer-events-none">
                <div @click.away="closeModal()" 
                     class="bg-white dark:bg-slate-800 w-full max-w-6xl rounded-[3.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 pointer-events-auto animate-in zoom-in-95 duration-200 flex flex-col max-h-[92vh]">
                    
                    <div class="flex flex-col lg:flex-row h-full overflow-hidden">
                        <!-- Sidebar Summary (Static on large, part of scroll on small if needed, but here we'll keep it as a side panel) -->
                        <div class="w-full lg:w-80 bg-slate-50 dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 p-10 flex flex-col shrink-0">
                            <div class="mb-10">
                                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Processing</p>
                                <h2 class="text-2xl font-black text-slate-800 dark:text-white" x-text="activeReq.RequisitionNumber"></h2>
                            </div>
                            
                            <div class="space-y-8 flex-1">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Target</label>
                                    <p class="font-bold text-slate-700 dark:text-slate-300" x-text="activeReq.health_center.Name"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Stock Source</label>
                                    <select x-model="formData.warehouseId" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-xs focus:ring-4 focus:ring-indigo-500/10">
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->WarehouseID }}">{{ $wh->WarehouseName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button @click="closeModal()" class="mt-8 py-4 text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest transition-colors">Discard Draft</button>
                        </div>

                        <!-- Main Interface (Scrollable) -->
                        <!-- Main Interface -->
                        <div class="flex-1 flex flex-col overflow-hidden">
                            <!-- Header -->
                            <div class="px-12 pt-12 pb-6 border-b border-slate-50 dark:border-slate-800">
                                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Configure Line Items</h3>
                                <p class="text-sm text-slate-500">Select batches and confirm quantities to issue.</p>
                            </div>

                            <!-- Body -->
                            <div class="flex-1 overflow-y-auto p-12 custom-scrollbar space-y-4">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <div class="bg-white dark:bg-slate-800/50 p-8 rounded-[3rem] border border-slate-100 dark:border-slate-700/50 shadow-sm transition-all hover:shadow-md group">
                                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                                            <div class="flex-1">
                                                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1" x-text="'Line Item #' + (index + 1)"></p>
                                                <h4 class="text-xl font-black text-slate-800 dark:text-white group-hover:text-indigo-500 transition-colors" x-text="item.itemName"></h4>
                                            </div>
                                            <div class="bg-slate-50 dark:bg-slate-900 px-6 py-3 rounded-2xl border border-slate-100 dark:border-slate-800">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-right">Qty Requested</p>
                                                <p class="text-xl font-black text-slate-800 dark:text-white text-right" x-text="item.requestedQty"></p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Stock Batch ID</label>
                                                <input type="text" x-model="item.batchId" placeholder="Required" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Lot Number</label>
                                                <input type="text" x-model="item.lotNo" placeholder="Required" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white text-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirm Quantity</label>
                                                <input type="number" x-model="item.issuedQty" :max="item.requestedQty" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-black dark:text-white text-xl focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Issuance Remarks -->
                                <div class="p-8 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-[2.5rem] border-2 border-dashed border-indigo-100 dark:border-indigo-800/50 mt-8">
                                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-1 mb-3 block text-left">Issuance Notes / Disposal Instructions</label>
                                    <textarea x-model="formData.remarks" placeholder="Add details about the courier, vehicle, or special handling..." rows="3" class="w-full px-6 py-4 bg-white dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all"></textarea>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="px-12 py-10 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/10">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <button @click="closeModal()" class="flex-1 py-5 font-black text-slate-400 uppercase tracking-widest text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Back to List</button>
                                    <button @click="submitIssuance" class="flex-[2] py-5 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl shadow-indigo-500/40 transition-all active:scale-95">
                                        Execute Stock Transfer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>

<script>
function issuanceManager() {
    return {
        showModal: false,
        activeReq: {},
        formData: {
            requisitionId: '',
            warehouseId: '1',
            remarks: '',
            items: []
        },
        init() {
            //
        },
        openIssuModal(req) {
            this.activeReq = req;
            this.formData.requisitionId = req.RequisitionID;
            this.formData.remarks = '';
            this.formData.items = req.items.map(i => ({
                itemId: i.ItemID,
                itemName: i.item.ItemName,
                requestedQty: i.QuantityRequested,
                issuedQty: i.QuantityRequested,
                batchId: '',
                lotNo: ''
            }));
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        closeDetailsModal() {
            this.showDetailsModal = false;
        },
        async submitIssuance() {
            if (this.formData.items.some(i => !i.batchId || !i.lotNo)) {
                alert("Please provide Batch ID and Lot Number for all items.");
                return;
            }

            try {
                // Map frontend items to backend allocationPlan structure
                const payload = {
                    requisitionId: this.formData.requisitionId,
                    allocationPlan: this.formData.items.map(item => ({
                        reqItemId: null, // Could be enhanced if reqItem IDs are available
                        allocated: [{
                            BatchID: item.batchId,
                            Quantity: item.issuedQty,
                            HealthCenterID: this.activeReq.HealthCenterID
                        }]
                    }))
                };

                const response = await fetch('/issuances/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Items issued successfully! Stock levels updated.');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || JSON.stringify(result.errors) || 'Validation failed'));
                }
            } catch (error) {
                console.error(error);
                alert('Connection error or validation failed. Check console for details.');
            }
        }
    }
}
</script>
@endsection
