@extends('layouts.app')

@section('content')
    <div x-data="issuanceManager()" x-init="init()">
        <!-- Header -->
        <div class="mb-14 px-4">
            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] mb-2">Inventory Outbound</p>
            <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight uppercase">Stock Issuance</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2.5 max-w-2xl">Fulfill approved health center requisitions and
                manage physical stock movement from main storage. Items are issued <strong>FEFO</strong> (First-Expired,
                First-Out).</p>
        </div>

        <!-- Active Requisitions -->
        <div class="space-y-6 mb-12">
            <div class="flex items-center gap-3 ml-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Awaiting Fulfillment</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($approvedRequisitions as $req)
                    <div
                        class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-all group flex flex-col">
                        <!-- Health Center Badge -->
                        <div class="mb-4 flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                {{ $req->healthCenter->Name ?? 'Unknown Health Center' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Requisition</span>
                                <h3
                                    class="text-xl font-black text-slate-800 dark:text-white group-hover:text-indigo-500 transition-colors">
                                    {{ $req->RequisitionNumber }}</h3>
                            </div>
                            <div
                                class="px-3 py-1 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                {{ $req->StatusType }}
                            </div>
                        </div>

                        <div class="space-y-4 flex-1">
                            <div
                                class="p-4 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-100 dark:border-slate-800/50">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Item Breakdown
                                </p>
                                <ul class="space-y-2">
                                    @foreach($req->items as $item)
                                        <li class="flex justify-between text-xs">
                                            <span
                                                class="font-bold text-slate-600 dark:text-slate-400">{{ $item->item->ItemName }}</span>
                                            <span class="text-slate-400">× {{ $item->QuantityRequested }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <button data-req="{{ json_encode($req, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG) }}"
                            @click="openIssuModal(JSON.parse($el.dataset.req))"
                            class="mt-8 w-full py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-indigo-500/20 active:scale-95">
                            Prepare Issuance
                        </button>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div
                            class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-700">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-400 uppercase tracking-widest">All Clear</h3>
                        <p class="text-sm text-slate-400 italic">No approved requisitions waiting for fulfillment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Issuance Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 lg:p-12 backdrop-blur-sm bg-slate-1200/80"
            x-cloak @click.self="closeModal()">

            <!-- Modal Content -->
            <div
                class="relative z-10 w-full max-w-6xl bg-white dark:bg-slate-800 rounded-[3.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 animate-in zoom-in-95 duration-200 flex flex-col my-auto transition-all">

                <div class="flex flex-col lg:flex-row h-full overflow-hidden">
                    <!-- Sidebar Summary -->
                    <div
                        class="w-full lg:w-80 bg-slate-50 dark:bg-slate-900 border-r border-slate-100 dark:border-slate-800 p-10 flex flex-col shrink-0">
                        <div class="mb-6">
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Processing</p>
                            <h2 class="text-2xl font-black text-slate-800 dark:text-white"
                                x-text="activeReq.RequisitionNumber"></h2>
                        </div>

                        <!-- Health Center Highlight -->
                        <div
                            class="mb-8 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-800/50">
                            <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">📍 Receiving
                                Health Center</p>
                            <p class="font-black text-indigo-700 dark:text-indigo-300 text-sm"
                                x-text="activeReq.health_center?.Name || '—'"></p>
                        </div>

                        <div class="space-y-6 flex-1">
                            <div>
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">Stock
                                    Source</label>
                                <select x-model="formData.warehouseId" @change="onWarehouseChange()"
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-xs focus:ring-4 focus:ring-indigo-500/10">
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->WarehouseID }}">{{ $wh->WarehouseName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div
                                class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-800/30">
                                <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">⚡ FEFO Active
                                </p>
                                <p class="text-[10px] text-amber-700 dark:text-amber-400">Batches are pre-sorted by earliest
                                    expiry date. Issue soonest-expiring stock first.</p>
                            </div>
                        </div>

                        <button @click="closeModal()"
                            class="mt-8 py-4 text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest transition-colors">Discard
                            Draft</button>
                    </div>

                    <!-- Main Interface (Scrollable) -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Header -->
                        <div class="px-12 pt-12 pb-6 border-b border-slate-50 dark:border-slate-800">
                            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Configure Line Items</h3>
                            <p class="text-sm text-slate-500">Select batches and confirm quantities to issue. Only available
                                (non-expired, in-stock) batches are shown.</p>
                        </div>

                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto p-12 custom-scrollbar space-y-6">
                            <template x-for="(item, index) in formData.items" :key="index">
                                <div
                                    class="bg-white dark:bg-slate-800/50 p-8 rounded-[3rem] border border-slate-100 dark:border-slate-700/50 shadow-sm transition-all hover:shadow-md group">
                                    <div
                                        class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1"
                                                x-text="'Line Item #' + (index + 1)"></p>
                                            <h4 class="text-xl font-black text-slate-800 dark:text-white group-hover:text-indigo-500 transition-colors"
                                                x-text="item.itemName"></h4>
                                        </div>
                                        <div
                                            class="bg-slate-50 dark:bg-slate-900 px-6 py-3 rounded-2xl border border-slate-100 dark:border-slate-800 text-right">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                Qty Requested</p>
                                            <p class="text-xl font-black text-slate-800 dark:text-white"
                                                x-text="item.requestedQty"></p>
                                        </div>
                                    </div>

                                    <!-- Batch Allocations for this item -->
                                    <div class="space-y-4">
                                        <template x-if="item.itemStatus === 'Approved'">
                                            <div class="space-y-4">
                                                <div class="flex justify-between items-center">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Batch
                                                        Allocations (FEFO)</p>
                                                    <button type="button" @click="addBatchAllocation(index)"
                                                        class="text-[10px] font-black text-indigo-500 hover:underline uppercase tracking-widest">
                                                        + Add Batch
                                                    </button>
                                                </div>

                                                <template x-for="(alloc, ai) in item.allocations" :key="ai">
                                                    <div
                                                        class="flex flex-col md:flex-row gap-4 p-5 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800">
                                                        <!-- Batch Dropdown -->
                                                        <div class="flex-1 space-y-1">
                                                            <label
                                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Batch No.
                                                            </label>
                                                            <select x-model="alloc.batchId" @change="onBatchSelected(index, ai)"
                                                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-xl font-bold dark:text-white text-sm focus:ring-4 focus:ring-indigo-500/10">
                                                                <template x-for="b in item.availableBatches" :key="b.BatchID">
                                                                    <option :value="b.BatchID" :disabled="b.QuantityOnHand <= 0"
                                                                        x-text="b.BatchNumber + '  |  Qty: ' + b.QuantityOnHand + '  |  Exp: ' + (b.ExpiryDate || 'N/A')">
                                                                    </option>
                                                                </template>
                                                            </select>
                                                        </div>

                                                        <!-- Available qty for selected batch -->
                                                        <div class="w-32 space-y-1">
                                                            <label
                                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Available</label>
                                                            <div class="px-4 py-3 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-100 dark:border-green-800/30 font-black text-green-700 dark:text-green-400 text-sm text-center"
                                                                x-text="alloc.availableQty !== null ? alloc.availableQty : '—'">
                                                            </div>
                                                        </div>

                                                        <!-- Issue Qty -->
                                                        <div class="w-36 space-y-1">
                                                            <label
                                                                class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Issue
                                                                Qty</label>
                                                            <input type="number" x-model.number="alloc.issuedQty"
                                                                :max="alloc.availableQty" min="1" @input="validateQty(index, ai)"
                                                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-xl font-black dark:text-white text-lg text-center focus:ring-4 focus:ring-indigo-500/10"
                                                                :class="alloc.issuedQty > alloc.availableQty ? 'ring-2 ring-red-400' : ''">
                                                            <p x-show="alloc.issuedQty > alloc.availableQty"
                                                                class="text-[9px] text-red-500 font-black mt-1 ml-1">Exceeds
                                                                available!</p>
                                                        </div>

                                                        <!-- Remove Batch -->
                                                        <button type="button" @click="removeBatchAllocation(index, ai)"
                                                            x-show="item.allocations.length > 1"
                                                            class="self-end p-3 text-slate-300 hover:text-red-500 transition-colors rounded-xl hover:bg-red-50">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>

                                                <!-- Total issued for this item -->
                                                <div class="flex justify-end">
                                                    <div class="flex items-center gap-3 px-5 py-2.5 rounded-2xl"
                                                        :class="getTotalIssued(index) === item.requestedQty ? 'bg-green-50 dark:bg-green-900/10' : 
                                                                    getTotalIssued(index) > item.requestedQty ? 'bg-red-50 dark:bg-red-900/10' : 'bg-slate-50 dark:bg-slate-900/30'">
                                                        <p class="text-[10px] font-black uppercase tracking-widest"
                                                            :class="getTotalIssued(index) === item.requestedQty ? 'text-green-600' : 
                                                                    getTotalIssued(index) > item.requestedQty ? 'text-red-600' : 'text-slate-400'">
                                                            Total Issuing:
                                                        </p>
                                                        <p class="font-black text-lg"
                                                            :class="getTotalIssued(index) === item.requestedQty ? 'text-green-700 dark:text-green-400' : 
                                                                    getTotalIssued(index) > item.requestedQty ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-300'"
                                                            x-text="getTotalIssued(index) + ' / ' + item.requestedQty">
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="item.itemStatus != 'Approved'">
                                            <div
                                                class="p-4 bg-red-50 dark:bg-red-900/10 rounded-2xl border border-red-100 dark:border-red-800/30">
                                                <p class="text-[12px] font-black text-red-500 uppercase tracking-widest mb-1">
                                                    ✖ Item Not Approved
                                                </p>
                                                <p class="text-[10px] text-red-700 dark:text-red-400">
                                                    This item was not approved and cannot be issued.
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Issuance Remarks -->
                            <div
                                class="p-8 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-[2.5rem] border-2 border-dashed border-indigo-100 dark:border-indigo-800/50 mt-8">
                                <label
                                    class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-1 mb-3 block text-left">Issuance
                                    Notes / Disposal Instructions</label>
                                <textarea x-model="formData.remarks"
                                    placeholder="Add details about the courier, vehicle, or special handling..." rows="3"
                                    class="w-full px-6 py-4 bg-white dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="px-12 py-10 border-t border-slate-50 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-900/10">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <button @click="closeModal()"
                                    class="flex-1 py-5 font-black text-slate-400 uppercase tracking-widest text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Back
                                    to List</button>
                                <button @click="submitIssuance"
                                    class="flex-[2] py-5 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl shadow-indigo-500/40 transition-all active:scale-95">
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>

    @push('scripts')
        <script>
            // Pre-load FEFO batches from the server (available, non-expired, sorted by expiry asc)
            const fefoAllBatches = @json($batchesByItem ?? []);

            function issuanceManager() {
                return {
                    showModal: false,
                    activeReq: {},
                    formData: {
                        requisitionId: '',
                        warehouseId: '{{ $warehouses->first()?->WarehouseID ?? 1 }}',
                        remarks: '',
                        items: []
                    },
                    init() {
                        //
                    },

                    getBatchesForItem(itemId) {
                        // Return FEFO-sorted batches for this item from server-loaded data
                        return fefoAllBatches[itemId] || [];
                    },

                    openIssuModal(req) {
                        this.activeReq = req;
                        this.formData.requisitionId = req.RequisitionID;
                        this.formData.remarks = '';
                        this.formData.items = req.items.map(i => {
                            const batches = this.getBatchesForItem(i.ItemID);
                            return {
                                itemId: i.ItemID,
                                reqItemId: i.RequisitionItemID,
                                itemStatus: i.StatusType,
                                itemName: i.item ? i.item.ItemName : ('Item #' + i.ItemID),
                                requestedQty: i.QuantityRequested,
                                availableBatches: batches,
                                allocations: [{
                                    batchId: batches.length ? batches[0].BatchID : '',
                                    availableQty: batches.length ? batches[0].QuantityOnHand : null,
                                    issuedQty: Math.min(i.QuantityRequested, batches.length ? batches[0].QuantityOnHand : 0),
                                }]
                            };
                        });
                        this.showModal = true;
                    },

                    onWarehouseChange() {
                        // On warehouse change, re-filter batches (would need server-side call in production)
                        // For now, reload page batches via API
                    },

                    onBatchSelected(itemIdx, allocIdx) {
                        const alloc = this.formData.items[itemIdx].allocations[allocIdx];
                        const batches = this.formData.items[itemIdx].availableBatches;
                        const selected = batches.find(b => b.BatchID == alloc.batchId);
                        alloc.availableQty = selected ? selected.QuantityOnHand : null;
                        alloc.issuedQty = selected ? Math.min(
                            this.formData.items[itemIdx].requestedQty,
                            selected.QuantityOnHand
                        ) : 1;
                    },

                    addBatchAllocation(itemIdx) {
                        this.formData.items[itemIdx].allocations.push({
                            batchId: '',
                            availableQty: null,
                            issuedQty: 1,
                        });
                    },

                    removeBatchAllocation(itemIdx, allocIdx) {
                        if (this.formData.items[itemIdx].allocations.length > 1) {
                            this.formData.items[itemIdx].allocations.splice(allocIdx, 1);
                        }
                    },

                    getTotalIssued(itemIdx) {
                        return this.formData.items[itemIdx].allocations.reduce((sum, a) => sum + (parseInt(a.issuedQty) || 0), 0);
                    },

                    validateQty(itemIdx, allocIdx) {
                        const alloc = this.formData.items[itemIdx].allocations[allocIdx];
                        if (alloc.availableQty !== null && alloc.issuedQty > alloc.availableQty) {
                            alloc.issuedQty = alloc.availableQty;
                        }
                    },

                    closeModal() {
                        this.showModal = false;
                    },

                    async submitIssuance() {
                        // Validate all items have at least one batch selected
                        for (let i = 0; i < this.formData.items.length; i++) {
                            const item = this.formData.items[i];

                            for (let alloc of item.allocations) {
                                if (!alloc.batchId) {
                                    alert(`Please select a batch for item: ${item.itemName}`);
                                    return;
                                }
                                if (alloc.issuedQty > alloc.availableQty) {
                                    alert(`Issued quantity exceeds available stock for item: ${item.itemName}`);
                                    return;
                                }
                            }
                            const totalIssued = this.getTotalIssued(i);
                            if (totalIssued !== item.requestedQty) {
                                if (!confirm(`Item "${item.itemName}" has ${totalIssued} units allocated but ${item.requestedQty} were requested. Continue anyway?`)) {
                                    return;
                                }
                            }
                        }

                        const payload = {
                            requisitionId: this.formData.requisitionId,
                            allocationPlan: this.formData.items.map(item => ({
                                reqItemId: item.reqItemId,
                                reqItemStatus: item.itemStatus,
                                allocated: item.allocations
                                    .filter(a => a.batchId && a.issuedQty > 0)
                                    .map(a => ({
                                        BatchID: a.batchId,
                                        Quantity: a.issuedQty,
                                        HealthCenterID: this.activeReq.HealthCenterID
                                    }))
                            }))
                        };

                        try {
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
    @endpush
@endsection