@extends('layouts.app')

@section('content')
<div x-data="supplierManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-12 flex justify-between items-end">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Partner Network</p>
            <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">Suppliers Directory</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Manage medical supply vendors and contact registration.</p>
        </div>
        <button @click="openModal()" class="px-8 py-4 bg-slate-900 dark:bg-slate-700 text-white font-black text-xs uppercase tracking-widest rounded-3xl shadow-xl transition-all active:scale-95 flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Partner
        </button>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($suppliers as $supplier)
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-slate-200 dark:border-slate-700/50 hover:border-teal-500/30 transition-all group">
            <div class="flex items-center mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex items-center justify-center font-black text-xl text-slate-400 group-hover:bg-teal-500 group-hover:text-white transition-all shadow-inner">
                    {{ substr($supplier->Name, 0, 1) }}
                </div>
                <div class="ml-4 overflow-hidden">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white truncate" title="{{ $supplier->Name }}">{{ $supplier->Name }}</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Verified Supplier</p>
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex items-start">
                    <svg class="w-4 h-4 text-slate-300 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $supplier->Address ?? 'No address provided' }}</p>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-slate-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <p class="text-xs text-slate-500 font-bold">{{ $supplier->ContactNumber ?? 'No contact' }}</p>
                </div>
            </div>

            <button class="w-full py-3 bg-slate-50 dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-black text-[10px] uppercase tracking-widest rounded-2xl transition-all border border-transparent hover:border-slate-200">
                View Transaction History
            </button>
        </div>
        @empty
        <div class="col-span-full py-32 text-center bg-white dark:bg-slate-800 rounded-[3rem] border border-dashed border-slate-300">
            <p class="text-slate-400 italic">No partners found in directory.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function supplierManager() {
    return {
        init() {
            //
        },
        openModal() {
            alert('Adding new suppliers is restricted. Please contact central administration.');
        }
    }
}
</script>
@endsection
