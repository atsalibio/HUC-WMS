<header class="sticky top-0 z-40 w-full h-16 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 transition-all duration-300">
    <div class="h-full px-6 flex items-center justify-between">
        <!-- Dashboard Breadcrumbs / Search Area -->
        <div class="flex items-center gap-8">
            <div class="hidden lg:flex flex-col">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest leading-none mb-1">
                    {{ ucfirst(str_replace('_', ' ', $currentPage ?? 'Dashboard')) }}
                </p>
                <h2 class="text-sm font-bold text-slate-800 dark:text-white leading-none flex items-center gap-2">
                    Iloilo City Healthcare Monitoring
                    @if(optional($user)->healthCenter)
                        <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                        <span class="text-indigo-500 font-black tracking-tight">{{ $user->healthCenter->Name }}</span>
                    @endif
                </h2>
            </div>

            <!-- Global Search Placeholder (Functional UI) -->
            <div class="relative group hidden md:block">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" placeholder="Global search..." 
                       class="w-64 pl-10 pr-4 py-2 bg-slate-100/50 dark:bg-slate-800/50 border-none rounded-xl text-xs font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:bg-white dark:focus:bg-slate-800 transition-all outline-none text-slate-600 dark:text-slate-300">
            </div>
        </div>

        <!-- Action Items (Right) -->
        <div class="flex items-center gap-2">
            <!-- Notifications Dropdown (Alpine.js) -->
            <div x-data="notificationHandler()" x-init="init()" class="relative">
                <button @click="open = !open" 
                         class="p-2.5 rounded-xl text-slate-400 hover:text-indigo-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <template x-if="notifications.length > 0">
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900 group-hover:scale-110 transition-transform pulse-slow"></span>
                    </template>
                </button>
                
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" x-cloak>
                    <div class="px-5 py-4 border-b border-slate-50 dark:border-slate-700/50 flex justify-between items-center">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Recent Alerts</span>
                        <template x-if="notifications.length > 0">
                            <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-500/10 text-[10px] font-black text-indigo-500 rounded-full" x-text="'New (' + notifications.length + ')'"></span>
                        </template>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <template x-for="note in notifications" :key="note.id">
                            <div @click="handleNotificationClick(note)" 
                                 class="p-4 border-b border-slate-50 dark:border-slate-700/30 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors cursor-pointer group">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm"
                                         :class="note.priority === 'High' ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/10' : (note.priority === 'Normal' ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10' : 'bg-slate-100 text-slate-600 dark:bg-slate-900/50')">
                                        <template x-if="note.priority === 'High'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </template>
                                        <template x-if="note.priority !== 'High'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-slate-800 dark:text-white mb-0.5 group-hover:text-indigo-500 transition-colors" x-text="note.title || 'Notification'"></p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2" x-text="note.message"></p>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter mt-1" x-text="new Date(note.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="notifications.length === 0">
                            <div class="p-8 text-center">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No new alerts</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Profile Overview (Quick glance) -->
            <div class="ml-2 flex items-center gap-3 pl-3 border-l border-slate-100 dark:border-slate-800" x-data="{ openProfile: false }">
                <div class="hidden sm:block text-right">
                    <p class="text-xs font-bold text-slate-800 dark:text-white leading-none mb-1 truncate max-w-[120px]">
                        {{ (optional($user)->FName ?? 'Guest') . ' ' . (optional($user)->LName ?? 'User') }}
                    </p>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">
                        {{ optional($user)->Role ?? 'Staff' }}
                    </p>
                </div>
                <div @click="openProfile = !openProfile" class="w-10 h-10 rounded-xl bg-slate-900 dark:bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-slate-900/10 dark:shadow-indigo-500/20 active:scale-95 transition-all cursor-pointer relative">
                    {{ substr(optional($user)->FName ?? 'U', 0, 1) }}
                    
                    <!-- Profile Dropdown -->
                    <div x-show="openProfile" @click.away="openProfile = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 top-12 w-48 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" x-cloak>
                        <div class="p-2">
                           <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 text-left text-xs font-black text-rose-500 uppercase tracking-widest hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Sign Out
                                </button>
                           </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function notificationHandler() {
    return {
        open: false,
        notifications: [],
        async init() {
            this.fetchNotifications();
            // Optional: Periodically fetch
            setInterval(() => this.fetchNotifications(), 30000);
        },
        async fetchNotifications() {
            try {
                const response = await fetch('/notifications');
                if (response.ok) {
                    this.notifications = await response.json();
                }
            } catch (e) {
                console.error('Notification fetch failed', e);
            }
        },
        async handleNotificationClick(note) {
            try {
                // Mark as read in background
                await fetch(`/notifications/${note.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                
                // Route to the link if it exists
                if (note.link) {
                    window.location.href = note.link;
                } else {
                    this.open = false;
                    this.fetchNotifications();
                }
            } catch (e) {
                console.error('Notification click failed', e);
                if (note.link) window.location.href = note.link;
            }
        }
    }
}
</script>