<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 z-40 w-64 bg-white shadow-lg h-[calc(100vh-4rem)] overflow-y-auto transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-gray-100">
    <div class="p-4 md:p-6">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Menu Utama</h2>
        
        <nav class="space-y-1 md:space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Dashboard</span>
            </a>

            <!-- Warehouse -->
            <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('locations.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Lokasi</span>
            </a>

            <!-- Master Produk -->
            <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('items.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Master Barang</span>
            </a>

            <!-- Device Details -->
            <a href="{{ route('device_details.index')}}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('device_details.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Detail Perangkat</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-3 md:my-4"></div>

            <!-- Barang Masuk -->
            <a href="{{ route('barang_masuk.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('barang_masuk.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Barang Masuk</span>
                {{-- <span class="ml-auto text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span> --}}
            </a>
            

            <!-- Barang Keluar -->
            <a href="{{ route('distribution.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('distribution.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9l-6 6-6-6"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Distribusi</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-3 md:my-4"></div>

            <!-- Manage User -->
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('users.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 0a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="font-medium text-sm md:text-base">Kelola Pengguna</span>
                </a>
            @endif

            <!-- Laporan -->
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 md:px-4 py-2 md:py-3 rounded-lg transition duration-200 {{ request()->routeIs('laporan.*') ? 'bg-green-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium text-sm md:text-base">Laporan</span>
            </a>
        </nav>
    </div>
</aside>
