<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUC Pharmacy App</title>
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
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-900 text-slate-900 dark:text-white font-body grayscale-0 antialiased h-screen overflow-hidden">
    <!-- Top Loading Bar (Alpine.js Managed) -->
    <div x-data="{ loading: false }" 
         @navigating.window="loading = true; setTimeout(() => loading = false, 800)"
         class="fixed top-0 left-0 right-0 z-[100] h-1 bg-teal-500 transition-all duration-500 origin-left"
         :style="loading ? 'transform: scaleX(1); opacity: 1;' : 'transform: scaleX(0); opacity: 0; transition: none;'"></div>

    <div class="flex h-full overflow-hidden bg-slate-900" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
        @include('layouts.sidebar', ['currentPage' => $currentPage ?? 'dashboard', 'user' => Auth::user()])

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('layouts.header', ['user' => auth()->user()])

            <main class="flex-1 overflow-y-auto p-6 scrollbar-hide relative bg-slate-50 dark:bg-slate-900"
                  x-show="show" x-cloak
                  x-transition:enter="transition ease-out duration-700 delay-150"
                  x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                  x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                @yield('content')
            </main>
        </div>
    </div>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>