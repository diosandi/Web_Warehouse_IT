<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 bg-white shadow-lg z-50 h-16 border-b border-gray-200">
    <div class="px-3 md:px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full gap-3">
            <!-- Left: Toggle & Logo -->
            <div class="flex items-center gap-2 md:gap-3 min-w-0">
                <!-- Toggle Button (Always Visible) -->
                <button 
                    id="sidebar-toggle" 
                    class="btn btn-icon btn-light flex-shrink-0"
                    type="button"
                    aria-label="Toggle sidebar"
                    title="Buka/Tutup Menu">
                    <svg class="h-5 w-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <h1 class="text-base md:text-lg font-bold text-gray-800 truncate">Warehouse IT</h1>
                        <p class="text-xs text-gray-500">RSCM</p>
                    </div>
                </a>
            </div>

            <!-- Right: User Menu & Logout -->
            <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                <!-- User Info (Hidden on mobile) -->
                <div class="hidden sm:flex items-center gap-2 md:gap-3 bg-gray-50 px-2 md:px-4 py-2 rounded-lg">
                    <div class="h-8 md:h-9 w-8 md:w-9 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-xs md:text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
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
