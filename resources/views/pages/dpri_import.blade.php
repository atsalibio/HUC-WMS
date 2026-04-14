@extends('layouts.app', ['currentPage' => 'dpri-import'])

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="dpriScanner()">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 px-4">
        <div class="space-y-1">
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight">DPRI Intelligent Radar</h3>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-1.5">Neural Scanning of DOH Drug Price Reference Index</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="https://dpri.doh.gov.ph/download" target="_blank" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-[10px] font-black text-slate-500 uppercase tracking-widest shadow-sm hover:text-blue-500 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                DOH Portal
             </a>
        </div>
    </div>

    <!-- Upload Zone -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-1 space-y-8">
            <div 
                class="relative group cursor-pointer border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[3rem] p-12 text-center bg-white dark:bg-slate-800 shadow-2xl shadow-slate-200/50 dark:shadow-none hover:border-blue-500/50 transition-all duration-500"
                @dragover.prevent="dragOver = true"
                @dragleave.prevent="dragOver = false"
                @drop.prevent="handleDrop($event)"
                @click="document.getElementById('pdfUpload').click()"
                :class="{'border-blue-500 ring-8 ring-blue-500/5 bg-blue-50/10': dragOver}"
            >
                <input type="file" class="hidden" id="pdfUpload" accept="application/pdf" @change="handleFileSelect($event)">
                
                <div class="space-y-6">
                    <div class="w-24 h-24 mx-auto bg-slate-50 dark:bg-slate-900 rounded-[2rem] flex items-center justify-center text-slate-300 group-hover:text-blue-500 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-all duration-500 transform group-hover:scale-110 group-hover:rotate-6 shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-800 dark:text-slate-100 uppercase tracking-widest">Deploy PDF Analysis</p>
                        <p class="text-[9px] uppercase tracking-[0.2em] font-black text-slate-400 mt-2 italic">Drag & drop source booklet</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 dark:bg-slate-900/60 rounded-[2.5rem] p-8 space-y-6 shadow-2xl">
                <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] flex items-center gap-3">
                    <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                    Extraction Logic
                </h4>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="text-blue-500 mt-1">✓</div>
                        <p class="text-[11px] font-bold text-slate-300 leading-relaxed">Neural regex identification of generic drug names and dosages.</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="text-blue-500 mt-1">✓</div>
                        <p class="text-[11px] font-bold text-slate-300 leading-relaxed">Automated deduplication against existing registry entries.</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="text-blue-500 mt-1">✓</div>
                        <p class="text-[11px] font-bold text-slate-300 leading-relaxed">Real-time unit-of-measure (UOM) classification.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results / Progress Section -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Progress Overlay -->
            <div x-show="isScanning" x-transition class="bg-white dark:bg-slate-800 rounded-[3rem] p-10 border border-slate-100 dark:border-slate-800 shadow-2xl space-y-8">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <h4 class="text-xl font-black text-slate-800 dark:text-white" x-text="'Processing: ' + currentFilename"></h4>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest" x-text="'Analyzing Page ' + scanProgress"></p>
                    </div>
                    <div class="text-4xl font-black text-blue-500 tabular-nums" x-text="Math.round(totalProgress) + '%'"></div>
                </div>
                <div class="w-full bg-slate-50 dark:bg-slate-900 h-4 rounded-full overflow-hidden p-1 shadow-inner">
                    <div class="h-full bg-slate-900 dark:bg-blue-500 rounded-full transition-all duration-300" :style="'width: ' + totalProgress + '%'"></div>
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="!isScanning && foundItems.length === 0" class="h-[500px] flex flex-col items-center justify-center text-slate-200 space-y-6">
                <div class="w-32 h-32 rounded-full border-4 border-dashed border-slate-100 dark:border-slate-800 flex items-center justify-center">
                    <svg class="w-16 h-16 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.4em] opacity-30">Awaiting document feed...</p>
            </div>

            <!-- Scanned Items Table -->
            <div x-show="foundItems.length > 0" x-transition class="bg-white dark:bg-slate-800 rounded-[3rem] border border-slate-200/60 dark:border-slate-700/60 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden flex flex-col h-[650px]">
                <div class="px-10 py-8 border-b border-slate-50 dark:border-slate-900/40 bg-slate-50/30 dark:bg-slate-900/10 space-y-6">
                    <div class="flex items-center justify-between gap-6 flex-wrap">
                        <div class="flex items-center gap-4 flex-wrap">
                            <span class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest" x-text="foundItems.length + ' Entities Identified'"></span>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" x-model="search" placeholder="Quick search..." class="text-xs font-bold bg-white dark:bg-slate-900 border-none rounded-xl pl-12 pr-4 py-3 w-64 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all text-slate-700">
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                             <button @click="clearResults()" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 transition-colors">Clear All</button>
                             <button @click="confirmImport()" :disabled="isImporting" class="px-8 py-4 bg-slate-900 dark:bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl transition-all flex items-center gap-3 active:scale-95">
                                <span x-show="!isImporting">Finalize Registry</span>
                                <span x-show="isImporting" class="animate-spin w-3 h-3 border-2 border-white/30 border-t-white rounded-full"></span>
                             </button>
                        </div>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 dark:bg-slate-900/30 sticky top-0 z-10 border-b border-slate-50 dark:border-slate-800">
                            <tr>
                                <th class="px-10 py-5 text-left">
                                    <input type="checkbox" @change="toggleAll()" :checked="allSelected" class="w-5 h-5 rounded-lg border-none bg-white dark:bg-slate-800 text-blue-500 focus:ring-2 focus:ring-blue-500/10">
                                </th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest cursor-pointer group" @click="sortBy('ItemName')">
                                    Generic Entity Name <span x-show="sortField==='ItemName'" x-text="sortDir==='asc' ? '↑' : '↓'"></span>
                                </th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest cursor-pointer group" @click="sortBy('ItemType')">
                                    Classification <span x-show="sortField==='ItemType'" x-text="sortDir==='asc' ? '↑' : '↓'"></span>
                                </th>
                                <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Base UOM</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50/50 dark:divide-slate-800">
                            <template x-for="(item, index) in filteredItems" :key="index">
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-all group">
                                    <td class="px-10 py-5">
                                        <input type="checkbox" x-model="item.selected" class="w-5 h-5 rounded-lg border-none bg-slate-100 dark:bg-slate-700 text-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all">
                                    </td>
                                    <td class="px-6 py-5">
                                        <input type="text" x-model="item.ItemName" class="text-sm font-black bg-transparent border-none p-0 focus:ring-0 text-slate-800 dark:text-slate-200 w-full hover:bg-slate-100/50 rounded px-2 -ml-2 transition-all">
                                    </td>
                                    <td class="px-6 py-5">
                                        <select x-model="item.ItemType" class="text-[10px] font-black bg-white dark:bg-slate-900 border-none rounded-lg px-3 py-1.5 text-indigo-500 uppercase tracking-widest shadow-sm">
                                            <option>Medicine</option>
                                            <option>Supply</option>
                                            <option>Equipment</option>
                                        </select>
                                    </td>
                                    <td class="px-10 py-5">
                                        <input type="text" x-model="item.UnitOfMeasure" class="text-xs font-black text-slate-400 bg-transparent border-none p-0 focus:ring-0 w-24">
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

