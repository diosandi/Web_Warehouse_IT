<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Maintenance RSCM</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-image: url('/images/background-rscm.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            z-index: 0;
        }

        .overlay {
            position: relative;
            z-index: 1;
        }

        .login-card {
            animation: slideIn 0.6s ease-out;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logo-section {
            background: linear-gradient(135deg, #009639 0%, #00a04a 50%, #1bb44c 100%);
            position: relative;
            overflow: hidden;
        }

        .logo-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
            pointer-events: none;
        }

        .input-with-icon {
            padding-left: 38px;
        }

        .input-with-icon:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        @media (max-width: 768px) {
            .logo-section {
                display: none;
            }

            .login-card {
                margin: 1rem;
            }
        }

        .logo-rscm {
            max-width: 140px;
            height: auto;
            filter: brightness(0) invert(1);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Overlay Background -->
    <div class="overlay min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl">

            <!-- Login Card dengan 2 Kolom -->
            <div class="login-card bg-white rounded-2xl overflow-hidden grid md:grid-cols-2">

                <!-- KOLOM KIRI - Form Login -->
                <div class="p-6 md:p-8 lg:p-10 flex flex-col justify-center bg-white">
                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex items-center mb-3">
                            <div class="h-10 w-1 bg-green-600 rounded-full mr-3"></div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">
                                    Selamat Datang
                                </h2>
                                <p class="text-gray-500 text-xs mt-0.5">
                                    Silakan login untuk melanjutkan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Login -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Error Message -->
                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Username Input -->
                        <div>
                            <label for="username" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Username
                            </label>
                            <div class="relative">
                                <div class="input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="username"
                                    name="username"
                                    type="text"
                                    autocomplete="username"
                                    required
                                    value="{{ old('username') }}"
                                    class="input-with-icon appearance-none block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                                    placeholder=""
                                >
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <div class="input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="input-with-icon appearance-none block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer"
                            >
                            <label for="remember" class="ml-2 block text-xs text-gray-700 cursor-pointer">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-1">
                            <button
                                type="submit"
                                class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-green-600 via-green-700 to-green-800 hover:from-green-700 hover:via-green-800 hover:to-green-900 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all duration-200 transform hover:scale-[1.02] hover:shadow-xl"
                            >
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Masuk ke Sistem
                            </button>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-5">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-3 bg-white text-gray-500">Informasi Login</span>
                        </div>
                    </div>

                    <!-- Info Default User -->
                    {{-- <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-3.5 shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-2.5 flex-1">
                                <h3 class="text-xs font-bold text-blue-900 mb-1.5">Kredensial Default</h3>
                                <div class="space-y-1 text-xs text-blue-800">
                                    <div class="flex items-center">
                                        <svg class="h-3.5 w-3.5 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Email:</span>
                                        <span class="ml-1.5 font-mono bg-white px-1.5 py-0.5 rounded text-xs">admin@rscm.co.id</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-3.5 w-3.5 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                        <span class="font-medium">Password:</span>
                                        <span class="ml-1.5 font-mono bg-white px-1.5 py-0.5 rounded text-xs">password</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <!-- KOLOM KANAN - Logo RSCM -->
                <div class="logo-section hidden md:flex flex-col items-center justify-center p-8 text-white relative">
                    <div class="text-center relative z-10">
                        <!-- Logo Kemenkes RSCM -->
                        <div class="mb-6">
                            <img
                                src="{{ url('images/rscm_new.png') }}"
                                alt="Logo Kemenkes RSCM"
                                class="logo-rscm mx-auto"
                                style="max-width: 160px;"
                            >
                        </div>

                        <!-- Text -->
                        <div class="space-y-3">
                            <h1 class="text-3xl font-bold tracking-wide">
                                IT MAINTENANCE
                            </h1>
                            <div class="h-0.5 w-20 bg-white mx-auto rounded-full opacity-80"></div>
                            <h2 class="text-2xl font-semibold">
                                RSCM
                            </h2>
                            <p class="text-blue-100 text-sm leading-relaxed max-w-xs mx-auto mt-4">
                                Sistem Manajemen Kelola Barang dan Laporan Kendala Perangkat IT<br>
                                <span class="text-xs opacity-90">Rumah Sakit Cipto Mangunkusumo</span>
                            </p>
                        </div>

                        <!-- Decorative Elements -->
                        <div class="mt-8 flex justify-center space-x-2">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg"></div>
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse shadow-lg" style="animation-delay: 0.4s"></div>
                        </div>

                        <!-- Icon Warehouse -->
                        <div class="mt-6 opacity-40">
                            <svg class="w-12 h-12 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <div class="inline-block bg-white/10 backdrop-blur-md rounded-full px-6 py-3 shadow-lg">
                    <p class="text-sm text-white font-medium">
                        © 2026 RSCM - Rumah Sakit Cipto Mangunkusumo
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

