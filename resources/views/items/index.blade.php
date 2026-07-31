@extends('layouts.app')

@section('content')
@php
    $selectedKategori = $filters['kategori'] ?? [];
    $selectedMerk = $filters['merk'] ?? [];
    $selectedAsset = $filters['asset'] ?? [];
    $selectedSource = $filters['source'] ?? null;
    $statusLabels = [
        'used' => 'Digunakan',
        'available' => 'Tersedia',
        'maintenance' => 'Pemeliharaan',
        'retired' => 'Tidak Digunakan',
        'vendor' => 'Dibawa Vendor',
    ];
    $sourceLabels = [
        'barang_masuk' => 'Barang Masuk',
        'master_item' => 'Master Item',
    ];
@endphp
<br>
<div class="distribution-page mx-auto w-full px-3 py-8 sm:px-4 lg:px-6 lg:py-12">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Master Data Barang</h1>
            <p class="text-sm md:text-base text-gray-600 mt-1">Kelola data perangkat IT (PC, Monitor, Printer, Scanner)</p>
        </div>
        <a href="{{ route('items.create', ['redirect' => url()->full()]) }}" class="btn btn-success">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="hidden sm:inline">Tambah</span>
            <span class="sm:hidden">Tambah</span>
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 md:p-4 rounded-lg mb-6 flex items-start gap-3">
            <svg class="w-5 md:w-6 h-5 md:h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif
    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.536-9.536a1 1 0 011.414-1.414L10 8.586l2.121-2.121a1 1 0 111.414 1.414L11.414 10l2.121 2.121a1 1 0 01-1.414 1.414L10 11.414l-2.121 2.121a1 1 0 01-1.414-1.414L8.586 10 6.464 7.879z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filter Section -->
    <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ request('search') || !empty($selectedKategori) || !empty($selectedMerk) || !empty($selectedAsset) || request('status') || $selectedSource ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Filter & Cari Barang</h2>
            </div>
            <svg class="w-5 h-5 text-gray-500 transition duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </summary>

        <div class="px-4 md:px-6 pb-4 md:pb-6">
        <form action="{{ route('items.index') }}" method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div>
                <label for="search" class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">🔍 Cari Barang</label>
                <div class="relative">
                    <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}" placeholder="Cari S/N, Service Tag, Asset, Merk, Type, Processor, OS, PO..." class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                    <input type="hidden" name='item_id' id="item_id_hidden">
                    <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    @if(request('search'))
                        <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold">{{ strlen(request('search')) }} char</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Tekan Enter atau klik Cari untuk mencari di semua field</p>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <!-- Kategori Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200">
                            <span class="text-gray-700 truncate text-xs uppercase">
                                {{ empty($selectedKategori) ? '-- Semua Kategori --' : collect($selectedKategori)->map(fn ($kategori) => $kategoriOptions[$kategori] ?? $kategori)->implode(', ') }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($kategoriOptions as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="kategori[]" value="{{ $value }}" {{ in_array($value, $selectedKategori, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Merk Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Merk</label>
                    <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200">
                            <span class="text-gray-700 truncate text-xs uppercase">
                                {{ empty($selectedMerk) ? '-- Semua Merk --' : implode(', ', $selectedMerk) }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($merkList as $merk)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="merk[]" value="{{ $merk }}" {{ in_array($merk, $selectedMerk, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $merk }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Asset Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Asset</label>
                    <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200">
                            <span class="text-gray-700 truncate text-xs uppercase">
                                {{ empty($selectedAsset) ? '-- Semua Asset --' : implode(', ', $selectedAsset) }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @forelse($assetList as $asset)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="asset[]" value="{{ $asset }}" {{ in_array($asset, $selectedAsset, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="text-xs uppercase">{{ $asset }}</span>
                                    </label>
                                @empty
                                    <p class="text-xs text-gray-500">Belum ada data asset</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Kondisi Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                        <select name="status" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option value="">Semua</option>
                            <option value="used" {{ request('status')=='used'?'selected':'' }}>Digunakan</option>
                            <option value="available" {{ request('status')=='available'?'selected':'' }}>Tersedia</option>
                            <option value="maintenance" {{ request('status')=='maintenance'?'selected':'' }}>Pemeliharaan</option>
                            <option value="retired" {{ request('status')=='retired'?'selected':'' }}>Tidak Digunakan</option>
                            <option value="vendor" {{ request('status')=='vendor'?'selected':'' }}>Dibawa Vendor</option>
                        </select>
                </div>

                <!-- Asal Data Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Asal Data</label>
                    <select name="source" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                        <option value="">Semua</option>
                        <option value="barang_masuk" {{ $selectedSource === 'barang_masuk' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="master_item" {{ $selectedSource === 'master_item' ? 'selected' : '' }}>Master Item</option>
                    </select>
                </div>
            </div>

                <!-- Buttons -->
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <button type="submit" class="btn btn-success btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-secondary btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Bersihkan</span>
                    </a>
                </div>


            <!-- Active Filters Display -->
            @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk) || !empty($selectedAsset) || request('status') || $selectedSource)
                <div class="text-xs md:text-sm text-gray-600 pt-3 border-t border-gray-200">
                    <span class="font-semibold text-gray-700 block mb-2">Filter aktif:</span>
                    <div class="flex flex-wrap gap-2">
                        @if(request('search'))
                            @php
                                $searchQuery = request()->query();
                                unset($searchQuery['search']);
                            @endphp
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>🔍 Cari: <strong>"{{ request('search') }}"</strong></span>
                                <a href="{{ route('items.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                </a>
                            </span>
                        @endif
                        @if(!empty($selectedKategori))
                            @php
                                $kategoriQuery = request()->query();
                                unset($kategoriQuery['kategori']);
                            @endphp
                            <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Kategori: <strong>{{ collect($selectedKategori)->map(fn ($kategori) => $kategoriOptions[$kategori] ?? $kategori)->implode(', ') }}</strong></span>
                                <a href="{{ route('items.index', $kategoriQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        @if(!empty($selectedMerk))
                            @php
                                $merkQuery = request()->query();
                                unset($merkQuery['merk']);
                            @endphp
                            <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Merk: <strong>{{ implode(', ', $selectedMerk) }}</strong></span>
                                <a href="{{ route('items.index', $merkQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        @if(!empty($selectedAsset))
                            @php
                                $assetQuery = request()->query();
                                unset($assetQuery['asset']);
                            @endphp
                            <span class="bg-cyan-100 text-cyan-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Asset: <strong>{{ implode(', ', $selectedAsset) }}</strong></span>
                                <a href="{{ route('items.index', $assetQuery) }}" class="hover:text-cyan-900 font-bold text-lg leading-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif
                        @if(request('status'))
                            @php
                                $statusQuery = request()->query();
                                unset($statusQuery['status']);
                            @endphp
                            <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Kondisi: <strong>{{ $statusLabels[request('status')] ?? request('status') }}</strong></span>
                                <a href="{{ route('items.index', $statusQuery) }}" class="hover:text-purple-900 font-bold text-lg leading-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif


                        @if($selectedSource)
                            @php
                                $sourceQuery = request()->query();
                                unset($sourceQuery['source']);
                            @endphp
                            <span class="bg-amber-100 text-amber-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Asal Data: <strong>{{ $sourceLabels[$selectedSource] ?? $selectedSource }}</strong></span>
                                <a href="{{ route('items.index', $sourceQuery) }}" class="hover:text-amber-900 font-bold text-lg leading-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                        @endif

                        <!-- Clear All Button -->
                        <a href="{{ route('items.index') }}" class="btn btn-soft-danger btn-sm px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                            Hapus Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </form>
        </div>
    </details>

    <!-- Daftar Master Data Barang -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Result Counter -->
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $items->total() }}</span>
                <span>Data Item Ditemukan</span>
                @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk) || !empty($selectedAsset) || request('status') || $selectedSource)
                    <span class="text-gray-500">(dari total database)</span>
                @endif
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $items->currentPage() }}</span> dari <span class="font-semibold">{{ $items->lastPage() }}</span>
            </div>
        </div>

        <!-- Table -->
        <div class="distribution-table-wrap overflow-x-auto">
            <table class="distribution-table w-full min-w-max divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Asal Data</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal Input</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Terakhir Diubah</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Asset</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Merk</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tipe</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">S/N</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">OS</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Processor</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">RAM</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Tahun</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Lokasi Saat Ini</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Kondisi</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $index => $item)
                        @php
                            $activeDistributionItem = $item->distributionItems->first();
                            $currentLocation = $activeDistributionItem?->distribution?->location ?? $item->storageLocation;
                            $currentLocationLabel = $currentLocation
                                ? collect([$currentLocation->gedung, $currentLocation->ruangan])->filter()->implode(' - ')
                                : '-';
                            $currentLocationLabel = $currentLocationLabel !== '' ? $currentLocationLabel : '-';
                            $isActivelyUsed = (bool) $activeDistributionItem;
                            $displayStatus = $isActivelyUsed
                                ? 'used'
                                : ($item->status === 'used' ? 'available' : $item->status);
                        @endphp
                        <tr class="hover:bg-green-50 transition duration-150">
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs md:text-sm text-gray-900">{{ $items->firstItem() + $index }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs md:text-sm font-semibold">
                                @if($item->kategori === 'PC')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">PC</span>
                                @elseif($item->kategori === 'Monitor')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Monitor</span>
                                @elseif($item->kategori === 'Printer Kertas')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">P.Kertas</span>
                                @elseif($item->kategori === 'Printer Barcode')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">P.Barcode</span>
                                @elseif($item->kategori === 'Scanner')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Scanner</span>
                                @elseif($item->kategori === 'Lainnya')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">Lainnya</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs md:text-sm font-semibold">
                                @if($item->barang_masuk_id)
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Barang Masuk</span>
                                @else
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Master Item</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">
                                {{ \App\Support\DateFormatter::date($item->barang_masuk?->tanggal_masuk ?? $item->created_at) }}
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">
                                {{ \App\Support\DateFormatter::date($item->updated_at) }}
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm font-semibold text-gray-700">{{ $item->asset ? Str::limit($item->asset, 12) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->merk ? Str::limit($item->merk, 10) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->type ? Str::limit($item->type) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm font-mono text-gray-700"><a href="{{ route('items.show', [$item->id, 'redirect' => url()->full()]) }}"
                                                                                                                                                class="text-green-600 hover:text-green-800 hover:underline font-semibold">

                                                                                                                                                {{ $item->serial_number }}

                                                                                                                                            </a></td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm font-mono text-gray-700 hidden lg:table-cell">{{ $item->os ? Str::limit($item->os, 8) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->processor ? Str::limit($item->processor, 8) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->ram_gb ? $item->ram_gb . 'G' : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700 hidden md:table-cell">{{ $item->tahun ?? '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ Str::limit($currentLocationLabel, 32) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                               @if(in_array($item->kategori, ['Printer Kertas', 'Printer Barcode']))
                                    @if($displayStatus == 'used')
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-800">Digunakan</span>
                                    @elseif($displayStatus == 'available')
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-800">Tersedia</span>
                                    @elseif($displayStatus == 'maintenance')
                                        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">Pemeliharaan</span>
                                    @elseif($displayStatus == 'vendor')
                                        <span class="px-2 py-1 rounded bg-purple-100 text-purple-800">Dibawa Vendor</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800">Tidak Digunakan</span>
                                    @endif

                                @else

                                    {{-- selain printer --}}
                                    @if($displayStatus == 'used')
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-800">Digunakan</span>
                                    @elseif($displayStatus == 'available')
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-800">Tersedia</span>
                                    @elseif($displayStatus == 'maintenance')
                                        <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800">Pemeliharaan</span>
                                    @elseif($displayStatus == 'vendor')
                                        <span class="px-2 py-1 rounded bg-purple-100 text-purple-800">Dibawa Vendor</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800">Tidak Digunakan</span>
                                    @endif

                                @endif
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex gap-1 md:gap-2 justify-center flex-wrap">
                                     <!-- DETAIL -->
                                    <a href="{{route('items.detail', ['item' => $item->id,'redirect'=> url()->full()]) }}" title="Detail" class="btn btn-primary btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="#1C274C"></circle>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17V11 M 7 3.33782 C 8.47087 2.48697 10.1786 2 12 2 C 17.5228 2 22 6.47715 22 12 C 22 17.5228 17.5228 22 12 22 C 6.47715 22 2 17.5228 2 12 C 2 10.1786 2.48697 8.47087 3.33782 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('items.edit', ['item' => $item->id,'redirect' => url()->full()]) }}" title="Edit" class="btn btn-warning btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('items.destroy', [$item->id, 'redirect' => url()->full()]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" class="btn btn-danger btn-icon">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    <!-- Jika Data Kosong -->
                    @empty
                        <tr>
                            <td colspan="16" class="px-3 md:px-4 py-8">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">
                                        @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk) || !empty($selectedAsset) || request('status') || $selectedSource)
                                            Tidak ada hasil yang cocok
                                        @else
                                            Belum ada data master barang
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-3 md:px-4 py-4 border-t border-gray-200 overflow-x-auto">
            <div class="flex justify-center md:justify-end">
                {{ $items->links() }}
            </div>
        </div>

    </div>
</div>

<style>
    /* Custom pagination styling untuk responsif */
    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .filter-summary {
        border-color: #d1d5db;
        outline: none;
        box-shadow: none;
    }

    .filter-dropdown[open] > .filter-summary {
        border-color: #22c55e; /* green-500 */
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5); /* ring */
        background-color: #fcfcfc;
    }
</style>
<script>
$(document).ready(function() {
    var $input = $('#search');
    var $suggestions = $('#suggestions');
    var $hidden = $('#item_id_hidden');
    var searchDelay;

    $input.on('input', function() {
        var query = $(this).val().trim();
        $hidden.val(''); // reset hidden
        clearTimeout(searchDelay);

        if (query.length < 3) {
            $suggestions.empty().hide();
            return;
        }

        searchDelay = setTimeout(function() {
            $.ajax({
                url: '{{ route('items.search_items') }}',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                        return;
                    }
                    var html = '';
                    $.each(data, function(i, item) {
                        html += '<div class="px-3 py-2 cursor-pointer hover:bg-green-100" data-id="'+item.id+'" data-text="'+item.text+'">'+item.text+'</div>';
                    });
                    $suggestions.html(html).show();
                }
            });
        }, 250);
    });

    $suggestions.on('click', 'div[data-id]', function() {
        var id = $(this).data('id');
        var text = $(this).data('text');
        $input.val(text);
        $hidden.val(id);
        $suggestions.hide();
    });

    // Hide suggestions on click outside
    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#search, #suggestions').length) {
            $suggestions.hide();
        }
    });
});

</script>
@endsection
