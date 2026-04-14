@extends('layouts.app')

@section('content')
<div x-data="receivingManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-10 px-4">
        <h1 class="text-4xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Receiving Bay</h1>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1.5">Accept and verify incoming shipments from approved procurement orders.</p>
    </div>

    <!-- Active Deliveries -->
    <div class="grid grid-cols-1 gap-6 mb-12">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-700/50">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-800 dark:text-white">Awaiting Delivery</h3>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 rounded-full text-xs font-black uppercase tracking-widest">
                    {{ count($pendingOrders) }} Orders Pending
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">PO Number</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Supplier</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Items Ordered</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($pendingOrders as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-black text-slate-800 dark:text-white">{{ $order->PONumber }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-tighter">{{ $order->PODate->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $order->supplier->Name ?? $order->SupplierName }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    @foreach($order->items->take(3) as $item)
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 border-2 border-white dark:border-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500" title="{{ $item->item->ItemName }}">
                                            {{ substr($item->item->ItemName, 0, 1) }}
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-600 border-2 border-white dark:border-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                            +{{ $order->items->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button 
                                    @click="startReceiving({{ $order->POID }})"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                                    Accept Delivery
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">No pending deliveries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Receiving History -->
    <div class="mb-12 px-4">
        <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Receiving History</h2>
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-700/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Receipt ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">PO Number</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Supplier</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Received Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Processed By</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($receivingHistory as $rec)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-black text-slate-800 dark:text-white">RCV-{{ str_pad($rec->ReceivingID, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-600 dark:text-slate-300">{{ $rec->procurementOrder->PONumber ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $rec->procurementOrder->supplier->Name ?? $rec->procurementOrder->SupplierName ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-bold">
                                {{ $rec->ReceivedDate->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                {{ $rec->user->FName ?? '' }} {{ $rec->user->LName ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button 
                                    @click="openDetailsModal({{ $rec->ReceivingID }})"
                                    class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 dark:hover:text-blue-400 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-sm">
                                    View Log
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No receiving history available yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Receiving Modal -->
    <div x-show="showReceivingModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 lg:p-12 backdrop-blur-sm bg-slate-900/60"
         x-cloak @click.self="showReceivingModal = false">

            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 animate-in zoom-in-95 duration-200 flex flex-col my-auto z-[120] transition-all">
                    
                    <!-- Header -->
                    <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Incoming Shipment</p>
                                <h2 class="text-3xl font-black text-slate-800 dark:text-white" x-text="'Order ' + activeOrder.PONumber"></h2>
                            </div>
                            <button @click="showReceivingModal = false" class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                        <form id="receivingForm" @submit.prevent="submitReceiving" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-800">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Target Warehouse</label>
                                    <select x-model="receivingData.warehouseId" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10">
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->WarehouseID }}">{{ $wh->WarehouseName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2 bg-slate-50 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-800">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3 block">Supplier Information</label>
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center font-black text-lg mr-4 border border-blue-500/20">
                                            <span x-text="activeOrder.supplier ? activeOrder.supplier.Name.charAt(0) : 'S'"></span>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 dark:text-white text-lg" x-text="activeOrder.supplier ? activeOrder.supplier.Name : activeOrder.SupplierName"></p>
                                            <p class="text-xs text-slate-500 font-medium" x-text="activeOrder.supplier ? activeOrder.supplier.Address : 'No address provided'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Item Verification List -->
                            <div class="space-y-4">
                                <h3 class="text-sm font-black text-slate-500 uppercase tracking-[0.1em] ml-2">Verify Received Quantities</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <template x-for="(item, index) in receivingData.items" :key="index">
                                        <div class="bg-slate-50 dark:bg-slate-900/40 p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-700/30 transition-all hover:bg-white dark:hover:bg-slate-800 hover:shadow-lg group">
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-slate-200 dark:border-slate-800">
                                                <div class="md:col-span-2">
                                                    <p class="font-black text-slate-800 dark:text-white group-hover:text-blue-600 transition-colors" x-text="item.itemName"></p>
                                                    <p class="text-[10px] text-slate-400 uppercase font-black" x-text="'Ordered: ' + item.orderedQty"></p>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Received Qty</label>
                                                    <input type="number" x-model="item.quantityReceived" :max="item.orderedQty" required class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-none rounded-xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Unit Cost (₱)</label>
                                                    <input type="number" x-model="item.unitCost" step="0.01" class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border-none rounded-xl font-bold dark:text-white text-xs focus:ring-4 focus:ring-blue-500/10">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Batch ID</label>
                                                    <input type="text" x-model="item.batchId" placeholder="Required" required :readonly="item.batchId && item.batchId.length > 0" 
                                                           :class="item.batchId && item.batchId.length > 0 ? 'bg-slate-100 dark:bg-slate-700/50 cursor-not-allowed opacity-75' : 'bg-white dark:bg-slate-800'"
                                                           class="w-full px-4 py-2.5 border-none rounded-xl font-black dark:text-white text-xs">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Lot Number</label>
                                                    <input type="text" x-model="item.lotNumber" placeholder="Required" required :readonly="item.lotNumber && item.lotNumber.length > 0"
                                                           :class="item.lotNumber && item.lotNumber.length > 0 ? 'bg-slate-100 dark:bg-slate-700/50 cursor-not-allowed opacity-75' : 'bg-white dark:bg-slate-800'"
                                                           class="w-full px-4 py-2.5 border-none rounded-xl font-black dark:text-white text-xs">
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Expiry Date</label>
                                                    <input type="date" x-model="item.expiryDate" :readonly="item.expiryDate && item.expiryDate.length > 0"
                                                           :class="item.expiryDate && item.expiryDate.length > 0 ? 'bg-slate-100 dark:bg-slate-700/50 cursor-not-allowed opacity-75' : 'bg-white dark:bg-slate-800'"
                                                           class="w-full px-4 py-2.5 border-none rounded-xl font-bold dark:text-white text-xs focus:ring-4 focus:ring-blue-500/10">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="p-10 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="button" @click="discardShipment()" class="flex-1 py-5 font-black text-rose-500 hover:text-rose-600 uppercase tracking-widest text-xs hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-[2.5rem] transition-all">Discard Shipment</button>
                            <button type="submit" form="receivingForm" class="flex-[1.5] py-5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest rounded-[2.5rem] shadow-2xl shadow-blue-500/30 transition-all active:scale-95">
                                Confirm & Stock Inventory
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div x-show="showDetailsModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 lg:p-12 backdrop-blur-sm bg-slate-900/60"
         x-cloak @click.self="closeDetailsModal()">

            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 animate-in zoom-in-95 duration-200 flex flex-col my-auto z-[120] transition-all">
                    
                    <!-- Header -->
                    <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Receipt Log</p>
                                <h2 class="text-3xl font-black text-slate-800 dark:text-white" x-text="activeRec && (activeRec.procurement_order || activeRec.procurementOrder) ? ('Order ' + ((activeRec.procurement_order && activeRec.procurement_order.PONumber) || (activeRec.procurementOrder && activeRec.procurementOrder.PONumber))) : 'Shipment Details'"></h2>
                            </div>
                            <button @click="closeDetailsModal()" class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-10 custom-scrollbar overflow-y-auto">
                        <div class="space-y-6">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Items Received</h3>
                            <div class="bg-slate-50 dark:bg-slate-900/40 rounded-[2.5rem] border border-slate-100 dark:border-slate-700/30 overflow-hidden text-sm">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-100/50 dark:bg-slate-800/50">
                                        <tr>
                                            <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-widest text-[10px]">Item / Description</th>
                                            <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-widest text-[10px]">Batch Details</th>
                                            <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-widest text-[10px] text-right">Qty Received</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 border-t border-slate-100 dark:border-slate-800">
                                        <template x-for="(item, index) in (activeRec.items || [])" :key="index">
                                            <tr class="hover:bg-white dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="font-black text-slate-800 dark:text-white" x-text="item.batch && item.batch.item ? item.batch.item.ItemName : 'Unknown Item'"></div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1">Batch: <span class="text-slate-800 dark:text-slate-300" x-text="(item.batch && item.batch.BatchNumber) ? item.batch.BatchNumber : 'N/A'"></span></div>
                                                    <div class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1">Lot: <span class="text-slate-800 dark:text-slate-300" x-text="(item.batch && item.batch.LotNumber) ? item.batch.LotNumber : 'N/A'"></span></div>
                                                    <div class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Exp: <span :class="(item.batch && item.batch.ExpiryDate) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-300'" x-text="(item.batch && item.batch.ExpiryDate) ? item.batch.ExpiryDate.split(' ')[0] : 'N/A'"></span></div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="font-black text-lg text-blue-600 dark:text-blue-400" x-text="item.QuantityReceived"></div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
         </div>
    </div>
</div>

<script>
function receivingManager() {
    return {
        showReceivingModal: false,
        showDetailsModal: false,
        activeOrder: {},
        activeRec: {},
        receivingData: {
            poId: '',
            warehouseId: '1',
            items: []
        },
        pendingOrders: @json($pendingOrders),
        receivingHistory: @json($receivingHistory),
        init() {
            console.log("Receiving Bay Initialized");
        },
        startReceiving(poId) {
            const order = this.pendingOrders.find(o => o.POID == poId);
            if (!order) return;
            
            this.activeOrder = order;
            this.receivingData.poId = order.POID;
            this.receivingData.items = order.items.map(i => ({
                itemId: i.ItemID,
                itemName: i.item.ItemName,
                orderedQty: i.QuantityOrdered,
                quantityReceived: i.QuantityOrdered,
                unitCost: i.UnitCost || 0,
                batchId: i.BatchID || '', // Carry over if exists in PO
                lotNumber: i.LotNumber || '', // Carry over if exists in PO
                expiryDate: (i.ExpiryDate && i.ExpiryDate !== '0000-00-00') ? i.ExpiryDate.split(' ')[0] : ''
            }));
            this.showReceivingModal = true;
        },
        openDetailsModal(recId) {
            console.log("Opening details modal for ID:", recId);
            const rec = this.receivingHistory.find(r => r.ReceivingID == recId);
            if (!rec) {
                console.warn("Record not found in history:", recId);
                return;
            }
            this.activeRec = rec;
            this.showDetailsModal = true;
        },
        closeDetailsModal() {
            this.showDetailsModal = false;
        },
        async submitReceiving() {
            try {
                this.loading = true;
                const response = await fetch('/receivings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.receivingData)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Items received and inventory updated!');
                    location.reload();
                } else {
                    let errorMsg = result.message || 'Validation failed';
                    if (result.errors) {
                        errorMsg += ': ' + Object.values(result.errors).flat().join(', ');
                    }
                    alert('Error: ' + errorMsg);
                }
            } catch (error) {
                console.error('Submission error:', error);
                alert('Connection error or network failure. Please verify your connection.');
            } finally {
                this.loading = false;
            }
        },
        async discardShipment() {
            if (!confirm(`Are you sure you want to discard Order ${this.activeOrder.PONumber}? This will mark it as rejected.`)) return;

            try {
                this.loading = true;
                const response = await fetch(`/procurement/orders/${this.receivingData.poId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: 'Rejected' })
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Shipment discarded successfully.');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to discard.'));
                }
            } catch (error) {
                console.error('Discard error:', error);
                alert('Connection error or network failure.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
