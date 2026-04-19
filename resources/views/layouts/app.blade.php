<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iloilo Warehouse Management System</title>
    {{-- Dark mode must be applied BEFORE any paint to avoid FOUC --}}
    <script>
        (function() {
            const theme = localStorage.getItem('color-theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .modal-lock { 
            overflow: hidden !important; 
            height: 100vh !important;
            width: 100vw !important;
            position: fixed !important;
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-900 text-slate-900 dark:text-white font-body grayscale-0 antialiased h-screen overflow-hidden">
    <!-- Top Loading Bar (Alpine.js Managed) -->
    <div x-data="{ loading: false }" 
         @navigating.window="loading = true; setTimeout(() => loading = false, 800)"
         class="fixed top-0 left-0 right-0 z-[100] h-1 bg-blue-600 transition-all duration-500 origin-left"
         :style="loading ? 'transform: scaleX(1); opacity: 1;' : 'transform: scaleX(0); opacity: 0; transition: none;'"></div>

    @php
        $userRole = auth()->user()->Role ?? 'Guest';
        $themeColors = match($userRole) {
            'Administrator' => ['primary' => 'indigo-600', 'bg' => 'bg-slate-900', 'accent' => 'amber-500', 'fade' => 'indigo-500/10'],
            'Head Pharmacist' => ['primary' => 'indigo-600', 'bg' => 'bg-indigo-950', 'accent' => 'blue-400', 'fade' => 'indigo-500/20'],
            'Health Center Staff' => ['primary' => 'blue-600', 'bg' => 'bg-blue-900', 'accent' => 'sky-400', 'fade' => 'blue-500/10'],
            'Warehouse Staff' => ['primary' => 'blue-700', 'bg' => 'bg-slate-900', 'accent' => 'blue-400', 'fade' => 'slate-500/10'],
            'Accounting Office User' => ['primary' => 'indigo-700', 'bg' => 'bg-indigo-950', 'accent' => 'rose-400', 'fade' => 'indigo-500/10'],
            'CMO/GSO/COA User' => ['primary' => 'cyan-700', 'bg' => 'bg-cyan-950', 'accent' => 'teal-400', 'fade' => 'cyan-500/10'],
            default => ['primary' => 'blue-600', 'bg' => 'bg-slate-900', 'accent' => 'blue-400', 'fade' => 'blue-500/10']
        };
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-950" x-data="{ show: false }" x-init="setTimeout(() => show = true, 50)">
        @include('layouts.sidebar', [
            'currentPage' => $currentPage ?? 'dashboard', 
            'user' => Auth::user(),
            'theme' => $themeColors
        ])

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('layouts.header', ['user' => auth()->user(), 'theme' => $themeColors])

            <main class="flex-1 overflow-y-auto p-6 md:p-10 scrollbar-hide relative bg-slate-50 dark:bg-slate-900"
                  x-show="show" x-cloak
                  x-transition:enter="transition-all ease-out duration-1000"
                  x-transition:enter-start="opacity-0 translate-y-8 filter blur-sm"
                  x-transition:enter-end="opacity-100 translate-y-0 filter blur-0">
                <div class="max-w-[1600px] mx-auto w-full pb-32">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>