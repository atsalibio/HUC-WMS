@extends('layouts.app')

@section('content')
    <div x-data="requisitionManager()" x-init="init()">
        <!-- Header -->
        <div class="mb-10 flex justify-between items-end px-4">
            <div>
                <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1.5">Central Distribution</p>
                <h1 class="text-4xl font-black text-slate-800 dark:text-white">Supply Requisitions</h1>
            </div>
            <button @click="openModal()"
                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Requisition
            </button>
        </div>

        <!-- Requisition List -->
        <div class="grid grid-cols-1 gap-6">
            <div
                class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl overflow-hidden border border-slate-200 dark:border-slate-700/50">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 dark:bg-slate-900/50">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Reference</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Health Center</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Requested By</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Date
                                </th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    Status</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($requisitions as $req)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div
                                            class="font-black text-slate-800 dark:text-white group-hover:text-blue-600 transition-colors">
                                            {{ $req->RequisitionNumber }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $req->items->count() }}
                                            Items Requested</div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">
                                            Recipient Station</div>
                                        <div class="text-sm font-black text-slate-800 dark:text-white">
                                            {{ $req->healthCenter->Name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-500 font-medium">
                                        {{ ($req->user->FName ?? '') . ' ' . ($req->user->LName ?? '') }}
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-500 font-medium">
                                        {{ $req->RequestDate->format('M d, Y') }}
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $statusClass = match ($req->StatusType) {
                                                'Approved' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                                'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                                'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                                default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                            {{ $req->StatusType }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button data-req="{{ json_encode($req, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG) }}"
                                            @click="openDetailsModal(JSON.parse($el.dataset.req))"
                                            class="p-2 text-slate-400 hover:text-blue-500 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-medium italic">No
                                        requisitions found. Central stock is safe... for now.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Requisition Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] grid place-items-center overflow-y-auto p-4 py-12 lg:p-12 backdrop-blur-sm scrollbar-hide bg-slate-1200/60"
            x-cloak @click.self="closeModal()">

            <!-- Modal Content -->
            <div
                class="relative z-10 w-full max-w-4xl bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 animate-in zoom-in-95 duration-200 flex flex-col my-auto transition-all">

                <!-- Modal Header (Static) -->
                <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">New Distribution
                                Record</p>
                            <h2 class="text-3xl font-black text-slate-800 dark:text-white">Create Requisition</h2>
                        </div>
                        <button @click="closeModal()"
                            class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <form id="requisitionForm" @submit.prevent="submitRequisition" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Receiving
                                    Health Center</label>
                                <select x-model="formData.healthCenterId" required :disabled="userRole === 'Health Center Staff'"
                                    class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all disabled:opacity-75 disabled:cursor-not-allowed">
                                    <option value="">Select Target Destination...</option>
                                    @foreach($healthCenters as $hc)
                                        <option value="{{ $hc->HealthCenterID }}">{{ $hc->Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">
                                    Priority Status
                                </label>

                                <div class="flex gap-4">
                                    <label
                                        class="flex items-center gap-3 cursor-pointer group px-5 py-4 bg-slate-50 dark:bg-slate-900 rounded-3xl w-full border-2 border-transparent transition-all"
                                        :class="formData.isUrgent ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/10' : ''">

                                        <input
                                            type="checkbox"
                                            :checked="formData.isUrgent"
                                            @change="formData.isUrgent = $event.target.checked"
                                            class="w-6 h-6 rounded-lg border-2 border-slate-300 text-amber-600 focus:ring-amber-500/20 transition-all">

                                        <span
                                            class="text-xs font-black uppercase tracking-widest"
                                            :class="formData.isUrgent ? 'text-amber-700' : 'text-slate-400'">
                                            Mark as Urgent Request
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 text-left">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Requisition
                                Remarks</label>
                            <textarea x-model="formData.remarks"
                                placeholder="Enter purpose or special instructions for this requisition..." rows="2"
                                class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all"></textarea>
                        </div>

                        <!-- Dynamic Items Section -->
                        <div class="space-y-6 pt-6">
                            <div class="flex justify-between items-center px-4">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Requested Items
                                </h3>
                                <button type="button" @click="addItem()"
                                    class="px-5 py-3 bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-300 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-600 transition-all shadow-xl active:scale-95">
                                    + Add Supply Item
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <div
                                        class="flex flex-col md:flex-row gap-4 items-start md:items-end bg-slate-50 dark:bg-slate-900/40 p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:bg-white dark:hover:bg-slate-800">
                                        <div class="flex-1 w-full space-y-2 text-left">
                                            <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Medical
                                                Item</label>
                                            <select x-model="item.itemId" required
                                                class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-sm focus:ring-4 focus:ring-blue-500/10">
                                                <option value="">Select Inventory Item...</option>
                                                @foreach($items as $i)
                                                    <option value="{{ $i->ItemID }}">{{ $i->ItemName }}
                                                        ({{ $i->UnitOfMeasure }})</option>
                                                @endforeach
                                            </select>
                                            <!-- Stock remaining badge -->
                                            <template x-if="item.itemId">
                                                <div class="flex items-center gap-2 mt-1 px-1">
                                                    <span class="text-[10px] font-black uppercase tracking-widest"
                                                        :class="getStockColor(item.itemId)"
                                                        x-text="'Warehouse Stock: ' + (stockByItem[item.itemId] ?? 0).toLocaleString()"></span>
                                                    <template x-if="item.quantity && Number(item.quantity) > (stockByItem[item.itemId] ?? 0)">
                                                        <span class="text-[9px] font-black text-red-500 uppercase tracking-widest bg-red-50 dark:bg-red-900/20 px-2 py-0.5 rounded-full">⚠ Exceeds stock</span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="w-full md:w-32 space-y-2 text-left">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase ml-1">Quantity</label>
                                            <input type="number" x-model="item.quantity" required min="1"
                                                class="w-full px-4 py-3.5 bg-white dark:bg-slate-800 border-none rounded-2xl font-black dark:text-white focus:ring-4 focus:ring-blue-500/10"
                                                :class="item.quantity && Number(item.quantity) > (stockByItem[item.itemId] ?? 0) && item.itemId ? 'ring-2 ring-red-400' : ''">
                                        </div>
                                        <button type="button" @click="removeItem(index)"
                                            class="p-4 text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-700/50 rounded-2xl transition-all mb-0.5">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer (Static) -->
                <div class="p-10 border-t border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-900/20">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" @click="closeModal()"
                            class="flex-1 py-5 font-black text-slate-400 uppercase tracking-widest text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Cancel
                            Requisition</button>
                        <button type="submit" form="requisitionForm"
                            class="flex-[1.5] py-5 bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest rounded-[2.5rem] shadow-2xl shadow-blue-500/20 transition-all active:scale-95">
                            Post Requisition
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requisition Details Modal -->
        <div x-show="showDetailsModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[110] grid place-items-center overflow-y-auto p-4 py-12 backdrop-blur-sm scrollbar-hide bg-slate-900/60"
            x-cloak @click.self="closeDetailsModal()">

            <!-- Modal Panel -->
            <div
                class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto animate-in zoom-in-95 duration-200">
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 text-left">
                        <!-- Left: Core Info & Items -->
                        <div class="space-y-10">
                            <div class="grid grid-cols-2 gap-8">
                                <div class="col-span-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">📍
                                        Target Destination (Requesting Health Center)</label>
                                    <div
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-[1.5rem] text-sm font-black uppercase tracking-widest border-2 border-blue-100 dark:border-blue-500/20 shadow-sm shadow-blue-500/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <span x-text="activeReq.health_center?.Name || 'Unknown Station'"></span>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Current
                                        Status</label>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest"
                                        :class="{
                                                    'bg-green-50 text-green-600': activeReq.StatusType === 'Approved',
                                                    'bg-amber-50 text-amber-600': activeReq.StatusType === 'Pending',
                                                    'bg-blue-50 text-blue-600': activeReq.StatusType === 'Completed',
                                                    'bg-red-50 text-red-600': activeReq.StatusType === 'Rejected'
                                                }" x-text="activeReq.StatusType"></span>
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Requested
                                        By</label>
                                    <p class="font-bold text-slate-800 dark:text-slate-200"
                                        x-text="activeReq.user?.FName + ' ' + activeReq.user?.LName || 'Staff'"></p>
                                </div>
                            </div>

                            <!-- Item List with Per-Item Status -->
                            <div class="space-y-4">
                                <h3
                                    class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-800 pb-3">
                                    Requested Supplies (Review List)</h3>
                                <div class="space-y-3">
                                    <template x-for="item in activeReq.items || []">
                                        <div
                                            class="flex flex-col p-5 bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] border border-slate-100 dark:border-slate-800 transition-all hover:bg-white dark:hover:bg-slate-800">
                                            <div class="flex justify-between items-center mb-4">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 flex items-center justify-center font-black text-indigo-500 text-xs">
                                                        <span x-text="(item.item?.ItemName || 'I').charAt(0)"></span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300"
                                                            x-text="item.item?.ItemName"></p>
                                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                                                            x-text="item.QuantityRequested + ' ' + (item.item?.UnitOfMeasure || 'units')">
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <!-- Item-level Approval Checkboxes -->
                                                    <template
                                                        x-if="activeReq.StatusType === 'Pending' && ['Head Pharmacist', 'Administrator'].includes('{{ Auth::user()->Role }}')">
                                                        <div
                                                            class="flex gap-2 bg-white dark:bg-slate-700/50 p-1.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                                                            <button @click="item.ItemStatus = 'Approved'"
                                                                :class="item.ItemStatus === 'Approved' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-blue-600'"
                                                                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                                                Approve
                                                            </button>
                                                            <button @click="item.ItemStatus = 'Rejected'"
                                                                :class="item.ItemStatus === 'Rejected' ? 'bg-red-600 text-white shadow-lg' : 'text-slate-400 hover:text-red-500'"
                                                                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                                                Reject
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <template
                                                        x-if="activeReq.StatusType !== 'Pending' || !['Head Pharmacist', 'Administrator'].includes('{{ Auth::user()->Role }}')">
                                                        <span
                                                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest"
                                                            :class="item.StatusType === 'Approved' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600'"
                                                            x-text="item.StatusType || 'Approved'"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Right: History & Decision Notes -->
                        <div class="space-y-10">
                            <!-- Internal Remarks -->
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Decision
                                    Remarks / Notes</label>
                                <textarea x-model="decisionRemarks"
                                    placeholder="Add justification for approval or rejection of specific items..." rows="4"
                                    class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-[2rem] font-bold dark:text-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"></textarea>
                            </div>

                            <!-- Approval Lifecycle -->
                            <div class="space-y-4">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Transaction
                                    Timeline
                                </h3>
                                <div class="space-y-2">
                                    <template x-for="log in activeReq.approval_logs || []">
                                        <div
                                            class="flex items-center justify-between p-4 bg-white dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300"
                                                        x-text="log.Decision"></p>
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                                                        x-text="new Date(log.DecisionDate).toLocaleString()"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div
                    class="px-10 py-8 border-t border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20 flex justify-between items-center">
                    <div>
                        <template
                            x-if="activeReq.StatusType === 'Pending' && ['Head Pharmacist', 'Administrator'].includes('{{ Auth::user()->Role }}')">
                            <div class="flex gap-3">
                                <button @click="processDecision('Rejected')"
                                    class="px-8 py-4 bg-red-50 text-red-600 font-black text-xs uppercase tracking-widest rounded-[2rem] hover:bg-red-100 transition-all">Reject
                                    All</button>
                                <button @click="processDecision('Approved')"
                                    class="px-10 py-5 bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-xl shadow-blue-500/20 transition-all hover:bg-blue-700">Submit
                                    Decision</button>
                            </div>
                        </template>
                    </div>
                    <button @click="closeDetailsModal()"
                        class="px-10 py-5 bg-slate-900 dark:bg-slate-700 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-xl transition-all active:scale-95">
                        Close Summary
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>


    <script>
        function requisitionManager() {
            return {
                showModal: false,
                showDetailsModal: false,
                activeReq: {},
                decisionRemarks: '',
                stockByItem: @json($stockByItem ?? []),
                userRole: '{{ Auth::user()->Role }}',
                formData: {
                    healthCenterId: '{{ Auth::user()->HealthCenterID ?? "" }}',
                    isUrgent: false,
                    remarks: '',
                    items: [{ itemId: '', quantity: 1 }]
                },
                init() {
                    this.$watch('showModal', value => this.toggleScroll(value || this.showDetailsModal));
                    this.$watch('showDetailsModal', value => this.toggleScroll(value || this.showModal));
                },
                toggleScroll(lock) {
                    document.documentElement.classList.toggle('modal-lock', lock);
                },
                openDetailsModal(req) {
                    console.log("Opening details for Req:", req.RequisitionNumber);
                    // Deep clone to avoid modifying original until saved
                    this.activeReq = JSON.parse(JSON.stringify(req));
                    // Initialize ItemStatus for each item if not set
                    if (this.activeReq.items) {
                        this.activeReq.items.forEach(item => {
                            if (!item.ItemStatus) item.ItemStatus = 'Approved';
                        });
                    }
                    this.decisionRemarks = '';
                    this.showDetailsModal = true;
                },
                closeDetailsModal() {
                    this.showDetailsModal = false;
                },
                openModal() {
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                },
                addItem() {
                    this.formData.items.push({ itemId: '', quantity: 1 });
                },
                removeItem(index) {
                    if (this.formData.items.length > 1) {
                        this.formData.items.splice(index, 1);
                    }
                },
                getStockColor(itemId) {
                    const stock = this.stockByItem[itemId] ?? 0;
                    if (stock === 0) return 'text-red-500';
                    if (stock <= 50) return 'text-amber-500';
                    return 'text-emerald-500';
                },
                async submitRequisition() {
                    if (!this.formData.healthCenterId || this.formData.items.length === 0) {
                        alert("Please select a target health center and add at least one item.");
                        return;
                    }

                    try {
                        const response = await fetch('/requisitions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();
                        if (result.success) {
                            alert('Requisition submitted for approval!');
                            location.reload();
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        alert('Connection error');
                    }
                },
                async processDecision(overallStatus) {
                    if (!confirm(`Confirm requisition decision: ${overallStatus}?`)) return;

                    // Prepare item-level statuses
                    const itemStatuses = {};
                    if (this.activeReq.items) {
                        this.activeReq.items.forEach(item => {
                            itemStatuses[item.RequisitionItemID] = overallStatus === 'Rejected' ? 'Rejected' : item.ItemStatus;
                        });
                    }

                    if (overallStatus === 'Approved') {
                        // If approving, ensure at least one item is approved
                        const hasApproved = Object.values(itemStatuses).some(status => status === 'Approved');
                        if (!hasApproved) {
                            alert('At least one item must be approved to approve the requisition.');
                            return;
                        }
                    }

                    // Status flows: Pending → Approved (queued for Issuance) → Completed
                    // Rejected closes the requisition immediately.
                    let finalStatus = overallStatus;

                    try {
                        const response = await fetch(`/requisitions/${this.activeReq.RequisitionID}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status: finalStatus,
                                itemStatuses: itemStatuses,
                                remarks: this.decisionRemarks
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            alert(`Requisition decision processed successfully!`);
                            location.reload();
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