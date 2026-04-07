<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to the Iloilo City Pharmacy Warehouse Management System. Manage medical supplies inventory efficiently.">
    <title>Login | Iloilo City Warehouse Management System</title>

    <link rel="icon" href="{{ asset('assets/img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #0d9488;
            --primary-light: #14b8a6;
            --primary-dark: #0f766e;
            --surface: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --bg-input: #f8fafc;
            --error-bg: #fef2f2;
            --error-text: #dc2626;
            --error-border: #fecaca;
            --success-bg: #ecfdf5;
            --success-text: #059669;
            --success-border: #a7f3d0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 25%, #99f6e4 50%, #5eead4 75%, #2dd4bf 100%);
            background-size: 400% 400%;
            animation: bgShift 12s ease-in-out infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        @keyframes bgShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width: 500px; height: 500px; background: #2dd4bf; top: -120px; left: -100px; animation: orbFloat1 18s ease-in-out infinite; }
        .orb-2 { width: 400px; height: 400px; background: #0d9488; bottom: -80px; right: -60px; animation: orbFloat2 22s ease-in-out infinite; }
        .orb-3 { width: 300px; height: 300px; background: #5eead4; top: 50%; left: 60%; animation: orbFloat3 15s ease-in-out infinite; }

        @keyframes orbFloat1 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(60px, 40px); } }
        @keyframes orbFloat2 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-40px, -60px); } }
        @keyframes orbFloat3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-30px, 50px); } }

        /* Card */
        .auth-card {
            position: relative;
            z-index: 1;
            display: flex;
            max-width: 860px;
            width: 100%;
            min-height: 520px;
            border-radius: 1.5rem;
            overflow: hidden;
            background: var(--surface);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.7),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: cardEntry 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px) scale(0.98);
        }

        @keyframes cardEntry {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Left branding panel */
        .brand-panel {
            position: relative;
            width: 45%;
            background: linear-gradient(160deg, var(--primary-dark) 0%, var(--primary) 40%, var(--primary-light) 100%);
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .logo-wrap {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 1rem;
            border-radius: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-wrap img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 3px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .brand-panel .subtitle {
            position: relative;
            z-index: 2;
            font-size: 0.8125rem;
            font-weight: 500;
            opacity: 0.85;
            margin-bottom: 0.25rem;
            letter-spacing: 0.02em;
        }

        .brand-panel h1 {
            position: relative;
            z-index: 2;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 1.625rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 0.625rem;
            letter-spacing: -0.02em;
        }

        .brand-panel .tagline {
            position: relative;
            z-index: 2;
            font-size: 0.8125rem;
            line-height: 1.6;
            opacity: 0.9;
            max-width: 280px;
            margin-bottom: 1.75rem;
        }

        .testimonial {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            max-width: 320px;
        }

        .testimonial p {
            font-size: 0.75rem;
            font-style: italic;
            line-height: 1.7;
            letter-spacing: 0.01em;
        }

        .testimonial .author {
            margin-top: 0.875rem;
            font-size: 0.6875rem;
            font-weight: 700;
            font-style: normal;
            opacity: 0.9;
            letter-spacing: 0.02em;
        }

        /* Right form panel */
        .form-panel {
            flex: 1;
            padding: 2.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--surface);
            position: relative;
        }

        .form-panel .heading {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.375rem;
            letter-spacing: -0.03em;
        }

        .form-panel .sub-heading {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .form-panel .sub-heading a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .form-panel .sub-heading a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Form fields */
        .field-group { margin-bottom: 1.25rem; }

        .field-group label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .field-group .input-wrap {
            position: relative;
        }

        .field-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-family: inherit;
            color: var(--text-primary);
            background: var(--bg-input);
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .field-group input::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }

        .field-group input:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.08);
        }

        .field-group input:hover:not(:focus) {
            border-color: #cbd5e1;
        }

        .toggle-password {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.2s;
            padding: 0;
        }

        .toggle-password:hover { color: var(--primary); }
        .toggle-password svg { width: 18px; height: 18px; }

        /* Alert box */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            animation: alertIn 0.3s ease-out;
        }

        .alert-error {
            background: var(--error-bg);
            color: var(--error-text);
            border: 1px solid var(--error-border);
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
        }

        @keyframes alertIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 0.8125rem 1.5rem;
            font-family: inherit;
            font-size: 0.9375rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            background-size: 200% 200%;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            letter-spacing: -0.01em;
            box-shadow: 0 4px 14px -3px rgba(13, 148, 136, 0.4);
        }

        .btn-submit:hover:not(:disabled) {
            background-position: 100% 0;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px -4px rgba(13, 148, 136, 0.45);
        }

        .btn-submit:active:not(:disabled) {
            transform: translateY(0) scale(0.985);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.65s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* Footer */
        .footer {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }

        .footer p {
            font-size: 0.625rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand-panel { display: none; }
            .auth-card { max-width: 440px; border-radius: 1.25rem; min-height: auto; }
            .form-panel { padding: 2rem 1.75rem; }
            .form-panel .heading { font-size: 1.5rem; }

            /* Show mobile branding */
            .mobile-brand {
                display: flex !important;
                flex-direction: column;
                align-items: center;
                margin-bottom: 1.5rem;
            }

            .mobile-brand img {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid var(--primary);
                box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
                margin-bottom: 0.75rem;
            }

            .mobile-brand span {
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.75rem;
                font-weight: 600;
                color: var(--primary);
                letter-spacing: 0.02em;
            }
        }

        @media (min-width: 769px) {
            .mobile-brand { display: none; }
        }
    </style>
</head>
<body>
    <!-- Ambient floating orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="auth-card" x-data="loginFlow()">
        <!-- Left Panel - Branding -->
        <div class="brand-panel">
            <div class="logo-wrap">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Iloilo City WMS Logo">
            </div>
            <p class="subtitle">Iloilo City Government</p>
            <h1>Iloilo City WMS</h1>
            <p class="tagline">
                A comprehensive, end-to-end inventory management system designed for modern healthcare.
            </p>
            <div class="testimonial">
                <p>
                    "This system has revolutionized our inventory control, enhancing accountability and ensuring the efficient delivery of medical supplies for the people of Iloilo City."
                </p>
                <p class="author">— Head Pharmacist, Iloilo City Pharmacy</p>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="form-panel">
            <!-- Mobile-only branding -->
            <div class="mobile-brand">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Iloilo City WMS Logo">
                <span>Iloilo City WMS</span>
            </div>

            <h2 class="heading">Welcome Back</h2>
            <p class="sub-heading">
                Don't have an account?
                <a href="{{ route('register.show') }}">Sign Up</a>
            </p>

            <form @submit.prevent="handleLogin" id="login-form">
                @csrf

                <!-- Username -->
                <div class="field-group">
                    <label for="login-username">Username</label>
                    <div class="input-wrap">
                        <input
                            id="login-username"
                            name="username"
                            type="text"
                            x-model="username"
                            required
                            autocomplete="username"
                            placeholder="Enter your username"
                            value="{{ old('username') }}"
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label for="login-password">Password</label>
                    <div class="input-wrap">
                        <input
                            id="login-password"
                            name="password"
                            :type="passwordVisible ? 'text' : 'password'"
                            x-model="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        <button type="button" class="toggle-password" @click="passwordVisible = !passwordVisible" aria-label="Toggle password visibility">
                            <!-- Eye open -->
                            <svg x-show="!passwordVisible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Eye closed -->
                            <svg x-show="passwordVisible" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.828 9.828" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Validation Errors (server-side) -->
                @if ($errors->any())
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <span>
                            @foreach ($errors->all() as $error)
                                {{ $error }}@if (!$loop->last) — @endif
                            @endforeach
                        </span>
                    </div>
                @endif

                <!-- Client-side message (Alpine) -->
                <template x-if="message">
                    <div class="alert" :class="messageType === 'error' ? 'alert-error' : 'alert-success'" x-text="message"></div>
                </template>

                <!-- Submit -->
                <button type="submit" class="btn-submit" :disabled="loading" id="login-submit-btn">
                    <span x-show="loading" class="spinner"></span>
                    <span x-text="loading ? 'Signing in…' : 'Sign In'"></span>
                </button>
            </form>

            <div class="footer">
                <p>&copy; {{ date('Y') }} Iloilo City Government</p>
            </div>
        </div>
    </div>

<script>
function loginFlow() {
    return {
        username: '',
        password: '',
        passwordVisible: false,
        loading: false,
        message: '',
        messageType: '',

        async handleLogin() {
            this.loading = true;
            this.message = '';

            const formData = new FormData();
            formData.append('username', this.username);
            formData.append('password', this.password);

            try {
                const response = await fetch("{{ route('login') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const result = await response.json();

                if (result.success) {
                    this.message = 'Login successful! Redirecting…';
                    this.messageType = 'success';
                    setTimeout(() => {
                        window.location.href = result.redirect || '{{ route("dashboard") }}';
                    }, 800);
                } else {
                    this.message = result.message || 'Invalid username or password.';
                    this.messageType = 'error';
                }
            } catch (e) {
                this.message = 'Server error. Please try again.';
                this.messageType = 'error';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

</body>
</html>