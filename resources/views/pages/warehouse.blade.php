@extends('layouts.app')

@section('content')
<div x-data="warehouseManager()" x-init="fetchWarehouses()">
    <!-- Header Section -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Warehouse Management</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Configure and manage inventory storage locations.</p>
        </div>
        <button @click="showModal = true" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg shadow-teal-500/20 transition-all active:scale-95 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Warehouse
        </button>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-blue-500/10 text-blue-500 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Warehouses</p>
                    <p class="text-2xl font-black text-slate-800 dark:text-white" x-text="warehouses.length">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouse Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Warehouse Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Location</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    <template x-for="wh in warehouses" :key="wh.WarehouseID">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-slate-200" x-text="wh.WarehouseName"></div>
                                <div class="text-xs text-slate-500" x-text="'ID: ' + wh.WarehouseID"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400" x-text="wh.Location || 'N/A'"></td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg" 
                                      :class="wh.WarehouseType === 'Main' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                                      x-text="wh.WarehouseType"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="editWarehouse(wh)" class="p-2 text-slate-400 hover:text-teal-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="warehouses.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-slate-400 font-medium italic">No warehouses found.</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" style="display:none;" class="fixed inset-0 z-[200] flex items-center justify-center p-4 backdrop-blur-sm" x-transition.opacity>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/70" @click="showModal = false"></div>

        <!-- Modal Content -->
        <div class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col max-h-[85vh]">
            
            <!-- Header -->
            <div class="px-8 pt-8 pb-4 border-b border-slate-100 dark:border-slate-700/30 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white" x-text="editMode ? 'Edit Warehouse' : 'Add New Warehouse'"></h2>
                    <button @click="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700/30 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <form id="warehouseForm" @submit.prevent="saveWarehouse" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Warehouse Name</label>
                        <input type="text" x-model="formData.warehouseName" required placeholder="e.g. Central Pharmacy Storage" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Location</label>
                        <input type="text" x-model="formData.location" placeholder="e.g. Level 2, Wing B" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Warehouse Type</label>
                        <select x-model="formData.warehouseType" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all">
                            <option value="Main">Main Storage</option>
                            <option value="Buffer">Buffer Station</option>
                            <option value="Temporary">Temporary</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="p-8 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20 flex-shrink-0">
                <div class="flex gap-3">
                    <button type="button" @click="closeModal()" class="flex-1 py-4 font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-2xl transition-colors">Cancel</button>
                    <button type="submit" form="warehouseForm" class="flex-1 py-4 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-2xl shadow-lg shadow-teal-500/20 transition-all active:scale-95" x-text="editMode ? 'Update Warehouse' : 'Create Warehouse'"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function warehouseManager() {
    return {
        showModal: false,
        editMode: false,
        editingId: null,
        warehouses: [],
        formData: {
            warehouseName: '',
            location: '',
            warehouseType: 'Main'
        },
        closeModal() {
            this.showModal = false;
            this.editMode = false;
            this.editingId = null;
            this.formData = { warehouseName: '', location: '', warehouseType: 'Main' };
        },
        editWarehouse(wh) {
            this.editMode = true;
            this.editingId = wh.WarehouseID;
            this.formData = {
                warehouseName: wh.WarehouseName,
                location: wh.Location,
                warehouseType: wh.WarehouseType
            };
            this.showModal = true;
        },
        async fetchWarehouses() {
            try {
                const response = await fetch('/warehouses');
                this.warehouses = await response.json();
            } catch (error) {
                console.error('Failed to fetch warehouses');
            }
        },
        async saveWarehouse() {
            try {
                const url = this.editMode ? `/warehouses/${this.editingId}` : '/warehouses';
                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    this.closeModal();
                    this.fetchWarehouses();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Connection error');
            }
        }
    }
}
</script>
@endsection
