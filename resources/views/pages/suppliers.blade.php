@extends('layouts.app')

@section('content')
    <div x-data="supplierManager()" x-init="init()" class="min-h-screen pb-20">
        <!-- Header Section -->
        <div class="mb-14 flex flex-col lg:flex-row lg:items-end justify-between gap-10 px-4">
            <div class="space-y-2">
                <p
                    class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.4em] animate-in fade-in slide-in-from-left duration-700 mt-1">
                    Strategic Partnerships</p>
                <h1 class="text-5xl font-black text-slate-800 dark:text-white tracking-tight uppercase leading-none">
                    Suppliers Registry</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium max-w-md mt-2">Orchestrate and manage your network
                    of medical supply providers and logistics partners.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <!-- Search & Filter Controls -->
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchSuppliers()"
                        placeholder="Search suppliers..."
                        class="pl-14 pr-6 h-14 bg-white dark:bg-slate-800 border-none rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-none focus:ring-4 focus:ring-indigo-500/10 w-full sm:w-80 font-bold transition-all placeholder:text-slate-400 dark:placeholder:text-slate-600">
                </div>

                <button @click="openAddModal()"
                    class="w-full sm:w-auto px-8 h-14 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-2xl shadow-indigo-500/20 transition-all active:scale-95 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Register Partner
                </button>
            </div>
        </div>

        <!-- Quick Stats/Filters -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div
                class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 shadow-sm transition-all hover:shadow-xl group">
                <p
                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover:text-indigo-500 transition-colors">
                    Active Partners</p>
                <p class="text-4xl font-black text-slate-800 dark:text-white" x-text="suppliers.length"></p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] border border-slate-100 dark:border-slate-700/50 shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Sorted By</p>
                <div class="flex items-center text-slate-800 dark:text-white font-black w-full gap-2">
                    <div class="relative group w-full">
                        <select x-model="sortBy" @change="fetchSuppliers()"
                            class="appearance-none w-full px-6 py-3 bg-slate-50 dark:bg-slate-900/50 text-indigo-600 dark:text-indigo-400 font-black text-[10px] uppercase tracking-widest rounded-[1.5rem] border border-slate-100 dark:border-slate-700/50 shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer pr-10">
                            <option value="Name" class="dark:bg-slate-800">Name</option>
                            <option value="CreatedAt" class="dark:bg-slate-800">Join Date</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-indigo-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <button @click="toggleDirection()"
                        class="shrink-0 p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-800 text-indigo-600 dark:text-indigo-400 rounded-2xl transition-colors shadow-sm">
                        <svg class="w-4 h-4 transition-transform duration-300"
                            :class="sortDirection === 'desc' ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <template x-if="loading">
            <div class="flex flex-col items-center justify-center py-40 animate-pulse">
                <div class="w-16 h-16 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-6"></div>
                <p class="font-black text-slate-400 uppercase tracking-widest text-[10px]">Syncing Registry...</p>
            </div>
        </template>

        <!-- Content Grid -->
        <template x-if="!loading">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="supplier in suppliers" :key="supplier.SupplierID">
                    <div
                        class="group bg-white dark:bg-slate-800 p-10 rounded-[3rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-100 dark:border-slate-700/50 hover:border-indigo-500/30 transition-all hover:translate-y-[-8px] duration-500 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-8">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center font-black text-2xl shadow-inner group-hover:scale-110 transition-transform duration-500"
                                    x-text="supplier.Name.charAt(0)"></div>
                                <div class="flex gap-2">
                                    <button @click="openEditModal(supplier)"
                                        class="p-3 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-2xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button @click="deleteSupplier(supplier.SupplierID)"
                                        class="p-3 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-2xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 leading-tight"
                                x-text="supplier.Name"></h3>
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-6">Certified
                                Provider</p>

                            <div class="space-y-4 mb-10">
                                <div class="flex items-start group/info">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center mr-4 shrink-0 transition-colors group-hover/info:border-indigo-500/30">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-bold leading-relaxed"
                                        x-text="supplier.Address || 'Primary location details not specified'"></p>
                                </div>
                                <div class="flex items-center group/info">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center mr-4 shrink-0 transition-colors group-hover/info:border-indigo-500/30">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-black"
                                        x-text="supplier.ContactInfo || 'No direct contact recorded'"></p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="pt-6 border-t border-slate-50 dark:border-slate-700/50 flex items-center justify-between">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                                x-text="'Registered ' + formatDate(supplier.CreatedAt)"></p>
                            <button
                                class="text-[9px] font-black text-indigo-500 uppercase tracking-widest hover:tracking-[0.2em] transition-all">Procurement
                                Logs →</button>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <template x-if="suppliers.length === 0">
                    <div
                        class="col-span-full py-40 text-center bg-white dark:bg-slate-800 rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-slate-700/50">
                        <div
                            class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <p class="font-black text-slate-800 dark:text-white uppercase tracking-widest mb-2">Registry Empty
                        </p>
                        <p class="text-slate-400 text-sm font-medium">No partners matching your search criteria were found.
                        </p>
                    </div>
                </template>
            </div>
        </template>

        <!-- Modal -->
        <div x-show="showModal" style="display:none;"
            class="fixed inset-0 z-[200] grid place-items-center overflow-y-auto p-4 py-12 lg:p-12 backdrop-blur-sm bg-slate-1200/70"
            x-transition.opacity x-cloak @click.self="closeModal()">

            <!-- Modal Content -->
            <div
                class="relative z-10 bg-white dark:bg-slate-800 w-full max-w-xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col my-auto transition-all animate-in zoom-in-95 duration-200">
                <div
                    class="p-10 border-b border-slate-100 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                    <div>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.3em] mb-1"
                            x-text="editMode ? 'Registry Update' : 'New Registration'"></p>
                        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight"
                            x-text="editMode ? 'Edit Partner' : 'Register Partner'"></h2>
                    </div>
                </div>

                <form @submit.prevent="submitForm()" class="p-10 space-y-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Official Entity
                            Name</label>
                        <input type="text" x-model="formData.Name" required
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contact
                            Reference (Email/Phone)</label>
                        <input type="text" x-model="formData.ContactInfo"
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Primary Office
                            Address</label>
                        <textarea x-model="formData.Address" rows="3"
                            class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-indigo-500/10 transition-all resize-none"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="closeModal()"
                            class="flex-1 py-5 font-black text-slate-400 uppercase tracking-widest text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2rem] transition-all">Cancel</button>
                        <button type="submit"
                            class="flex-[2] py-5 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-xl shadow-indigo-500/20 transition-all active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
                            :disabled="submitting">
                            <span x-show="!submitting" x-text="editMode ? 'Sync Changes' : 'Confirm Registration'"></span>
                            <span x-show="submitting">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </template>
    </div>

    <script>
        function supplierManager() {
            return {
                suppliers: [],
                loading: true,
                showModal: false,
                submitting: false,
                searchQuery: '',
                sortBy: 'Name',
                sortDirection: 'asc',
                editMode: false,
                currentId: null,
                formData: {
                    Name: '',
                    Address: '',
                    ContactInfo: ''
                },

                init() {
                    this.fetchSuppliers();
                },

                async fetchSuppliers() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/suppliers?search=${this.searchQuery}&sort=${this.sortBy}&direction=${this.sortDirection}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const result = await response.json();
                        this.suppliers = result.suppliers;
                    } catch (e) {
                        console.error('Failed to fetch suppliers');
                    } finally {
                        this.loading = false;
                    }
                },

                toggleDirection() {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    this.fetchSuppliers();
                },

                openAddModal() {
                    this.editMode = false;
                    this.formData = { Name: '', Address: '', ContactInfo: '' };
                    this.showModal = true;
                },

                openEditModal(supplier) {
                    this.editMode = true;
                    this.currentId = supplier.SupplierID;
                    this.formData = {
                        Name: supplier.Name,
                        Address: supplier.Address,
                        ContactInfo: supplier.ContactInfo
                    };
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                async submitForm() {
                    this.submitting = true;
                    const url = this.editMode ? `/suppliers/${this.currentId}` : '/suppliers';
                    const method = this.editMode ? 'PUT' : 'POST';

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.formData)
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.closeModal();
                            this.fetchSuppliers();
                        } else {
                            alert(result.message || 'Submission failed');
                        }
                    } catch (e) {
                        alert('Connection error');
                    } finally {
                        this.submitting = false;
                    }
                },

                async deleteSupplier(id) {
                    if (!confirm('Are you sure you want to remove this partner from the registry?')) return;

                    try {
                        const response = await fetch(`/suppliers/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.fetchSuppliers();
                        }
                    } catch (e) {
                        alert('Deletion failed');
                    }
                },

                formatDate(dateStr) {
                    if (!dateStr) return 'N/A';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 99px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
@endsection