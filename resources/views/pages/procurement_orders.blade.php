@extends('layouts.app')

@section('content')
    <div class="space-y-6" x-data="poManager()">
        <!-- Header -->
        <div class="mb-10 flex justify-between items-center px-4">
            <div>
                <h1 class="text-4xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Procurement Orders
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] mt-1.5">
                    Manage bulk orders and supplier contracts.</p>
            </div>
            <button @click="openModal()"
                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                New Order
            </button>
        </div>

        <!-- PO Table -->
        <div
            class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-100 dark:border-slate-700/50">
                        <tr
                            class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                            <th class="px-10 py-6">Order Details</th>
                            <th class="px-10 py-6">Supplier Entity</th>
                            <th class="px-10 py-6">Timeline</th>
                            <th class="px-10 py-6">Status</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                        @forelse($procurementOrders as $po)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="font-black text-slate-800 dark:text-white text-xs">{{ $po->PONumber }}</div>
                                    <div class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1">
                                        {{ $po->DocumentType }} | {{ $po->healthCenter->Name ?? 'Main Depot' }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    <div
                                        class="text-[11px] font-black text-slate-700 dark:text-slate-300 uppercase tracking-tight">
                                        {{ $po->supplier->Name ?? $po->SupplierName }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="text-[11px] font-bold text-slate-500">{{ $po->PODate->format('M d, Y') }}</div>
                                </td>
                                <td class="px-10 py-6">
                                    @php
                                        $statusClass = match ($po->StatusType) {
                                            'Approved' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                            'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                            'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                                        };
                                    @endphp
                                    <span
                                        class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                        {{ $po->StatusType }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button @click="viewDetails('{{ $po->POID }}')"
                                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:border-blue-500/30 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-10 py-20 text-center text-slate-300 font-black text-[10px] uppercase tracking-[0.4em] italic">
                                    No procurement records detected</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== CREATE MODAL ===== -->
        <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide bg-slate-1200/70"
            x-cloak @click.self="closeModal()">

            <!-- Modal Panel -->
            <div
                class="relative z-10 w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto transition-all animate-in zoom-in-95 duration-200">

                <!-- Header -->
                <div
                    class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30 flex justify-between items-center flex-shrink-0">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-tight">New
                            Procurement</h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Create a new bulk
                            supply order</p>
                    </div>
                    <button @click="closeModal()"
                        class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <form id="procurementForm" @submit.prevent="submitPO" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Supplier
                                        Entity</label>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="manualSupplier" x-model="formData.isManualSupplier"
                                            class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                        <label for="manualSupplier"
                                            class="text-[10px] font-black text-slate-400 uppercase cursor-pointer">Manual
                                            Entry</label>
                                    </div>
                                </div>

                                <template x-if="!formData.isManualSupplier">
                                    <select x-model="formData.supplier_id"
                                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-xs">
                                        <option value="">Select Registered Supplier...</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->SupplierID }}">{{ $s->Name }}</option>
                                        @endforeach
                                    </select>
                                </template>
                                <template x-if="formData.isManualSupplier">
                                    <div class="space-y-3">
                                        <input type="text" x-model="formData.supplierName" placeholder="Supplier Name"
                                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-xs">
                                        <textarea x-model="formData.supplierAddress" placeholder="Supplier Address" rows="2"
                                            class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-xs"></textarea>
                                    </div>
                                </template>
                            </div>

                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Assign
                                    to Health Center</label>
                                <select x-model="formData.health_center_id"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-xs">
                                    <option value="">Main Storage Office (GSO)</option>
                                    @foreach($healthCenters as $hc)
                                        <option value="{{ $hc->HealthCenterID }}">{{ $hc->Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left border-t border-slate-50 dark:border-slate-800 pt-8">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Document
                                    Reference Type</label>
                                <div class="flex gap-3">
                                    <template x-for="type in ['Purchase Order', 'Contract', 'Both']">
                                        <label
                                            class="flex-1 flex items-center justify-center py-3 px-4 rounded-2xl border-2 border-slate-100 dark:border-slate-800 cursor-pointer transition-all"
                                            :class="formData.refFileType === type ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10 text-blue-600' : 'hover:border-blue-200'">
                                            <input type="radio" name="refFT" :value="type" x-model="formData.refFileType"
                                                class="hidden">
                                            <span class="text-[10px] font-black uppercase tracking-widest"
                                                x-text="type"></span>
                                        </label>
                                    </template>
                                </div>

                                <div
                                    class="mt-4 p-8 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-[2rem] text-center hover:border-blue-500/50 transition-all group">
                                    <input type="file" @change="handlePhotoUpload($event)" class="hidden" id="refPhoto"
                                        accept="image/*">
                                    <label for="refPhoto" class="cursor-pointer">
                                        <div class="text-slate-400 group-hover:text-blue-500 transition-colors">
                                            <svg class="w-8 h-8 mx-auto mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <p class="text-[10px] font-black uppercase tracking-widest"
                                                x-text="formData.photoName || 'Upload Reference Photo'"></p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Reference
                                    Number</label>
                                <input type="text" x-model="formData.contractNumber" placeholder="PO-2026-X or Contract ID"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-xs">

                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div class="space-y-1">
                                        <label
                                            class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">Start
                                            Date</label>
                                        <input type="date" x-model="formData.contractStartDate"
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                    <div class="space-y-1">
                                        <label
                                            class="text-[8px] font-black text-slate-400 uppercase tracking-widest ml-1">End
                                            Date</label>
                                        <input type="date" x-model="formData.contractEndDate"
                                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="space-y-6 pt-8 border-t border-slate-50 dark:border-slate-800">
                            <div class="flex justify-between items-center px-2">
                                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">Order
                                    Line Items</h3>
                                <button type="button" @click="addItem()"
                                    class="px-6 py-3 bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-300 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-600 transition-all shadow-xl active:scale-95">
                                    + Add Entity
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <div
                                        class="bg-slate-50 dark:bg-slate-900/40 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm group relative">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                            <div class="md:col-span-2 space-y-2">
                                                <label
                                                    class="text-[10px] font-black text-slate-400 uppercase ml-1 tracking-widest">Medical
                                                    Item</label>
                                                <select x-model="item.itemId" required
                                                    class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-xs focus:ring-4 focus:ring-blue-500/10">
                                                    <option value="">Search Inventory Catalog...</option>
                                                    @foreach($items as $i)
                                                        <option value="{{ $i->ItemID }}">{{ $i->ItemName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[10px] font-black text-slate-400 uppercase ml-1 tracking-widest">Quantity</label>
                                                <input type="number" x-model="item.quantity" required min="1"
                                                    class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-2xl font-black dark:text-white focus:ring-4 focus:ring-blue-500/10 text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[10px] font-black text-slate-400 uppercase ml-1 tracking-widest">Unit
                                                    Price (₱)</label>
                                                <input type="number" x-model="item.unitCost" step="0.01"
                                                    class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-2xl font-black dark:text-white focus:ring-4 focus:ring-blue-500/10 text-sm">
                                            </div>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 pt-8 border-t border-slate-200 dark:border-slate-700/50">
                                            <div class="space-y-2">
                                                <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Expiry
                                                    Timeline</label>
                                                <input type="date" x-model="item.expiryDate"
                                                    class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-xs">
                                            </div>
                                        </div>

                                        <button type="button" @click="removeItem(index)"
                                            class="absolute -top-3 -right-3 p-3 bg-white dark:bg-slate-800 text-red-400 hover:text-red-500 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-700 transition-all opacity-0 group-hover:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div
                    class="px-10 py-8 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20 flex-shrink-0">
                    <div class="flex flex-col sm:flex-row gap-6">
                        <button type="button" @click="closeModal()"
                            class="flex-1 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Discard
                            Draft</button>
                        <button type="submit" form="procurementForm"
                            class="flex-1 py-5 bg-slate-900 dark:bg-blue-600 hover:scale-[1.02] text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl transition-all active:scale-95">Complete
                            Procurement</button>
                    </div>
                </div>

            </div>{{-- end modal panel --}}
        </div>{{-- end create modal --}}

        <!-- ===== DETAILS MODAL ===== -->
        <div x-show="showDetailsModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide bg-slate-1200/70"
            x-cloak @click.self="closeDetailsModal()">

            <!-- Modal Panel -->
            <div
                class="relative z-10 w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">

                <!-- Header -->
                <div
                    class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30 flex justify-between items-center flex-shrink-0">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white"
                            x-text="selectedOrder ? 'Ref: ' + selectedOrder.PONumber : ''"></h2>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mt-1"
                            x-text="selectedOrder ? selectedOrder.DocumentType + ' | ' + (selectedOrder.health_center ? selectedOrder.health_center.Name : 'Main Storage') : ''">
                        </p>
                    </div>
                    <button @click="closeDetailsModal()"
                        class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div
                            class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Supplier Entity
                            </p>
                            <p class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight"
                                x-text="(selectedOrder && selectedOrder.supplier) ? selectedOrder.supplier.Name : (selectedOrder ? selectedOrder.SupplierName : 'N/A')">
                            </p>
                        </div>
                        <div
                            class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Order Date</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white"
                                x-text="selectedOrder ? formatDate(selectedOrder.PODate) : 'N/A'"></p>
                        </div>
                        <div
                            class="p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Current Status
                            </p>
                            <span
                                class="px-5 py-2 rounded-full text-[9px] font-black uppercase tracking-widest inline-block"
                                :class="selectedOrder ? getStatusClass(selectedOrder.StatusType) : ''"
                                x-text="selectedOrder ? selectedOrder.StatusType : 'N/A'"></span>
                        </div>

                        <template x-if="selectedOrder && selectedOrder.PhotoPath">
                            <div
                                class="md:col-span-3 p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Reference
                                    Document Photo</p>
                                <div
                                    class="relative group/img overflow-hidden rounded-2xl border-4 border-white dark:border-slate-800 shadow-lg">
                                    <img :src="'/' + selectedOrder.PhotoPath"
                                        class="w-full h-auto max-h-64 object-contain transition-transform duration-500 group-hover/img:scale-105">
                                    <a :href="'/' + selectedOrder.PhotoPath" target="_blank"
                                        class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-black uppercase tracking-widest">
                                        Open Full Size
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-[0.2em] px-2">Order
                            Manifest</h3>
                        <div
                            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] overflow-hidden">
                            <table class="w-full text-left text-sm">
                                <thead
                                    class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                                    <tr>
                                        <th
                                            class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                            Medical Item</th>
                                        <th
                                            class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">
                                            Qty</th>
                                        <th
                                            class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase tracking-widest text-right">
                                            Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    <template x-if="selectedOrder && selectedOrder.items && selectedOrder.items.length > 0">
                                        <template x-for="item in selectedOrder.items" :key="item.POItemID">
                                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10 transition-colors">
                                                <td class="px-10 py-5">
                                                    <p class="font-black text-slate-800 dark:text-white text-xs"
                                                        x-text="item.item ? item.item.ItemName : 'Unknown Entity'"></p>
                                                    <p class="text-[9px] text-blue-600 font-black uppercase tracking-widest mt-0.5"
                                                        x-text="item.item ? item.item.ItemType : ''"></p>
                                                </td>
                                                <td class="px-10 py-5 font-black text-slate-800 dark:text-white text-center text-sm"
                                                    x-text="Number(item.QuantityOrdered).toLocaleString()"></td>
                                                <td class="px-10 py-5 font-black text-slate-800 dark:text-white text-right text-sm"
                                                    x-text="'₱' + Number(item.UnitCost || 0).toLocaleString(undefined, {minimumFractionDigits: 2})">
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template
                                        x-if="!selectedOrder || !selectedOrder.items || selectedOrder.items.length === 0">
                                        <tr>
                                            <td colspan="3"
                                                class="px-10 py-10 text-center text-slate-300 italic text-xs uppercase tracking-widest">
                                                No line items recorded</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-10 py-8 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20 text-right flex-shrink-0 flex justify-between items-center">
                    <div>
                        <template x-if="selectedOrder && selectedOrder.StatusType === 'Pending'">
                            <div class="flex gap-3">
                                <button @click="updateStatus(selectedOrder.POID, 'Rejected')"
                                    class="px-8 py-4 bg-rose-50 text-rose-600 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-rose-100 transition-all border border-rose-100">Reject
                                    Order</button>
                                <button @click="updateStatus(selectedOrder.POID, 'Approved')"
                                    class="px-10 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all">Approve
                                    Supply Order</button>
                            </div>
                        </template>
                    </div>
                    <button @click="closeDetailsModal()"
                        class="px-12 py-5 bg-slate-900 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-2xl hover:bg-slate-800 shadow-xl transition-all">Close
                        Viewer</button>
                </div>

            </div>{{-- end modal panel --}}
        </div>{{-- end details modal --}}

    </div>{{-- end x-data --}}

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('poManager', () => ({
                    showCreateModal: false,
                    showDetailsModal: false,
                    selectedOrder: null,
                    orders: @json($procurementOrders),
                    formData: {
                        isManualSupplier: false,
                        supplier_id: '',
                        supplierName: '',
                        supplierAddress: '',
                        health_center_id: '',
                        refFileType: 'Purchase Order',
                        fileName: '',
                        contractNumber: '',
                        contractStartDate: '',
                        contractEndDate: '',
                        items: [{ itemId: '', quantity: 1, unitCost: 0, expiryDate: '' }]
                    },
                    init() {
                        this.$watch('showCreateModal', value => this.toggleScroll(value || this.showDetailsModal));
                        this.$watch('showDetailsModal', value => this.toggleScroll(value || this.showCreateModal));
                    },
                    toggleScroll(lock) {
                        document.documentElement.classList.toggle('modal-lock', lock);
                    },

                    openModal() {
                        this.showCreateModal = true;
                    },
                    closeModal() {
                        this.showCreateModal = false;
                        this.resetForm();
                    },
                    viewDetails(id) {
                        this.selectedOrder = this.orders.find(o => o.POID == id) || null;
                        if (this.selectedOrder) this.showDetailsModal = true;
                    },
                    closeDetailsModal() {
                        this.showDetailsModal = false;
                        this.selectedOrder = null;
                    },
                    formatDate(dateStr) {
                        if (!dateStr) return NULL;
                        return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    },
                    getStatusClass(status) {
                        return {
                            'Approved': 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                            'Pending': 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                            'Completed': 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                        }[status] || 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400';
                    },
                    addItem() {
                        this.formData.items.push({ itemId: '', quantity: 1, unitCost: 0, expiryDate: '' });
                    },
                    removeItem(index) {
                        if (this.formData.items.length > 1) {
                            this.formData.items.splice(index, 1);
                        }
                    },
                    handleFileUpload(e) {
                        const file = e.target.files[0];
                        if (file) this.formData.fileName = file.name;
                    },
                    resetForm() {
                        this.formData = {
                            isManualSupplier: false,
                            supplier_id: '',
                            supplierName: '',
                            supplierAddress: '',
                            health_center_id: '',
                            refFileType: 'Purchase Order',
                            fileName: '',
                            contractNumber: '',
                            contractStartDate: '',
                            contractEndDate: '',
                            items: [{ itemId: '', quantity: 1, unitCost: 0, expiryDate: '' }],
                            photoFile: null,
                            photoName: ''
                        };
                    },
                    handlePhotoUpload(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.formData.photoFile = file;
                            this.formData.photoName = file.name;
                        }
                    },
                    async submitPO() {
                        if ((!this.formData.supplier_id && !this.formData.supplierName) || this.formData.items.length === 0) {
                            alert('Please select/enter a supplier and add at least one item.');
                            return;
                        }
                        try {
                            const submissionData = new FormData();
                            submissionData.append('supplier_id', this.formData.supplier_id);
                            submissionData.append('supplier_name', this.formData.supplierName);
                            submissionData.append('supplier_address', this.formData.supplierAddress);
                            submissionData.append('health_center_id', this.formData.health_center_id);
                            submissionData.append('contract_number', this.formData.contractNumber);
                            submissionData.append('contract_start_date', this.formData.contractStartDate);
                            submissionData.append('contract_end_date', this.formData.contractEndDate);
                            submissionData.append('document_type', this.formData.refFileType);

                            if (this.formData.photoFile) {
                                submissionData.append('photo', this.formData.photoFile);
                            }

                            this.formData.items.forEach((item, index) => {
                                submissionData.append(`items[${index}][itemId]`, item.itemId);
                                submissionData.append(`items[${index}][quantity]`, item.quantity);
                                submissionData.append(`items[${index}][unitCost]`, item.unitCost);
                                submissionData.append(`items[${index}][expiryDate]`, item.expiryDate);
                            });

                            const response = await fetch('/procurement/orders', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: submissionData
                            });
                            const result = await response.json();
                            if (response.ok && result.success) {
                                alert('Order created successfully!');
                                location.reload();
                            } else {
                                alert('Error: ' + (result.message || 'Validation failed. Check file size.'));
                                console.error(result.errors);
                            }
                        } catch (error) {
                            alert('Connection error. Please try again.');
                        }
                    },
                    async updateStatus(id, status) {
                        if (!confirm(`Are you sure you want to mark this order as ${status}?`)) return;
                        try {
                            const response = await fetch(`/procurement/orders/${id}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ status })
                            });
                            const result = await response.json();
                            if (result.success) {
                                alert(`Order status updated to ${status}!`);
                                location.reload();
                            } else {
                                alert('Error: ' + result.message);
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
