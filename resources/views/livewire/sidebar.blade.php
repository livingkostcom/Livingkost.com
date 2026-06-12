<div class="flex h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50" x-data="{ sidebarOpen: false }"
    @keydown.escape="sidebarOpen = false">
    <!-- Sidebar Mobile Overlay -->
    <div class="fixed inset-0 bg-black/50 z-20 md:hidden transition-opacity duration-300"
        :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
        @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside
        class="fixed left-0 top-0 bottom-0 w-64 bg-white text-gray-900 transition-transform duration-300 ease-in-out z-40 md:z-0 md:relative overflow-y-auto md:translate-x-0 shadow border-r border-gray-200"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <!-- Logo Section -->
        <div class="h-16 border-b border-gray-200 flex items-center px-6">
            <div class="flex items-center justify-between w-full">
                <div>
                    <h1 class="text-xl font-extrabold text-orange-600">
                        Living<span class="text-gray-900">Kost</span></h1>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            @foreach ($menuItems as $item)
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 @if ($item['active']) bg-gradient-to-r from-orange-600 to-orange-600 text-white shadow-lg @else text-gray-700 hover:bg-orange-50 hover:text-orange-600 @endif"
                    @click="sidebarOpen = false">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}">
                        </path>
                    </svg>
                    <span class="font-medium text-sm">{{ $item['label'] }}</span>
                    @if ($item['active'])
                        <div class="ml-auto w-2 h-2 rounded-full bg-white"></div>
                    @endif
                </a>
            @endforeach
        </nav>

        <!-- Logout Section -->
        <div class="p-4 border-t border-gray-200">
            @livewire('auth.logout')
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navigation Bar -->
        <nav class="bg-white/80 backdrop-blur-md shadow-md sticky top-0 z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Mobile Menu Toggle -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden text-gray-700 hover:text-gray-900 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <h2
                        class="text-lg font-semibold bg-gradient-to-r from-orange-600 to-orange-600 bg-clip-text text-transparent">
                        @yield('page-title', 'Dasbor')</h2>

                    <!-- User Info for Desktop -->
                    <div class="hidden md:flex items-center space-x-4">
                        @livewire('notification-dropdown')
                        <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center font-bold text-white text-sm shadow-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>

                    <!-- Notification Bell for Mobile -->
                    <div class="md:hidden">
                        @livewire('notification-dropdown')
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="max-w-8xl mx-auto py-8 px-3 sm:px-5 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@livewireScripts

<style>
    /* Custom scrollbar for sidebar */
    aside::-webkit-scrollbar {
        width: 6px;
    }

    aside::-webkit-scrollbar-track {
        background: rgba(249, 250, 251, 0.5);
    }

    aside::-webkit-scrollbar-thumb {
        background: rgba(219, 234, 254, 0.7);
        border-radius: 3px;
    }

    aside::-webkit-scrollbar-thumb:hover {
        background: rgba(191, 219, 254, 0.9);
    }

    /* Smooth transitions */
    @media (prefers-reduced-motion: no-preference) {
        aside {
            transition: transform 0.3s ease-in-out;
        }
    }
</style>
