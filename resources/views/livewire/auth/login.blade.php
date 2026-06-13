<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50 flex items-center justify-center p-4">
    <style>
        @keyframes float-up {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float-in-left {
            0% {
                opacity: 0;
                transform: translateX(-30px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(249, 115, 22, 0.5);
            }

            50% {
                box-shadow: 0 0 40px rgba(234, 88, 12, 0.8);
            }
        }

        @keyframes float-blob-1 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(30px, -50px);
            }

            66% {
                transform: translate(-20px, 20px);
            }
        }

        @keyframes float-blob-2 {

            0%,
            100% {
                transform: translate(0, 0);
            }

            33% {
                transform: translate(-30px, 50px);
            }

            66% {
                transform: translate(20px, -20px);
            }
        }

        @keyframes slide-down {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes input-focus-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.1);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(249, 115, 22, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(249, 115, 22, 0);
            }
        }

        .animate-float-up {
            animation: float-up 0.6s ease-out forwards;
        }

        .animate-float-in-left {
            animation: float-in-left 0.6s ease-out forwards;
        }

        .animate-pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        .animate-float-blob-1 {
            animation: float-blob-1 20s ease-in-out infinite;
        }

        .animate-float-blob-2 {
            animation: float-blob-2 20s ease-in-out infinite;
        }

        .animate-slide-down {
            animation: slide-down 0.5s ease-out;
        }

        .input-glow:focus {
            animation: input-focus-glow 0.6s ease-out;
        }

        /* Stagger animations */
        .stagger-1 {
            animation-delay: 0.1s;
        }

        .stagger-2 {
            animation-delay: 0.2s;
        }

        .stagger-3 {
            animation-delay: 0.3s;
        }

        .stagger-4 {
            animation-delay: 0.4s;
        }

        .stagger-5 {
            animation-delay: 0.5s;
        }
    </style>

    <!-- Decorative Elements -->
    <div
        class="absolute top-0 left-0 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -z-10 animate-float-blob-1">
    </div>
    <div
        class="absolute bottom-0 right-0 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -z-10 animate-float-blob-2">
    </div>

    <div class="w-full max-w-md animate-float-up">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8 space-y-8 backdrop-blur-xl bg-opacity-95">
            <!-- Header -->
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-extrabold text-orange-600 animate-slide-down stagger-2">
                    Living<span class="text-gray-900">Kost</span></h1>
                <p class="text-gray-500 text-sm animate-slide-down stagger-3">
                    {{ $appTagline ?? 'Sistem Manajemen Kos' }}</p>
            </div>

            <!-- Form -->
            <form wire:submit="login" class="space-y-6">
                <!-- Email Input -->
                <div class="space-y-2 animate-slide-down stagger-1">
                    <label for="email" class="block text-sm font-semibold text-gray-700">
                        Email Address
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <input wire:model="email" id="email" type="email"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent focus:bg-white transition duration-300 input-glow @error('email') border-red-500 bg-red-50 @enderror"
                            placeholder="your@email.com" />
                    </div>
                    @error('email')
                        <div
                            class="flex items-center gap-2 mt-2 text-sm text-red-600 bg-red-50 p-3 rounded-lg animate-float-up">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-2 animate-slide-down stagger-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Password
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-orange-500 transition"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <input wire:model="password" id="password" type="password"
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent focus:bg-white transition duration-300 input-glow @error('password') border-red-500 bg-red-50 @enderror"
                            placeholder="••••••••" />
                    </div>
                    @error('password')
                        <div
                            class="flex items-center gap-2 mt-2 text-sm text-red-600 bg-red-50 p-3 rounded-lg animate-float-up">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between animate-slide-down stagger-3">
                    <label for="remember" class="flex items-center gap-3 cursor-pointer group">
                        <input wire:model="remember" id="remember" type="checkbox"
                            class="w-5 h-5 text-orange-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 cursor-pointer transition" />
                        <span class="text-sm text-gray-600 group-hover:text-gray-900 transition">
                            Ingat saya
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-orange-600 to-orange-600 hover:from-orange-700 hover:to-orange-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-300 transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:scale-100 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl animate-slide-down stagger-4">
                    <span wire:loading.remove>
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Login
                    </span>
                    <span wire:loading>
                        <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>

            <!-- Footer -->
            <div class="pt-6 border-t border-gray-200">
                <p class="text-center text-sm text-gray-600">
                    Hubungi administrator untuk membuat akun baru
                </p>
            </div>
        </div>

        <!-- Extra Info -->
        <div class="mt-8 text-center animate-float-up">
            <p class="text-xs text-gray-500">
                © 2026 {{ $appName ?? 'Living Kost' }}. All rights reserved.
            </p>
        </div>
    </div>
</div>
