<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Iloilo City Pharmacy</title>

    <!-- Your custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Alpine.js for interactivity -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="min-h-screen animated-gradient flex items-center justify-center p-4">

<div class="auth-card animate-premium-in" x-data="loginFlow()">
    <!-- Left Panel - Branding -->
    <div class="auth-branding hidden md:flex animated-gradient">
        <div class="logo-container">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Pharmacy Logo" class="w-16 h-16">
        </div>
        <p class="text-sm font-medium text-white/80 mb-1">Iloilo City Government</p>
        <h1 class="text-3xl font-bold mb-2 leading-tight font-display text-white">Iloilo City Pharmacy</h1>
        <p class="text-sm text-white/90 mb-6 max-w-xs px-2 leading-relaxed font-normal">
            A comprehensive, end-to-end inventory management system designed for modern healthcare.
        </p>
        <div class="testimonial-box bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 shadow-lg">
            <p class="text-xs italic leading-relaxed font-medium">
                "This system has revolutionized our inventory control, enhancing accountability and ensuring the efficient delivery of medical supplies for the people of Iloilo City."
            </p>
            <p class="mt-4 text-xs font-semibold text-white/90">- Head Pharmacist, Iloilo City Pharmacy</p>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="auth-form-container w-full md:w-1/2 overflow-y-auto max-h-[96vh]">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-slate-800 dark:text-white font-display">Welcome Back</h2>
            <div class="flex items-center justify-center gap-2 mt-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Don't have an account? 
                    <a href="{{ route('register.show') }}" class="text-teal-600 dark:text-teal-400 hover:underline font-medium transition-colors">
                        Sign Up
                    </a>
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{passwordVisible: false}">
            @csrf

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1.5">Username</label>
                <input id="username" name="username" type="text" required
                    class="form-input bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 text-sm py-2.5 dark:text-white placeholder-slate-400/50"
                    placeholder="Enter your username" value="{{ old('username') }}">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input id="password" name="password" :type="passwordVisible ? 'text' : 'password'" required
                        class="form-input bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 text-sm py-2.5 pr-10 dark:text-white placeholder-slate-400/50"
                        placeholder="••••••••">
                    <button type="button" @click="passwordVisible = !passwordVisible"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-teal-500 focus:outline-none transition-colors">
                        <span x-show="!passwordVisible">Show</span>
                        <span x-show="passwordVisible">Hide</span>
                    </button>
                </div>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="p-2 text-xs text-center border font-semibold rounded-lg text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-900/30">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="btn btn-primary w-full py-2.5 shadow-sm active:scale-[0.98] text-sm font-semibold flex justify-center items-center gap-2">
                    Sign In
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center border-t border-slate-50 dark:border-slate-800/50 pt-4">
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">
                &copy; 2026 ILOILO CITY
            </p>
        </div>
    </div>
</div>

<script>
function loginFlow() {
    return {
        username: '',
        password: '',
        loading: false,
        message: '',
        messageType: '',
        async handleLogin() {
            this.loading = true;
            this.message = '';
            try {
                const formData = new FormData();
                formData.append('username', this.username);
                formData.append('password', this.password);

                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = result.redirect || '{{ route("dashboard") }}';
                } else {
                    this.message = result.message || 'Login failed';
                }
            } catch (e) {
                this.message = 'Server error. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

</body>
</html>