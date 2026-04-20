@extends('layouts.app')

@section('content')
<div x-data="patientApprovalManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-12">
        <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-2">Final Verification</p>
        <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">Patient Prescription Approval</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Validate and authorize medication release for health center patients.</p>
    </div>

    <!-- Approval Queue -->
    <div class="grid grid-cols-1 gap-8">
        @forelse($requisitions as $req)
        <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden flex flex-col md:flex-row animate-in fade-in slide-in-from-bottom-5">
            <!-- Patient Info Side -->
            <div class="w-full md:w-80 bg-slate-50 dark:bg-slate-900/50 p-10 border-r border-slate-100 dark:border-slate-700/30 flex flex-col justify-between">
                <div>
                    <div class="w-16 h-16 rounded-3xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-black text-2xl mb-6 shadow-inner">
                        {{ substr($req->patient->FName, 0, 1) }}{{ substr($req->patient->LName, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-1">{{ $req->patient->FName }} {{ $req->patient->LName }}</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $req->patient->Age }}y/o | {{ $req->patient->Gender }}</p>

                    <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-700/50 space-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Health Center</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $req->healthCenter->Name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Diagnosis</p>
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400 italic">"{{ $req->Diagnosis }}"</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 space-y-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ID Proof Status</p>
                    @if($req->IDProof)
                        <div class="group/img relative overflow-hidden rounded-2xl border-2 border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                            <img src="/{{ $req->IDProof }}" class="w-full h-auto max-h-32 object-cover bg-slate-100 dark:bg-slate-800">
                            <a href="/{{ $req->IDProof }}" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/img:opacity-100 transition-opacity flex flex-col items-center justify-center text-white gap-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span class="text-[8px] font-black uppercase tracking-widest">Full View</span>
                            </a>
                        </div>
                    @else
                        <div class="p-4 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 text-center">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">No ID Proof Detected</span>
                        </div>
                    @endif

                    <div class="pt-4">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1">Reference</p>
                        <p class="text-xs font-mono font-bold text-slate-400">{{ $req->RequisitionNumber }}</p>
                    </div>
                </div>
            </div>

            <!-- Items & Actions -->
            <div class="flex-1 p-10 flex flex-col">
                <div class="flex-1">
                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Requested Medications</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($req->items as $item)
                        <div class="p-5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm flex items-center justify-between group hover:border-rose-500/30 transition-all">
                            <div>
                                <p class="font-black text-slate-800 dark:text-white group-hover:text-rose-500 transition-colors">{{ $item->item->ItemName }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase">{{ $item->item->ItemType }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-slate-600 dark:text-slate-400">Qty {{ $item->QuantityRequested }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Decision Footer -->
                @if($req->StatusType === 'Approved')
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-700 flex gap-4">
                    <button @click="dispenseStock({{ $req->PatientReqID }})" class="flex-[2] py-4 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-500/20 transition-all active:scale-95">
                        Dispense & Finalize Release
                    </button>
                </div>
                @else
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-700 flex gap-4">
                    <button @click="updateStatus({{ $req->PatientReqID }}, 'Rejected')" class="flex-1 py-4 bg-slate-50 dark:bg-slate-900 text-slate-400 hover:text-red-500 font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all border border-transparent hover:border-red-500/20">
                        Deny Request
                    </button>
                    <button @click="updateStatus({{ $req->PatientReqID }}, 'Approved')" class="flex-[2] py-4 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-rose-500/20 transition-all active:scale-95">
                        Verify & Authorize Release
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="py-32 text-center bg-white dark:bg-slate-800 rounded-[3rem] border border-dashed border-slate-300 dark:border-slate-700">
            <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-xl font-black text-slate-400 uppercase tracking-widest">Queue Empty</h3>
            <p class="text-sm text-slate-400 max-w-xs mx-auto mt-2 font-medium">No patient requisitions are currently awaiting pharmacist verification.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function patientApprovalManager() {
    return {
        init() {
            //
        },
        async updateStatus(id, status) {
            if (!confirm('Are you sure you want to ' + status.toLowerCase() + ' this requisition?')) return;

            try {
                const response = await fetch('/patient-requisitions/' + id + '/status', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: status })
                });

                const result = await response.json();
                if (result.success) {
                    alert('Requisition ' + status.toLowerCase() + ' successfully.');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Connection error');
            }
        },
        async dispenseStock(reqId) {
            if (!confirm('Finalize medicine dispensing for this patient?')) return;
            try {
                const response = await fetch(`/patient-requisitions/${reqId}/dispense`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const result = await response.json();
                if (result.success) {
                    alert('Dispensing completed!');
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