<!-- pdf.js from CDN -->
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('dpriScanner', () => ({
        dragOver: false,
        isScanning: false,
        isImporting: false,
        currentFilename: '',
        scanProgress: 0,
        totalProgress: 0,
        foundItems: [],
        search: '',
        filterType: '',
        sortField: 'ItemName',
        sortDir: 'asc',
        allSelected: true,

        get filteredItems() {
            let items = this.foundItems;
            if (this.search) items = items.filter(i => i.ItemName.toLowerCase().includes(this.search.toLowerCase()));
            if (this.filterType) items = items.filter(i => i.ItemType === this.filterType);
            const field = this.sortField;
            const dir = this.sortDir === 'asc' ? 1 : -1;
            return [...items].sort((a, b) => {
                if (a[field] < b[field]) return -1 * dir;
                if (a[field] > b[field]) return 1 * dir;
                return 0;
            });
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDir = 'asc';
            }
        },

        async handleFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.processFile(file);
        },

        async handleDrop(e) {
            this.dragOver = false;
            const file = e.dataTransfer.files[0];
            if (!file || file.type !== 'application/pdf') {
                alert('Please drop a valid PDF document.');
                return;
            }
            this.processFile(file);
        },

        async processFile(file) {
            this.isScanning = true;
            this.currentFilename = file.name;
            this.foundItems = [];
            this.totalProgress = 0;

            const reader = new FileReader();
            reader.onload = async (event) => {
                const typedarray = new Uint8Array(event.target.result);
                try {
                    const pdf = await pdfjsLib.getDocument(typedarray).promise;
                    const numPages = pdf.numPages;

                    for (let i = 1; i <= numPages; i++) {
                        this.scanProgress = i;
                        this.totalProgress = (i / numPages) * 100;
                        const page = await pdf.getPage(i);
                        const textContent = await page.getTextContent();
                        const pageText = textContent.items.map(item => item.str).join(' ');
                        this.parsePageText(pageText);
                    }
                } catch (err) {
                    console.error('PDF Error:', err);
                    alert('Extraction failed. Document may be encrypted or non-standard.');
                } finally {
                    this.isScanning = false;
                    this.deduplicate();
                }
            };
            reader.readAsArrayBuffer(file);
        },

        parsePageText(text) {
            const cleanText = text.replace(/\s+/g, ' ');
            // Refined Regex: Looks for uppercase drug names (min 5 chars) followed by dosage patterns
            const medRegex = /([A-Z][A-Z\s\/]{4,})\s+([\d\.]{1,}\s*(?:mg|mcg|ml|g|%|iu|units|tablet|capsule|bottle|vial|ampule|iv|im|respuole|pouch|tube|canister|nebule|sachet|suppository))/gi;
            
            const noiseFilter = ['ANNEX', 'PAGE', 'OFFICIAL', 'PRICE', 'INDEX', 'RETAIL', 'UNIT COST', 'QUANTITY', 'TOTAL', 'SUMMARY', 'TABLE', 'DOH', 'REPUBLIC', 'PHILIPPINES'];
            
            let match;
            while ((match = medRegex.exec(cleanText)) !== null) {
                let name = match[1].trim();
                const dosage = match[2].trim();
                
                // Secondary check: ensure name isn't just noise or too short
                const isNoise = noiseFilter.some(noise => name.includes(noise));
                
                if (!isNoise && name.length > 5) {
                    this.foundItems.push({
                        ItemName: `${name} ${dosage}`.replace(/\s+/g, ' '),
                        ItemType: 'Medicine',
                        UnitOfMeasure: this.inferUOM(dosage),
                        selected: true
                    });
                }
            }
        },

        inferUOM(dosage) {
            const d = dosage.toLowerCase();
            if (d.includes('tablet')) return 'Tablet';
            if (d.includes('capsule')) return 'Capsule';
            if (d.includes('bottle') || d.includes('ml')) return 'Bottle';
            if (d.includes('vial') || d.includes('ampule')) return 'Ampule';
            if (d.includes('sachet')) return 'Sachet';
            if (d.includes('tube')) return 'Tube';
            return 'Unit';
        },

        deduplicate() {
            const seen = new Set();
            this.foundItems = this.foundItems.filter(item => {
                const key = item.ItemName.toLowerCase().replace(/\s/g, '');
                if (seen.has(key)) return false;
                seen.add(key);
                return true;
            });
            this.foundItems.sort((a, b) => a.ItemName.localeCompare(b.ItemName));
        },

        toggleAll() {
            this.allSelected = !this.allSelected;
            this.foundItems.forEach(i => i.selected = this.allSelected);
        },

        clearResults() {
            if (confirm('Clear extraction buffer?')) this.foundItems = [];
        },

        async confirmImport() {
            const selected = this.foundItems.filter(i => i.selected);
            if (selected.length === 0) return alert('No entities selected.');
            if (!confirm(`Import ${selected.length} items to database?`)) return;

            this.isImporting = true;
            try {
                const response = await fetch('/inventory/bulk-import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: selected })
                });
                const result = await response.json();
                if (result.success) {
                    alert(`Successfully imported ${result.count} items!`);
                    this.foundItems = this.foundItems.filter(i => !i.selected);
                }
            } catch (err) {
                alert('Connection failure.');
            } finally {
                this.isImporting = false;
            }
        }
    }));
});
</script>
@endpush
@endsection
