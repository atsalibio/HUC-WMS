{{-- resources/views/layouts/partials/header.blade.php --}}
@php
use App\Services\System\NotificationService;
use App\Services\System\HealthCenterService;

$user = auth()->user();

// Fetch notifications via service
$notificationService = app(NotificationService::class);
$notifications = $notificationService->getNotificationsForUser($user);
$unreadCount = $notifications->where('isRead', false)->count();

// Health center info
$healthCenterService = app(HealthCenterService::class);
$hcName = $user->HealthCenterID ? $healthCenterService->getName($user->HealthCenterID) : null;
$allHCs = $user->Role === 'Administrator' ? $healthCenterService->getAll() : [];
@endphp

<style>[x-cloak]{ display: none !important; }</style>

<header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/60 dark:border-slate-800/60">
    <div class="px-6 py-3.5 flex justify-between items-center">

        {{-- Left: Mobile Menu + Page Title + Health Center --}}
        <div class="flex items-center space-x-4">
            {{-- Mobile Menu Button --}}
            <button class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page Icon + Title --}}
            @php
                $pageIcons = [
                    'dashboard' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                    'profile' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    'settings' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                ];
                $currentIcon = $pageIcons[$page ?? 'dashboard'] ?? $pageIcons['dashboard'];
            @endphp
            <h1 class="text-xl font-bold text-slate-800 dark:text-white font-display tracking-tight flex items-center">
                <div class="p-1.5 bg-teal-50 dark:bg-teal-900/30 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $currentIcon }}"></path>
                    </svg>
                </div>
                {{ $pageTitle ?? 'Overview' }}
            </h1>

            {{-- Health Center Badge --}}
            @if($hcName)
                <div class="hidden lg:flex items-center px-3 py-1 bg-teal-50 dark:bg-teal-900/20 border border-teal-100 dark:border-teal-800 rounded-full ml-4">
                    <span class="text-[10px] font-bold text-teal-600 dark:text-teal-400 uppercase tracking-tighter mr-2">Center:</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $hcName }}</span>
                </div>
            @endif

            {{-- Admin Health Center Switch --}}
            @if($user->Role === 'Administrator')
                <div class="hidden xl:block ml-4">
                    <select onchange="switchHealthCenter(this.value)" class="text-[11px] bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg py-1 px-2 focus:ring-primary focus:border-primary outline-none transition-all">
                        <option value="">Switch Health Center...</option>
                        @foreach($allHCs as $hc)
                            <option value="{{ $hc->HealthCenterID }}" @selected($user->HealthCenterID == $hc->HealthCenterID)>{{ $hc->Name }}</option>
                        @endforeach
                        <option value="none" @selected(empty($user->HealthCenterID))>None (Central Only)</option>
                    </select>
                </div>
                <script>
                    function switchHealthCenter(hcId) {
                        fetch("{{ route('api.switchHealthCenter') }}", {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ healthCenterId: hcId })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if(data.success) location.reload();
                            else alert('Failed to switch health center: ' + data.message);
                        });
                    }
                </script>
            @endif
        </div>

        {{-- Right Side: Notifications & Profile --}}
        <div class="flex items-center gap-3">

            {{-- Notifications --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="relative p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
                    </svg>
                    @if($unreadCount)
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden z-50">
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 mb-1 font-bold text-slate-600 dark:text-slate-300">Notifications</div>
                    <ul class="max-h-96 overflow-y-auto">
                        @forelse($notifications as $notif)
                            <li class="px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800 {{ !$notif->isRead ? 'font-bold' : '' }}">
                                {{ $notif->Title }} <br>
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $notif->Timestamp }}</span>
                            </li>
                        @empty
                            <li class="px-4 py-2 text-sm text-slate-500">No notifications</li>
                        @endforelse
                    </ul>
                    @if($unreadCount)
                        <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-800 text-center">
                            <button onclick="markAllAsRead()" class="text-xs text-teal-600 dark:text-teal-400 font-bold hover:underline">Mark all as read</button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                <button @click="open = !open" :class="{ 'bg-slate-100 dark:bg-slate-800': open }" class="flex items-center space-x-3 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-xs ring-2 ring-teal-600/20 shadow-lg shadow-teal-600/10">
                        {{ substr($user->FName, 0, 1) . substr($user->LName, 0, 1) }}
                    </div>
                    <div class="text-left hidden md:block">
                        <p class="text-xs font-bold text-slate-800 dark:text-white leading-tight">{{ $user->FName }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ $user->Role }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl py-2 border border-slate-200 dark:border-slate-800 z-50">
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800 mb-1">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">User Menu</p>
                    </div>
                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-teal-600 transition-colors">My Profile</a>
                    <a href="{{ route('settings') }}" class="flex items-center px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-teal-600 transition-colors">Account Settings</a>
                    <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function markAllAsRead() {
    fetch("{{ route('api.notifications.markAllRead') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    }).then(r => r.json()).then(data => {
        if(data.success) location.reload();
        else console.error('Failed to mark notifications read');
    });
}
</script>