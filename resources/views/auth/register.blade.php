<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <!-- YOUR CUSTOM CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Tailwind (needed for your classes like flex, grid, etc.) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen animated-gradient flex items-center justify-center p-4"
    x-data="{ passVisible: false, confirmVisible: false, selectedRole: 'Administrator' }">

    <div class="auth-card animate-premium-in">

        <!-- LEFT PANEL -->
        <div class="auth-branding hidden md:flex">
            <div class="logo-container">
                <img src="{{ asset('assets/img/logo.png') }}" class="w-20 h-20 rounded-full object-cover">
            </div>

            <p class="text-sm text-white/80 mb-1">Iloilo City Government</p>

            <h1 class="text-3xl font-bold mb-2 font-display text-white">
                Iloilo City WMS
            </h1>

            <p class="text-sm text-white/90 mb-6 max-w-xs">
                A comprehensive inventory system for modern healthcare.
            </p>

            <div class="testimonial-box">
                <p class="text-xs italic">
                    "This system has revolutionized our inventory control..."
                </p>
                <p class="mt-4 text-xs font-semibold text-white/90">
                    - Head Pharmacist
                </p>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="auth-form-container w-full md:w-1/2">

            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold font-display">Create Account</h2>

                <p class="mt-2 text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login.show') }}" class="text-blue-600 hover:underline font-medium">
                        Sign In
                    </a>
                </p>
            </div>

            <!-- ERRORS -->
            @if ($errors->any())
                <div class="mb-4 p-2 text-sm text-red-600 bg-red-50 border border-red-200 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" class="space-y-3">
                @csrf

                <!-- NAME -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input name="FName" value="{{ old('FName') }}" placeholder="First Name" required class="form-input">
                    <input name="MName" value="{{ old('MName') }}" placeholder="Middle Name" class="form-input">
                    <input name="LName" value="{{ old('LName') }}" placeholder="Last Name" required class="form-input">
                </div>

                <!-- ROLE -->
                <div>
                    <label class="text-sm font-medium">Role</label>
                    <select name="Role" x-model="selectedRole" class="form-select" required>
                        <option>Administrator</option>
                        <option>Head Pharmacist</option>
                        <option>Health Center Staff</option>
                        <option>Warehouse Staff</option>
                        <option>Accounting Office User</option>
                        <option>CMO/GSO/COA User</option>
                    </select>
                </div>

                <!-- HEALTH CENTER -->
                <div x-show="selectedRole === 'Health Center Staff'">
                    <label class="text-sm font-medium">Health Center</label>
                    <select name="HealthCenterID" class="form-select">
                        <option value="">Select Health Center</option>
                        @foreach($healthCenters as $hc)
                            <option value="{{ $hc->HealthCenterID }}">
                                {{ $hc->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- USERNAME -->
                <input name="username" value="{{ old('username') }}" placeholder="Username" required class="form-input">

                <!-- PASSWORDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <div class="relative">
                        <input name="password" :type="passVisible ? 'text' : 'password'" placeholder="Password" required
                            class="form-input pr-10">

                        <button type="button" @click="passVisible = !passVisible"
                            class="absolute right-3 top-3 text-gray-400">
                            👁
                        </button>
                    </div>

                    <div class="relative">
                        <input name="password_confirmation" :type="confirmVisible ? 'text' : 'password'"
                            placeholder="Confirm Password" required class="form-input pr-10">

                        <button type="button" @click="confirmVisible = !confirmVisible"
                            class="absolute right-3 top-3 text-gray-400">
                            👁
                        </button>
                    </div>

                </div>

                <!-- SUBMIT -->
                <button type="submit" class="btn btn-primary w-full">
                    Create Account
                </button>

            </form>

            <div class="mt-6 text-center text-xs text-gray-400">
                © 2026 ILOILO CITY
            </div>

        </div>
    </div>

    <!-- Alpine -->
    <script src="//unpkg.com/alpinejs" defer></script>

</body>

</html>