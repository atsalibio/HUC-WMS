@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="inventoryManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 px-2">
        <div class="space-y-1">
            <h2 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Inventory Depot</h2>
            <p class="text-slate-500 font-black text-[10px] uppercase tracking-[0.3em] mt-1">Real-time cross-batch stock monitoring</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCreateModal()" class="px-7 py-3 bg-teal-600 hover:bg-teal-700 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-teal-500/20 transition-all active:scale-95">
                + New Registry
            </button>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="mb-8 p-1.5 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 inline-flex gap-1">
        <template x-for="cat in ['all', 'medicine', 'utility', 'others']">
            <button @click="category = cat" 
                    :class="category === cat ? 'bg-slate-900 text-white dark:bg-teal-600' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                    class="px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-widest transition-all"
                    x-text="cat"></button>
        </template>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left table-auto">
                <thead>
                    <tr class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                        <th class="px-10 py-6">Medical Entity</th>
                        <th class="px-10 py-6">Details</th>
                        <th class="px-10 py-6 text-center">In Stock</th>
                        <th class="px-10 py-6">Next Expiry (FEFO)</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                    <template x-for="item in filteredInventory" :key="item.ItemID">
                        <tr @click="toggleItem(item.ItemID)" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group cursor-pointer border-b border-slate-50 dark:border-slate-800">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-xl shadow-inner" x-text="getEmoji(item.Category)"></div>
                                    <div>
                                        <p class="text-xs font-black text-slate-800 dark:text-white" x-text="item.ItemName"></p>
                                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest" x-text="item.Category"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">
                                    Brand: <span class="text-slate-600 dark:text-slate-300" x-text="item.Brand || 'Generic'"></span><br>
                                    Unit: <span class="text-slate-600 dark:text-slate-300" x-text="item.Unit"></span>
                                </p>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <div class="inline-flex items-baseline gap-1">
                                    <span class="text-xl font-black text-slate-800 dark:text-white tabular-nums" x-text="Number(item.TotalQuantity).toLocaleString()"></span>
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="item.Unit"></span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-2">
                                    <p class="text-[11px] font-bold" :class="isExpiringSoon(item.NextExpiry) ? 'text-red-500' : 'text-slate-600 dark:text-slate-400'" x-text="formatDate(item.NextExpiry)"></p>
                                    <svg :class="expandedItems.includes(item.ItemID) ? 'rotate-180' : ''" class="w-4 h-4 text-slate-300 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click.stop="openViewModal(item)" class="w-9 h-9 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-400 hover:text-teal-600 transition-all italic font-serif">i</button>
                                </div>
                            </td>
                        </tr>
                        <!-- Sub-batches (FEFO) -->
                        <tr x-show="expandedItems.includes(item.ItemID)" class="bg-slate-50/50 dark:bg-slate-900/40 border-l-4 border-teal-500 border-b border-slate-50 dark:border-slate-800" x-cloak>
                            <td colspan="5" class="px-10 py-8">
                                <div class="space-y-4">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Batch Fragments (FEFO Order)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-left">
                                        <template x-for="batch in batches[item.ItemID]" :key="batch.BatchID">
                                            <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col justify-between">
                                                <div class="flex justify-between items-start mb-4">
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Batch ID</p>
                                                        <p class="text-[11px] font-black text-slate-800 dark:text-white font-mono" x-text="batch.BatchID"></p>
                                                    </div>
                                                    <span class="px-2 py-1 bg-slate-50 dark:bg-slate-900 text-[8px] font-black text-slate-400 rounded-lg" x-text="'Ref: '+ (batch.PONumber || 'N/A')"></span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Qty</p>
                                                        <p class="text-lg font-black text-slate-800 dark:text-white" x-text="Number(batch.QuantityOnHand).toLocaleString()"></p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Expiry</p>
                                                        <p class="text-[11px] font-bold" :class="isExpiringSoon(batch.ExpiryDate) ? 'text-red-500' : 'text-slate-600 dark:text-slate-300'" x-text="formatDate(batch.ExpiryDate)"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Details Modal -->
    <div x-show="showViewModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 backdrop-blur-sm" x-transition.opacity>
        <div class="fixed inset-0 bg-slate-900/60" @click="showViewModal = false"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col">
                <div class="px-8 pt-8 pb-6 border-b border-slate-50 dark:border-slate-700/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white" x-text="selectedItem.ItemName"></h2>
                        <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mt-1" x-text="selectedItem.Category"></p>
                    </div>
                    <button @click="showViewModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Brand</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white" x-text="selectedItem.Brand || 'Generic'"></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Base Unit</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white" x-text="selectedItem ? selectedItem.Unit : ''"></p>
                        </div>
                    </div>
                </div>
                <div class="px-8 pb-8">
                    <button @click="showViewModal = false" class="w-full py-4 bg-slate-900 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 transition-all">Close Viewer</button>
                </div>
            </div>
        </div>

    <!-- Create Registry Modal -->
    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-10 backdrop-blur-sm" x-transition.opacity>
        <div class="fixed inset-0 bg-slate-900/60" @click="closeCreateModal()"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col max-h-[92vh]">
                
                <!-- Header -->
                <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-tight">New Medical Entity</h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Register a master item profile</p>
                    </div>
                    <button @click="closeCreateModal()" class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <form id="createItemForm" @submit.prevent="submitItem" class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Entity Name</label>
                            <input type="text" x-model="formData.ItemName" required placeholder="e.g. Paracetamol 500mg" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Brand Name (Optional)</label>
                                <input type="text" x-model="formData.Brand" placeholder="e.g. Biogesic" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dosage Form</label>
                                <input type="text" x-model="formData.DosageUnit" placeholder="e.g. 500mg/Tablet" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Classification Type</label>
                                <select x-model="formData.ItemType" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                                    <template x-for="cat in ['Medicine', 'Supply', 'Equipment', 'Others']">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Base Unit</label>
                                <select x-model="formData.UnitOfMeasure" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                                    <template x-for="uom in ['Tablet', 'Capsule', 'Bottle', 'Vial', 'Ampule', 'Sachet', 'Box', 'Piece', 'Unit']">
                                        <option :value="uom" x-text="uom"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="p-10 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <button type="button" @click="closeCreateModal()" class="flex-1 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Cancel</button>
                        <button type="submit" form="createItemForm" class="flex-1 py-5 bg-slate-900 dark:bg-teal-600 hover:scale-[1.02] text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl transition-all active:scale-95">Expand Registry</button>
                    </div>
                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('inventoryManager', () => ({
        category: 'all',
        expandedItems: [],
        inventory: @json($aggregatedInventory),
        batches: @json($batchesByItem),
        showViewModal: false,
        showCreateModal: false,
        selectedItem: null,
        formData: {
            ItemName: '',
            Brand: '',
            DosageUnit: '',
            ItemType: 'Medicine',
            UnitOfMeasure: 'Tablet'
        },
        
        get filteredInventory() {
            let items = this.inventory;
            if (this.category !== 'all') {
                items = items.filter(i => {
                    const mapped = this.mapCategory(i.Category);
                    return mapped === this.category;
                });
            }
            return items;
        },

        openCreateModal() {
            this.showCreateModal = true;
        },
        
        closeCreateModal() {
            this.showCreateModal = false;
            this.formData = { ItemName: '', Brand: '', DosageUnit: '', ItemType: 'Medicine', UnitOfMeasure: 'Tablet' };
        },

        openViewModal(item) {
            console.log('Opening View Modal for:', item);
            this.selectedItem = item;
            this.showViewModal = true;
        },

        mapCategory(cat) {
            if (cat === 'Medicine') return 'medicine';
            if (cat === 'Supply' || cat === 'Equipment') return 'utility';
            return 'others';
        },

        toggleItem(id) {
            if (this.expandedItems.includes(id)) {
                this.expandedItems = this.expandedItems.filter(i => i !== id);
            } else {
                this.expandedItems.push(id);
            }
        },

        getEmoji(cat) {
            if (cat === 'Medicine') return '💊';
            if (cat === 'Utility' || cat === 'Equipment') return '🛠️';
            return '📦';
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
            return diff < (1000 * 60 * 60 * 24 * 90); // 90 days
        },

        async submitItem() {
            try {
                const response = await fetch('/inventory/item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert('Item registry expanded successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Validation failed.'));
                }
            } catch (error) {
                alert('Connection error');
            }
        }
    }));
});
</script>
@endpush
@endsection
