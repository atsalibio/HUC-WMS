@extends('layouts.app')

@section('content')
    <div class="space-y-6" x-data="inventoryManager()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 px-4">
            <div class="space-y-1">
                <h2 class="text-4xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Inventory Depot</h2>
                <p class="text-slate-500 font-black text-[10px] uppercase tracking-[0.3em] mt-1.5">Real-time cross-batch
                    stock monitoring</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openCreateModal()"
                    class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95">
                    + New Registry
                </button>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <!-- Category Tabs -->
            <div
                class="p-1.5 bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 inline-flex gap-1">
                <template x-for="cat in ['all', 'medicine', 'utility', 'others']">
                    <button @click="category = cat"
                        :class="category === cat ? 'bg-slate-900 text-white dark:bg-blue-600' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'"
                        class="px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-widest transition-all"
                        x-text="cat"></button>
                </template>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <!-- Search -->
                <div class="relative group w-full sm:w-64">
                    <div
                        class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Search entity..."
                        class="w-full pl-11 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 outline-none transition-all dark:text-white">
                </div>

                <!-- Sort -->
                <div class="flex items-center gap-2">
                    <div class="relative group">
                        <select x-model="sortBy"
                            class="appearance-none px-6 py-3 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-black text-[10px] uppercase tracking-widest rounded-[1.5rem] border border-slate-200 dark:border-slate-700 shadow-sm focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer min-w-[200px] pr-10">
                            <option value="name">Entity Name</option>
                            <option value="expiry">Next Expiry (FEFO)</option>
                            <option value="issuance">Last Issuance Date</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                        class="p-3 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95">
                        <svg x-show="sortDirection === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path>
                        </svg>
                        <svg x-show="sortDirection === 'desc'" style="display:none;" class="w-4 h-4" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left table-auto">
                    <thead>
                        <tr
                            class="text-[10px] uppercase tracking-[0.2em] font-black text-slate-400 border-b border-slate-50 dark:border-slate-800">
                            <th class="px-10 py-6">Medical Entity</th>
                            <th class="px-10 py-6">Details</th>
                            <th class="px-10 py-6 text-center">In Stock</th>
                            <th class="px-10 py-6">Next Expiry (FEFO)</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50 dark:divide-slate-700/50">
                        <template x-for="item in filteredInventory" :key="item.ItemID">
                            <tr @click="toggleItem(item.ItemID)"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group cursor-pointer border-b border-slate-50 dark:border-slate-800">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-xl shadow-inner"
                                            x-text="getEmoji(item.Category)"></div>
                                        <div>
                                            <p class="text-xs font-black text-slate-800 dark:text-white"
                                                x-text="item.ItemName"></p>
                                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest"
                                                x-text="item.Category"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <p
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">
                                        Brand: <span class="text-slate-600 dark:text-slate-300"
                                            x-text="item.Brand || 'Generic'"></span><br>
                                        Last Issued: <span class="text-slate-600 dark:text-slate-300"
                                            :class="!item.LastIssuance ? 'italic opacity-50' : ''"
                                            x-text="item.LastIssuance ? formatDate(item.LastIssuance) : 'Never Issued'"></span>
                                    </p>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <div class="inline-flex items-baseline gap-1">
                                        <span class="text-xl font-black text-slate-800 dark:text-white tabular-nums"
                                            x-text="Number(item.TotalQuantity).toLocaleString()"></span>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                                            x-text="item.Unit"></span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-2">
                                        <p class="text-[11px] font-bold"
                                            :class="isExpiringSoon(item.NextExpiry) ? 'text-red-500' : 'text-slate-600 dark:text-slate-400'"
                                            x-text="formatDate(item.NextExpiry)"></p>
                                        <svg :class="expandedItems.includes(item.ItemID) ? 'rotate-180' : ''"
                                            class="w-4 h-4 text-slate-300 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click.stop="openViewModal(item)"
                                            class="w-9 h-9 inline-flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Sub-batches sorted by Date Received (Issuance Order) -->
                            <tr x-show="expandedItems.includes(item.ItemID)"
                                class="bg-slate-50/50 dark:bg-slate-900/40 border-l-4 border-blue-500 border-b border-slate-50 dark:border-slate-800"
                                x-cloak>
                                <td colspan="5" class="px-10 py-8">
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Batch Ledger — Sorted by Date Received</h4>
                                            <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full"
                                                x-text="(batches[item.ItemID] || []).length + ' active batches'"></span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-left">
                                            <template x-for="(batch, idx) in sortedBatches(item.ItemID)" :key="batch.BatchID">
                                                <div
                                                    class="p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 flex flex-col justify-between hover:shadow-md hover:border-blue-200 dark:hover:border-blue-700/50 transition-all">
                                                    <div class="flex justify-between items-start mb-4">
                                                        <div>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Batch #<span x-text="idx + 1"></span></p>
                                                            <p class="text-[11px] font-black text-slate-800 dark:text-white font-mono" x-text="batch.BatchNumber || ('BCH-' + batch.BatchID)"></p>
                                                            <template x-if="batch.LotNumber">
                                                                <p class="text-[9px] font-bold text-slate-400 mt-0.5" x-text="'Lot: ' + batch.LotNumber"></p>
                                                            </template>
                                                        </div>
                                                        <span
                                                            class="px-2 py-1 bg-slate-50 dark:bg-slate-900 text-[8px] font-black text-slate-400 rounded-lg"
                                                            x-text="'Ref: ' + (batch.PONumber || 'N/A')"></span>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Qty on Hand</p>
                                                            <p class="text-lg font-black text-slate-800 dark:text-white"
                                                                x-text="Number(batch.QuantityOnHand).toLocaleString()"></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Unit Cost</p>
                                                            <p class="text-sm font-black text-slate-600 dark:text-slate-300"
                                                                x-text="batch.UnitCost ? '₱' + Number(batch.UnitCost).toLocaleString('en-PH', {minimumFractionDigits:2}) : '—'"></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Received</p>
                                                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-300" x-text="formatDate(batch.DateReceived)"></p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Expiry</p>
                                                            <p class="text-[10px] font-bold"
                                                                :class="isExpiringSoon(batch.ExpiryDate) ? 'text-red-500' : 'text-slate-600 dark:text-slate-300'"
                                                                x-text="formatDate(batch.ExpiryDate)"></p>
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

        <!-- Library Details Modal -->
        <div x-show="showViewModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide bg-slate-1200/70"
            x-cloak @click.self="showViewModal = false">

            <!-- Modal Panel -->
            <div
                class="relative z-10 w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">
                <div
                    class="px-8 pt-8 pb-6 border-b border-slate-50 dark:border-slate-700/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white" x-text="selectedItem.ItemName"></h2>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1"
                            x-text="selectedItem.Category"></p>
                    </div>
                    <button @click="showViewModal = false"
                        class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Brand</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white"
                                x-text="selectedItem.Brand || 'Generic'"></p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Base Unit</p>
                            <p class="text-xs font-black text-slate-800 dark:text-white"
                                x-text="selectedItem ? selectedItem.Unit : ''"></p>
                        </div>
                    </div>
                </div>
                <!--- Batch Details and other info can be added here in the future --->
                <div class="p-8 space-y-6">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white"> Batch Details </h2>
                        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1"
                            x-text="itemBatches.length + ' Available Batches'"></p>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[2.5rem] overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead
                                class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th
                                        class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase">
                                        Batch Number</th>
                                    <th
                                        class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase text-center">
                                        Quantity</th>
                                    <th
                                        class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase text-center">
                                        Unit Price</th>
                                    <th
                                        class="px-10 py-5 text-[9px] font-black text-slate-400 uppercase text-right">
                                        Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                <template x-if="itemBatches.length > 0">
                                    <template x-for="batch in itemBatches" :key="batch.BatchID">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/10 transition-colors">
                                            <td class="px-10 py-5">
                                                <p class="font-black text-slate-800 dark:text-white text-xs"
                                                    x-text="batch.BatchNumber || 'Unknown Entity'"></p>
                                                <p class="text-[9px] text-blue-600 font-black uppercase tracking-widest mt-0.5"
                                                    x-text="batch.LotNumber || ''"></p>
                                            </td>
                                            <td class="px-10 py-5 font-black text-slate-800 dark:text-white text-center text-sm"
                                                x-text="batch.QuantityOnHand || 0"></td>
                                            <td class="px-10 py-5 font-black text-slate-800 dark:text-white text-center text-sm"
                                                x-text="'₱' + Number(batch.UnitCost || 0).toLocaleString(undefined, {minimumFractionDigits: 2})">
                                            </td>
                                            <td class="px-10 py-5 font-black text-slate-800 dark:text-white text-right text-sm"
                                                x-text="batch.ExpiryDate || 'N/A'">
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template
                                    x-if="itemBatches.length === 0">
                                    <tr>
                                        <td colspan="4"
                                            class="px-10 py-10 text-center text-slate-300 italic text-xs uppercase tracking-widest">
                                            No Batch Information Available</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-8 pb-8">
                    <button @click="showViewModal = false"
                        class="w-full py-4 bg-slate-900 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-800 transition-all">Close
                        Viewer</button>
                </div>
            </div>
        </div>

        <!-- Create Registry Modal -->
        <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide bg-slate-1200/60"
            x-cloak @click.self="closeCreateModal()">

            <!-- Modal Panel -->
            <div
                class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-2xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">

                <!-- Header -->
                <div
                    class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30 flex justify-between items-center">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white uppercase tracking-tight">New Medical
                            Entity</h2>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Register a master
                            item profile</p>
                    </div>
                    <button @click="closeCreateModal()"
                        class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <form id="createItemForm" @submit.prevent="submitItem" class="space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Entity
                                Name</label>
                            <input type="text" x-model="formData.ItemName" required placeholder="e.g. Paracetamol 500mg"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Brand
                                    Name (Optional)</label>
                                <input type="text" x-model="formData.Brand" placeholder="e.g. Biogesic"
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dosage
                                    Form</label>
                                <input type="text" x-model="formData.DosageUnit" placeholder="e.g. 500mg/Tablet"
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Classification
                                    Type</label>
                                <select x-model="formData.ItemType" required
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                                    <template x-for="cat in ['Medicine', 'Supply', 'Equipment', 'Others']">
                                        <option :value="cat" x-text="cat"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Base
                                    Unit</label>
                                <select x-model="formData.UnitOfMeasure" required
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                                    <template
                                        x-for="uom in ['Tablet', 'Capsule', 'Bottle', 'Vial', 'Ampule', 'Sachet', 'Box', 'Piece', 'Unit']">
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
                        <button type="button" @click="closeCreateModal()"
                            class="flex-1 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Cancel</button>
                        <button type="submit" form="createItemForm"
                            class="flex-1 py-5 bg-slate-900 dark:bg-blue-600 hover:scale-[1.02] text-white font-black text-xs uppercase tracking-[0.2em] rounded-[2.5rem] shadow-2xl transition-all active:scale-95">Expand
                            Registry</button>
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
                    itemBatches: [],
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
                    searchQuery: '',
                    sortBy: 'issuance',
                    sortDirection: 'desc',

                    init() {
                        this.$watch('showViewModal', value => this.toggleScroll(value || this.showCreateModal));
                        this.$watch('showCreateModal', value => this.toggleScroll(value || this.showViewModal));
                    },
                    toggleScroll(lock) {
                        document.documentElement.classList.toggle('modal-lock', lock);
                    },

                    get filteredInventory() {
                        let items = [...this.inventory];

                        // 1. Search Filter
                        if (this.searchQuery) {
                            const query = this.searchQuery.toLowerCase();
                            items = items.filter(i =>
                                i.ItemName.toLowerCase().includes(query) ||
                                (i.Brand && i.Brand.toLowerCase().includes(query))
                            );
                        }

                        // 2. Category Filter
                        if (this.category !== 'all') {
                            items = items.filter(i => {
                                const mapped = this.mapCategory(i.Category);
                                return mapped === this.category;
                            });
                        }

                        // 3. Sorting
                        items.sort((a, b) => {
                            let diff = 0;
                            if (this.sortBy === 'name') {
                                diff = a.ItemName.localeCompare(b.ItemName);
                            } else if (this.sortBy === 'expiry') {
                                const dateA = a.NextExpiry && a.NextExpiry !== 'N/A' ? new Date(a.NextExpiry) : new Date('9999-12-31');
                                const dateB = b.NextExpiry && b.NextExpiry !== 'N/A' ? new Date(b.NextExpiry) : new Date('9999-12-31');
                                diff = dateA - dateB;
                            } else if (this.sortBy === 'issuance') {
                                const dateA = a.LastIssuance ? new Date(a.LastIssuance) : new Date('1970-01-01');
                                const dateB = b.LastIssuance ? new Date(b.LastIssuance) : new Date('1970-01-01');
                                diff = dateA - dateB;
                            }

                            return this.sortDirection === 'asc' ? diff : -diff;
                        });

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
                        this.selectedItem = item;
                        this.itemBatches = this.sortedBatches(item.ItemID);
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

                    // Returns batches for an item sorted by DateReceived ascending (issuance order)
                    sortedBatches(itemId) {
                        const list = this.batches[itemId] || [];
                        return [...list].sort((a, b) => {
                            const da = a.DateReceived ? new Date(a.DateReceived) : new Date('9999-12-31');
                            const db = b.DateReceived ? new Date(b.DateReceived) : new Date('9999-12-31');
                            return da - db;
                        });
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
