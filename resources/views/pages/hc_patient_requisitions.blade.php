@extends('layouts.app')

@section('content')
<div x-data="patientReqManager()" x-init="init()">
    <!-- Header -->
    <div class="mb-12 flex justify-between items-end">
        <div>
            <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-2">Patient Care</p>
            <h1 class="text-4xl font-black text-slate-800 dark:text-white tracking-tight">Patient Requisitions</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Dispense medication and supplies directly to registered patients.</p>
        </div>
        <button @click="openModal()" class="px-8 py-4 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-widest rounded-3xl shadow-xl shadow-rose-500/20 transition-all active:scale-95 flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Patient Request
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700/50 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Patients</p>
            <p class="text-3xl font-black text-slate-800 dark:text-white">{{ count($patients) }}</p>
        </div>
    </div>

    <!-- Active Patient Requisitions Table -->
    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Patient</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Requisition #</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Items</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($requisitions as $req)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center font-black text-sm mr-4">
                                    {{ substr($req->patient->FName, 0, 1) }}{{ substr($req->patient->LName, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 dark:text-white">{{ $req->patient->FName }} {{ $req->patient->LName }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold">{{ $req->patient->Age }}y/o | {{ $req->patient->Gender }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $req->RequisitionNumber }}</div>
                            <div class="text-[10px] text-slate-400 uppercase">{{ $req->RequestDate->format('M d, H:i') }}</div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $req->items->count() }} line items
                            </div>
                        </td>
                        <td class="px-8 py-6">
                             @php
                                $statusClass = match($req->StatusType) {
                                    'Approved' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                    'Pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                    'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                    default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                {{ $req->StatusType }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                             <button 
                                data-req="{{ json_encode($req, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG) }}"
                                @click="openDetailsModal(JSON.parse($el.dataset.req))"
                                class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-medium">No patient requisitions found today.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Requisition Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-8 backdrop-blur-sm"
         x-cloak>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60" @click="closeModal()"></div>

        <!-- Scrollable Container for Modal -->
        <div class="fixed inset-0 overflow-y-auto flex items-center justify-center p-4 pointer-events-none">
            <div @click.away="closeModal()" 
                 class="bg-white dark:bg-slate-800 w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 pointer-events-auto animate-in zoom-in-95 duration-200 flex flex-col max-h-[95vh]">
                
                <!-- Modal Header (Static) -->
                <div class="px-10 pt-10 pb-6 border-b border-slate-100 dark:border-slate-700/30">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mb-1">New Patient Record</p>
                            <h2 class="text-3xl font-black text-slate-800 dark:text-white">Dispense Medication</h2>
                        </div>
                        <button @click="closeModal()" class="p-3 text-slate-400 hover:text-slate-600 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <form id="requisitionForm" @submit.prevent="submitRequisition" class="space-y-10">
                        <!-- Patient Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Registered Patient</label>
                                    <span class="text-[10px] font-black text-rose-500 uppercase">System ID</span>
                                </div>
                                <select x-model="formData.patientId" @change="if(formData.patientId) { formData.patientName = ''; isNewPatient = false; }" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                                    <option value="">Select Registered Patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->PatientID }}">{{ $patient->FName }} {{ $patient->LName }} ({{ $patient->Age }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center px-1">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Manual Entry / New Registration</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="checkbox" id="regNew" x-model="isNewPatient" @change="if(isNewPatient) formData.patientId = ''" class="w-4 h-4 text-rose-600 rounded">
                                        <label for="regNew" class="text-[10px] font-black text-slate-400 uppercase pointer-cursor">Register New</label>
                                    </div>
                                </div>
                                <input type="text" x-model="formData.patientName" @input="if(formData.patientName && !isNewPatient) formData.patientId = ''" placeholder="Enter full name for ad-hoc request" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                        </div>

                        <!-- New Patient Details (Conditional) -->
                        <template x-if="isNewPatient">
                            <div class="p-8 bg-rose-50/30 dark:bg-rose-900/10 rounded-[2.5rem] border border-rose-100 dark:border-rose-800 space-y-6 animate-in slide-in-from-top duration-300">
                                <h3 class="text-xs font-black text-rose-500 uppercase tracking-widest px-2">Patient Registry Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">First Name</label>
                                        <input type="text" x-model="newPatient.FName" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Middle Name</label>
                                        <input type="text" x-model="newPatient.MName" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Last Name</label>
                                        <input type="text" x-model="newPatient.LName" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Age</label>
                                        <input type="number" x-model="newPatient.Age" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Gender</label>
                                        <select x-model="newPatient.Gender" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <label class="text-[9px] font-black text-slate-400 uppercase ml-1">Contact / Address</label>
                                        <input type="text" x-model="newPatient.Address" placeholder="Home address or contact #" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl text-xs font-bold dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Secondary Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Condition / Diagnosis</label>
                                <input type="text" x-model="formData.diagnosis" required placeholder="Reason for clinical dispense" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Clinical Notes</label>
                                <input type="text" x-model="formData.notes" placeholder="Additional treatment instructions" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                        </div>

                        <!-- Verification Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Contact Info (This Request)</label>
                                <input type="text" x-model="formData.contactInfo" placeholder="Mobile / Address for follow-up" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                            <div class="space-y-4">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ID Proof (Photo/Document)</label>
                                <input type="file" @change="formData.idProof = $event.target.files[0]" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-3xl font-bold dark:text-white focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                        </div>

                        <!-- Items Section -->
                        <div class="space-y-6">
                            <div class="flex justify-between items-center px-2">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Items to Dispense</h3>
                                <button type="button" @click="addItem()" class="px-5 py-2.5 bg-slate-900 dark:bg-slate-700 text-white dark:text-slate-300 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-rose-600 transition-all shadow-lg active:scale-95">
                                    + Add Medication
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <div class="flex flex-col md:flex-row gap-4 items-start md:items-end bg-slate-50 dark:bg-slate-900/40 p-6 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                                        <div class="flex-1 w-full space-y-2">
                                            <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Select Supply / Medicine</label>
                                            <select x-model="item.itemId" required class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl font-bold dark:text-white text-sm focus:ring-4 focus:ring-rose-500/10">
                                                <option value="">Search Inventory...</option>
                                                @foreach($items as $i)
                                                    <option value="{{ $i->ItemID }}">{{ $i->ItemName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-full md:w-32 space-y-2 text-left">
                                            <label class="text-[10px] font-black text-slate-400 uppercase ml-1">Quantity</label>
                                            <input type="number" x-model="item.quantity" required min="1" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border-none rounded-2xl font-black dark:text-white focus:ring-4 focus:ring-rose-500/10">
                                        </div>
                                        <button type="button" @click="removeItem(index)" class="p-3.5 text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-800 rounded-2xl transition-all shadow-sm border border-transparent hover:border-red-100 dark:hover:border-red-900/30">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                        <button type="button" @click="closeModal()" class="flex-1 py-5 font-black text-slate-400 uppercase tracking-widest text-xs hover:bg-slate-100 dark:hover:bg-slate-700/50 rounded-[2.5rem] transition-all">Cancel Request</button>
                        <button type="submit" form="requisitionForm" class="flex-[1.5] py-5 bg-slate-900 dark:bg-rose-600 hover:bg-slate-800 dark:hover:bg-rose-700 text-white font-black text-xs uppercase tracking-widest rounded-[2.5rem] shadow-2xl shadow-rose-500/20 transition-all active:scale-95">
                            Submit for Verification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Requisition Details Modal -->
    <template x-if="showDetailsModal">
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 lg:p-8 backdrop-blur-md" x-transition.opacity x-cloak>
            <div class="fixed inset-0 bg-slate-900/60" @click="closeDetailsModal()"></div>

            <div class="bg-white dark:bg-slate-800 w-full max-w-5xl rounded-[3rem] shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700/50 flex flex-col max-h-[90vh] z-[120] animate-in zoom-in-95 duration-200">
                <!-- Modal Header -->
                <div class="px-10 py-8 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                    <div>
                        <p class="text-[10px] font-black text-teal-500 uppercase tracking-[0.3em] mb-1" x-text="'ID: ' + activeReq.RequisitionNumber"></p>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white">Patient Record Summary</h2>
                    </div>
                    <button @click="closeDetailsModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white dark:bg-slate-700 text-slate-400 hover:text-red-500 transition-all shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 text-left">
                        <!-- Left: Core Info & Documents -->
                        <div class="space-y-10">
                            <div class="space-y-6">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-800 pb-3">Patient Registry Info</h3>
                                <div class="grid grid-cols-2 gap-8">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Full Name</label>
                                        <p class="font-bold text-slate-800 dark:text-slate-200" x-text="(activeReq.patient ? (activeReq.patient.FName + ' ' + activeReq.patient.LName) : activeReq.ManualName)"></p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Age / Gender</label>
                                        <p class="font-bold text-slate-800 dark:text-slate-200" x-text="(activeReq.patient ? (activeReq.patient.Age + ' yrs • ' + activeReq.patient.Gender) : 'N/A')"></p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Case Tracking ID</label>
                                        <p class="font-bold text-slate-800 dark:text-slate-200" x-text="activeReq.RequisitionNumber"></p>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">ID Proof Reference</label>
                                        <template x-if="activeReq.IDProof && activeReq.IDProof.startsWith('assets/img')">
                                            <div class="mt-2 group/img relative overflow-hidden rounded-2xl border-2 border-slate-100 dark:border-slate-800 shadow-sm transition-all hover:shadow-md">
                                                <img :src="'/' + activeReq.IDProof" class="w-full h-auto max-h-48 object-contain bg-slate-50 dark:bg-slate-900">
                                                <a :href="'/' + activeReq.IDProof" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/img:opacity-100 transition-opacity flex flex-col items-center justify-center text-white gap-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span class="text-[9px] font-black uppercase tracking-widest">Open Full View</span>
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="!activeReq.IDProof || !activeReq.IDProof.startsWith('assets/img')">
                                            <p class="font-bold text-slate-800 dark:text-slate-200" x-text="activeReq.IDProof || 'Not Provided'"></p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 dark:border-slate-800 pb-3">Clinical Diagnosis</h3>
                                <div class="p-6 bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] border border-slate-100 dark:border-slate-800">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2" x-text="activeReq.Diagnosis || 'General Prescription'"></p>
                                    <p class="text-xs text-slate-500 italic leading-relaxed" x-text="activeReq.Notes || 'No additional clinical notes recorded for this patient case.'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Items & Actions -->
                        <div class="space-y-10">
                            <!-- Prescribed Items -->
                            <div class="space-y-4">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Prescribed Medication</h3>
                                <div class="space-y-3">
                                    <template x-for="item in activeReq.items || []">
                                        <div class="flex justify-between items-center p-4 bg-white dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:border-teal-500/30">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/20 text-teal-600 flex items-center justify-center font-black text-xs">
                                                    💊
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300" x-text="item.item.ItemName"></p>
                                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest" x-text="'Dosage Tracking'"></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-black text-slate-800 dark:text-white" x-text="item.Quantity"></p>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest uppercase">Units</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Dispensing History -->
                            <div class="space-y-4">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Transaction History</h3>
                                <div class="p-8 bg-slate-50 dark:bg-slate-900/10 rounded-2xl border-2 border-dashed border-slate-100 dark:border-slate-800/50 text-center">
                                    <template x-if="activeReq.StatusType === 'Completed'">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 bg-teal-500 text-white rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-[10px] font-black text-teal-600 uppercase tracking-widest mb-1">Stock Dispensed</p>
                                            <p class="text-xs text-slate-400" x-text="'Processed on ' + (activeReq.DispensedDate || activeReq.updated_at)"></p>
                                        </div>
                                    </template>
                                    <template x-if="activeReq.StatusType !== 'Completed'">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest tracking-[0.3em]">Awaiting Delivery</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div class="px-10 py-8 border-t border-slate-50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/20">
                    <div class="flex justify-between items-center">
                        <div class="text-left">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Verification Status</p>
                            <span class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest" :class="{
                                'bg-teal-50 text-teal-600': activeReq.StatusType === 'Approved',
                                'bg-amber-50 text-amber-600': activeReq.StatusType === 'Pending',
                                'bg-red-50 text-red-600': activeReq.StatusType === 'Rejected',
                                'bg-blue-50 text-blue-600': activeReq.StatusType === 'Completed'
                            }" x-text="activeReq.StatusType"></span>
                        </div>
                        <div class="flex gap-4">
                            <button @click="closeDetailsModal()" class="px-10 py-5 bg-white dark:bg-slate-700 text-slate-600 dark:text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 transition-all active:scale-95">
                                Close
                            </button>
                            <template x-if="activeReq.StatusType === 'Approved'">
                                <button @click="dispenseStock(activeReq.PatientReqID)" class="px-10 py-5 bg-slate-900 dark:bg-teal-600 text-white font-black text-xs uppercase tracking-widest rounded-[2rem] shadow-xl transition-all hover:bg-teal-500 active:scale-95">
                                    Finalize Dispensing
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function patientReqManager() {
    return {
        showModal: false,
        showDetailsModal: false,
        activeReq: {},
        isNewPatient: false,
        newPatient: {
            FName: '',
            MName: '',
            LName: '',
            Age: '',
            Gender: 'Male',
            Address: '',
            ContactNumber: '',
            HealthCenterID: '{{ Auth::user()->HealthCenterID ?? 1 }}'
        },
        formData: {
            patientId: '',
            manualName: '',
            diagnosis: '',
            notes: '',
            contactInfo: '',
            idProof: '',
            healthCenterId: '{{ Auth::user()->HealthCenterID ?? 1 }}',
            items: [{ itemId: '', quantity: 1 }]
        },
        init() {
            //
        },
        openDetailsModal(req) {
            this.activeReq = req;
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
        async submitRequisition() {
            if (this.isNewPatient) {
                if (!this.newPatient.FName || !this.newPatient.LName) {
                    alert('Please provide first and last name for patient registration.');
                    return;
                }
                // Step 1: Register Patient
                try {
                    const regResponse = await fetch('/patients', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newPatient)
                    });
                    const regResult = await regResponse.json();
                    if (regResult.success) {
                        this.formData.patientId = regResult.patient.PatientID;
                    } else {
                        alert('System Error: Could not register new patient.');
                        return;
                    }
                } catch (e) {
                    alert('Connection error during registration.');
                    return;
                }
            }

            if ((!this.formData.patientId && !this.formData.manualName) || this.formData.items.length === 0) {
                alert("Please select/enter a patient and add at least one item.");
                return;
            }

            const submissionData = new FormData();
            submissionData.append('patientId', this.formData.patientId);
            submissionData.append('healthCenterId', this.formData.healthCenterId);
            submissionData.append('diagnosis', this.formData.diagnosis);
            submissionData.append('notes', this.formData.notes);
            submissionData.append('contactInfo', this.formData.contactInfo);
            
            if (this.formData.idProof instanceof File) {
                submissionData.append('idProof', this.formData.idProof);
            }

            this.formData.items.forEach((item, index) => {
                submissionData.append(`items[${index}][itemId]`, item.itemId);
                submissionData.append(`items[${index}][quantity]`, item.quantity);
            });

            try {
                const response = await fetch('/patient-requisitions', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: submissionData
                });
                
                const result = await response.json();
                if (response.ok && result.success) {
                    alert('Requisition created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Validation failed. Check file size/type.'));
                    console.error(result.errors);
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
