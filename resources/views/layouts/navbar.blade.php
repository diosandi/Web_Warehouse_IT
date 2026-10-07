<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-lg z-50 h-16 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
    <div class="px-3 md:px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full gap-3">
            <!-- Left: Toggle & Logo -->
            <div class="flex items-center gap-2 md:gap-3 min-w-0">
                <!-- Toggle Button (Always Visible) -->
                <button
                    id="sidebar-toggle"
                    class="btn btn-icon btn-light dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600 flex-shrink-0"
                    type="button"
                    aria-label="Toggle sidebar"
                    title="Buka/Tutup Menu">
                    <svg class="h-5 w-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition duration-200 flex-shrink-0">
                    <div class="h-9 w-9 bg-gradient-to-br from-green-600 to-green-700 rounded-lg flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="hidden sm:block min-w-0">
                        <h1 class="text-base md:text-lg font-bold text-gray-800 dark:text-gray-100 truncate">IT Maintenance</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">RSCM IPLT</p>
                    </div>
                </a>
            </div>

            <!-- Right: User Menu & Logout -->
            <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                     {{-- Icon Matahari (Muncul saat Dark Mode) --}}
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path></svg>
                </button>

                <!-- User Info (Hidden on mobile) -->
                <div class="hidden sm:flex items-center gap-2 md:gap-3 bg-gray-50 dark:bg-gray-700 px-2 md:px-4 py-2 rounded-lg">
                    <div class="h-8 md:h-9 w-8 md:w-9 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-xs md:text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-300 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm flex-shrink-0"
                        title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
