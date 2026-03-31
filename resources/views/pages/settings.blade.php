@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-fade-in" 
     x-data="{ 
        activeTab: 'profile',
        showNotification: false,
        notificationMsg: '',
        notificationType: 'success',
        currentTheme: localStorage.getItem('color-theme') || 'system',
        
        notify(msg, type = 'success') {
            this.notificationMsg = msg;
            this.notificationType = type;
            this.showNotification = true;
            setTimeout(() => this.showNotification = false, 3000);
        },

        setTheme(theme) {
            this.currentTheme = theme;
            if (theme === 'system') {
                localStorage.removeItem('color-theme');
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } else {
                localStorage.setItem('color-theme', theme);
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            this.notify('Appearance preference saved!', 'success');
        },

        async submitProfile(e) {
            const formData = new FormData(e.target);
            try {
                const response = await fetch('/settings/profile', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    this.notify(result.message || 'Profile updated!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    const msg = result.message || (result.errors ? Object.values(result.errors).flat().join(' ') : 'Error updating profile');
                    this.notify(msg, 'error');
                }
            } catch (err) {
                this.notify('Connection failure', 'error');
            }
        },

        async submitPassword(e) {
            const formData = new FormData(e.target);
            try {
                const response = await fetch('/settings/password', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const result = await response.json();
                if (response.ok && result.success) {
                    this.notify(result.message || 'Password updated!');
                    e.target.reset();
                } else {
                    const msg = result.message || (result.errors ? Object.values(result.errors).flat().join(' ') : 'Error updating password');
                    this.notify(msg, 'error');
                }
            } catch (err) {
                this.notify('Verification failed', 'error');
            }
        }
     }">
     
    <!-- Notification Toast -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-90"
         class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 px-8 py-5 rounded-[2rem] shadow-2xl border backdrop-blur-md"
         :class="notificationType === 'success' ? 'bg-white/90 dark:bg-slate-800/90 text-teal-600 border-teal-100 dark:border-teal-900/30' : 'bg-red-50/90 dark:bg-red-900/20 text-red-600 border-red-100 dark:border-red-900/30'"
    >
        <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="notificationType === 'success' ? 'bg-teal-50 dark:bg-teal-900/30' : 'bg-red-100 dark:bg-red-900/30'">
            <template x-if="notificationType === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </template>
            <template x-if="notificationType === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            </template>
        </div>
        <p class="font-black text-xs uppercase tracking-widest" x-text="notificationMsg"></p>
    </div>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1 uppercase tracking-tight">System Configuration</h3>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Operational Identity & Security Guardrails</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-10 min-h-[600px]">
        <!-- Settings Navigation -->
        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="bg-white dark:bg-slate-800 p-3 rounded-[2.5rem] shadow-xl border border-slate-100 dark:border-slate-700/50 space-y-1">
                <template x-for="tab in [
                    {id: 'profile', label: 'Profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'},
                    {id: 'security', label: 'Security', icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'},
                    {id: 'notifications', label: 'Notifications', icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'},
                    {id: 'appearance', label: 'Appearance', icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'},
                    {id: 'activity', label: 'Activity Log', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'}
                ]">
                    <button @click="activeTab = tab.id" 
                        :class="activeTab === tab.id ? 'bg-slate-900 text-white dark:bg-teal-600 shadow-xl shadow-teal-500/10' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900/50 hover:text-slate-600 dark:hover:text-slate-200'"
                        class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="tab.icon"></path></svg>
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Content Panel -->
        <div class="flex-1">
            <!-- Profile Tab -->
            <div x-show="activeTab === 'profile'" x-cloak class="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                    <div class="p-10 border-b border-slate-50 dark:border-slate-700/30">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Public Profile</h3>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Operational Identity Metadata</p>
                    </div>
                    
                    <form @submit.prevent="submitProfile($event)" class="p-10 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">First Name</label>
                                <input type="text" name="FName" value="{{ Auth::user()->FName }}" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Middle Initial / Name</label>
                                <input type="text" name="MName" value="{{ Auth::user()->MName }}" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Last Name</label>
                                <input type="text" name="LName" value="{{ Auth::user()->LName }}" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Account Role</label>
                                <input type="text" disabled value="{{ Auth::user()->Role }}" class="w-full px-6 py-4 bg-slate-100 dark:bg-slate-900/50 border-none rounded-2xl font-black text-slate-400 uppercase tracking-[0.2em] cursor-not-allowed text-[10px]">
                            </div>
                        </div>
                        
                        <div class="pt-6">
                            <button type="submit" class="px-10 py-5 bg-slate-900 dark:bg-teal-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-2xl transition-all active:scale-95">Update Primary Identity</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tab -->
            <div x-show="activeTab === 'security'" x-cloak class="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                    <div class="p-10 border-b border-slate-50 dark:border-slate-700/30">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Security Protocol</h3>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Credential Management & Access Guard</p>
                    </div>
                    
                    <form @submit.prevent="submitPassword($event)" class="p-10 space-y-8 max-w-lg">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Current Secret</label>
                            <input type="password" name="currentPassword" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">New Access Secret</label>
                            <input type="password" name="newPassword" required minlength="8" class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Confirm Secret</label>
                            <input type="password" name="confirmPassword" required class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl font-bold dark:text-white focus:ring-4 focus:ring-teal-500/10 transition-all text-sm">
                        </div>
                        <div class="pt-6">
                            <button type="submit" class="px-10 py-5 bg-red-600 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl shadow-xl transition-all active:scale-95">Rotate Authentication Secret</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Appearance Tab -->
            <div x-show="activeTab === 'appearance'" x-cloak class="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                    <div class="p-10 border-b border-slate-50 dark:border-slate-700/30">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Visual Interface</h3>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Dynamic Rendering & UI Personalization</p>
                    </div>
                    
                    <div class="p-10 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <template x-for="theme in [
                            {id: 'light', label: 'Light Protocol', desc: 'Standard clarity mode', icon: 'M12 3v1m0 16v1m9-9h-1M4 9H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z'},
                            {id: 'dark', label: 'Dark Protocol', desc: 'Low-light tactical mode', icon: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'},
                            {id: 'system', label: 'Sync Output', desc: 'System-wide parity', icon: 'M9.75 17L9 21h6l-.75-4M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'}
                        ]">
                            <button @click="setTheme(theme.id)" 
                                :class="currentTheme === theme.id ? 'border-teal-500 bg-teal-50/10 ring-4 ring-teal-500/5' : 'border-slate-100 dark:border-slate-800 hover:border-slate-200'"
                                class="relative p-8 rounded-[2.5rem] border-2 transition-all text-left group">
                                <div x-show="currentTheme === theme.id" class="absolute top-6 right-6 text-teal-500">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </div>
                                <div class="mb-6 w-12 h-12 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center text-slate-400 group-hover:text-teal-500 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="theme.icon"></path></svg>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight mb-1" x-text="theme.label"></h4>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest" x-text="theme.desc"></p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Activity Tab -->
            <div x-show="activeTab === 'activity'" x-cloak class="space-y-8 animate-in slide-in-from-bottom-4 duration-500" x-data="{ subTab: 'transaction' }">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-50 dark:border-slate-700/30 flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Audit Archive</h3>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">End-to-end Activity Trace</p>
                        </div>
                        <div class="flex p-1.5 bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-200/50 dark:border-slate-800">
                            <button @click="subTab = 'transaction'" :class="subTab === 'transaction' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-lg' : 'text-slate-400'" class="px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Transactions</button>
                            <button @click="subTab = 'security'" :class="subTab === 'security' ? 'bg-white dark:bg-slate-800 text-teal-600 shadow-lg' : 'text-slate-400'" class="px-6 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Security</button>
                        </div>
                    </div>
                    
                    <div class="p-10">
                        <div x-show="subTab === 'transaction'" class="space-y-4">
                            @forelse($logs as $log)
                                <div class="flex items-center p-6 bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] border border-slate-100 dark:border-slate-800/50 transition-all hover:border-teal-500/30">
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center text-teal-500 mr-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ $log->ActionType }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $log->ActionDetails }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ optional($log->ActionDate)->diffForHumans() ?? 'N/A' }}</p>
                                        <span class="px-3 py-1 bg-teal-50 dark:bg-teal-900/20 text-teal-600 rounded-lg text-[8px] font-black uppercase tracking-widest mt-1 inline-block">{{ $log->ReferenceType }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center text-slate-300 italic uppercase tracking-[0.4em] text-[10px]">No transaction history found</div>
                            @endforelse
                        </div>

                        <div x-show="subTab === 'security'" class="space-y-4" x-cloak>
                            @forelse($securityLogs as $sLog)
                                <div class="flex items-center p-6 bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] border border-slate-100 dark:border-slate-800/50 transition-all hover:border-red-500/30">
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center text-red-500 mr-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.444l-8.618-12.4m8.618 12.4l8.618-12.4m-8.618 12.4V9.828"></path></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ $sLog->ActionType }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $sLog->Description ?? $sLog->ActionDescription ?? 'No details' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ optional($sLog->ActionDate)->diffForHumans() ?? 'N/A' }}</p>
                                        <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-lg text-[8px] font-black uppercase tracking-widest mt-1 inline-block">{{ $sLog->IPAddress }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center text-slate-300 italic uppercase tracking-[0.4em] text-[10px]">No security events logged</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab (UI Placeholder for now as per legacy match) -->
            <div x-show="activeTab === 'notifications'" x-cloak class="space-y-8 animate-in slide-in-from-bottom-4 duration-500">
                <div class="bg-white dark:bg-slate-800 rounded-[3rem] shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
                    <div class="p-10 border-b border-slate-50 dark:border-slate-700/30">
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Notification Channels</h3>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Alerting Framework & Push Directives</p>
                    </div>
                    
                    <div class="p-10 space-y-10">
                        <div class="flex items-center justify-between p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem]">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">System Global Alerts</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Override all filters for critical stock alerts</p>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" checked class="sr-only peer">
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-500 rounded-full"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-8 bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem]">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight">Email Requisition Reports</h4>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Daily summary of patient approvals</p>
                            </div>
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-teal-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
