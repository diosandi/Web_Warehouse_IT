@extends('layouts.app')

@section('content')
@php
    $statusLabels = [
        'dipakai' => 'Dipakai',
        'dikembalikan' => 'Dikembalikan',
    ];
    $selectedRuangan = $locations->firstWhere('id', (int) request('ruangan'));
    $hasActiveFilter = request()->filled('search')
        || request()->filled('status')
        || request()->filled('tanggal_dari')
        || request()->filled('tanggal_sampai')
        || request()->filled('gedung')
        || request()->filled('ruangan');
@endphp
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Distribusi Barang</h1>
            <p class="text-gray-600 mt-1">Kelola data distribusi perangkat IT</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <a href="{{ route('distribution.report_detail', request()->query()) }}"
               class="btn btn-primary">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Laporan Detail
            </a>
            <a href="{{ route('distribution.create', ['redirect' => url()->full()]) }}"
               class="btn btn-success">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah
            </a>
        </div>
    </div>

    <!-- Alert -->
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
     <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
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
        <form action="" method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div>
                <label for="search" class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">🔍 Cari Barang</label>
                <div class="relative">
                    <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}" placeholder="Cari user, SN, Merk" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                    <input type="hidden" name="item_id" id="item_id_hidden">
                    <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    @if(request('search'))
                        <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold">{{ strlen(request('search')) }} char</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Tekan Enter atau klik Cari untuk mencari di semua field</p>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option value="">Semua</option>
                            <option value="dipakai" {{ request('status')=='dipakai'?'selected':'' }}>Dipakai</option>
                            <option value="dikembalikan" {{ request('status')=='dikembalikan'?'selected':'' }}>Dikembalikan</option>
                        </select>
                </div>
                <!-- TANGGAL -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
                </div>
                <!-- LOKASI -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Gedung</label>
                    <select id="filter_gedung" name="gedung" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                        <option value="">Semua</option>
                        @foreach($locations->unique('gedung') as $loc)
                            <option value="{{ $loc->gedung }}"
                                {{ request('gedung') == $loc->gedung ? 'selected' : '' }}>
                                {{ $loc->gedung }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Ruangan</label>
                    <select id="filter_ruangan" name="ruangan" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                        <option value="">Semua</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('ruangan')==$loc->id?'selected':'' }}>
                                {{ $loc->ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <button type="submit" class="btn btn-success btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    <a href="{{ route('distribution.index') }}" class="btn btn-secondary btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Bersihkan</span>
                    </a>
                </div>
                @if($hasActiveFilter)
                    <div class="text-xs md:text-sm text-gray-600 pt-3 border-t border-gray-200">
                        <span class="font-semibold text-gray-700 block mb-2">Filter aktif:</span>
                        <div class="flex flex-wrap gap-2">
                            @if(request('search'))
                                @php
                                    $searchQuery = request()->query();
                                    unset($searchQuery['search'], $searchQuery['item_id']);
                                @endphp
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Cari: <strong>"{{ request('search') }}"</strong></span>
                                    <a href="{{ route('distribution.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            @if(request('status'))
                                @php
                                    $statusQuery = request()->query();
                                    unset($statusQuery['status']);
                                @endphp
                                <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Status: <strong>{{ $statusLabels[request('status')] ?? request('status') }}</strong></span>
                                    <a href="{{ route('distribution.index', $statusQuery) }}" class="hover:text-purple-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            @if(request('tanggal_dari') || request('tanggal_sampai'))
                                @php
                                    $tanggalQuery = request()->query();
                                    unset($tanggalQuery['tanggal_dari'], $tanggalQuery['tanggal_sampai']);
                                    $tanggalLabel = request('tanggal_dari') && request('tanggal_sampai')
                                        ? request('tanggal_dari') . ' sampai ' . request('tanggal_sampai')
                                        : (request('tanggal_dari') ? 'Mulai ' . request('tanggal_dari') : 'Sampai ' . request('tanggal_sampai'));
                                @endphp
                                <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Tanggal: <strong>{{ $tanggalLabel }}</strong></span>
                                    <a href="{{ route('distribution.index', $tanggalQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            @if(request('gedung'))
                                @php
                                    $gedungQuery = request()->query();
                                    unset($gedungQuery['gedung'], $gedungQuery['ruangan']);
                                @endphp
                                <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Gedung: <strong>{{ request('gedung') }}</strong></span>
                                    <a href="{{ route('distribution.index', $gedungQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            @if(request('ruangan'))
                                @php
                                    $ruanganQuery = request()->query();
                                    unset($ruanganQuery['ruangan']);
                                @endphp
                                <span class="bg-cyan-100 text-cyan-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Ruangan: <strong>{{ $selectedRuangan?->ruangan ?? request('ruangan') }}</strong></span>
                                    <a href="{{ route('distribution.index', $ruanganQuery) }}" class="hover:text-cyan-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            <a href="{{ route('distribution.index') }}" class="btn btn-soft-danger btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Hapus Semua
                            </a>
                        </div>
                    </div>
                @endif
        </form>
        </div>
    </details>


    <!-- Daftar Distribution  -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Result Counter -->
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $distribution->total() }}</span>
                <span>Data Distribusi Ditemukan</span>
                @if($hasActiveFilter)
                    <span class="text-gray-500">(dari total database)</span>
                @endif
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $distribution->currentPage() }}</span> dari <span class="font-semibold">{{ $distribution->lastPage() }}</span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">PC</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Monitor</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Printer Kertas</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Printer Barcode</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Scanner</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Lainnya</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Divisi</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Lokasi</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <!-- BODY -->
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($distribution as $d)
                       @php
                            $kategori = [
                                'PC' => [],
                                'Monitor' => [],
                                'Printer Kertas' => [],
                                'Printer Barcode' => [],
                                'Scanner' => [],
                                'Lainnya' => [],
                            ];

                            $visibleDistributionItems = $d->status === 'dikembalikan'
                                ? $d->distributionItems
                                : $d->distributionItems->where('status', 'dipakai');

                            foreach($visibleDistributionItems as $di){
                                $item = $di->item;

                                if(isset($kategori[$item->kategori])) {
                                        $kategori[$item->kategori][] = [
                                        'id' => $item->id,
                                        'serial_number' => $item->serial_number,
                                        'merk' => $item->merk,
                                    ];
                                }
                            }
                        @endphp
                        <tr class="{{$d->status == 'dikembalikan' ? 'bg-gray-100 opacity-70': ''}}">
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $loop->iteration + ($distribution->currentPage() - 1) * $distribution->perPage() }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['PC']))
                                                                                                       @foreach($kategori['PC'] as $pc)
                                                                                                            <div>
                                                                                                                @if($d->status == 'dikembalikan')
                                                                                                                    <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </span>
                                                                                                                @else
                                                                                                                    <a href="{{ route('items.show', [$pc['id'],'redirect' => url()->full()]) }}"
                                                                                                                    title="Lihat detail Item"
                                                                                                                    class="text-green-600 hover:text-green-800 hover:underline font-semibold">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </a>
                                                                                                                @endif
                                                                                                                    / {{ $pc['merk'] ?? '-' }}
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['Monitor']))
                                                                                                        @foreach($kategori['Monitor'] as $pc)
                                                                                                            <div>
                                                                                                                @if($d->status == 'dikembalikan')
                                                                                                                    <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </span>
                                                                                                                @else
                                                                                                                    <a href="{{ route('items.show', [$pc['id'],'redirect' => url()->full()]) }}"
                                                                                                                    title="Lihat detail Item"
                                                                                                                    class="text-green-600 hover:underline">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </a>
                                                                                                                @endif
                                                                                                                    / {{ $pc['merk'] }}
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['Printer Kertas']))
                                                                                                        <div class="flex flex-col gap-1">
                                                                                                            @foreach($kategori['Printer Kertas'] as $pk)
                                                                                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                                                                                    @if($d->status == 'dikembalikan')
                                                                                                                        <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                            {{ $pk['serial_number'] }}
                                                                                                                        </span>
                                                                                                                    @else
                                                                                                                        <a href="{{ route('items.show', [$pk['id'],'redirect' => url()->full()]) }}"
                                                                                                                        title="Lihat detail Item"
                                                                                                                        class="hover:underline">
                                                                                                                            {{ $pk['serial_number'] }}
                                                                                                                        </a>
                                                                                                                    @endif
                                                                                                                        / {{ $pk['merk'] }}
                                                                                                                </span>
                                                                                                            @endforeach
                                                                                                            <span class="text-xs text-gray-500">
                                                                                                                Total: {{ count($kategori['Printer Kertas']) }}
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['Printer Barcode']))
                                                                                                        <div class="flex flex-col gap-1">
                                                                                                            @foreach($kategori['Printer Barcode'] as $pb)
                                                                                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                                                                                    @if($d->status == 'dikembalikan')
                                                                                                                        <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                            {{ $pb['serial_number'] }}
                                                                                                                        </span>
                                                                                                                    @else
                                                                                                                        <a href="{{ route('items.show', [$pb['id'],'redirect' => url()->full()]) }}"
                                                                                                                        title="Lihat detail Item"
                                                                                                                        class="hover:underline">
                                                                                                                            {{ $pb['serial_number'] }}
                                                                                                                        </a>
                                                                                                                    @endif
                                                                                                                        / {{ $pb['merk'] }}
                                                                                                                </span>
                                                                                                            @endforeach
                                                                                                            <span class="text-xs text-gray-500">
                                                                                                                Total: {{ count($kategori['Printer Barcode']) }}
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['Scanner']))
                                                                                                        @foreach($kategori['Scanner'] as $pc)
                                                                                                             <div>
                                                                                                                @if($d->status == 'dikembalikan')
                                                                                                                    <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </span>
                                                                                                                @else
                                                                                                                    <a href="{{ route('items.show', [$pc['id'],'redirect' => url()->full()]) }}"
                                                                                                                    title="Lihat detail Item"
                                                                                                                    class="text-green-600 hover:underline">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </a>
                                                                                                                @endif
                                                                                                                    / {{ $pc['merk'] }}
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">@if(count($kategori['Lainnya']))
                                                                                                        @foreach($kategori['Lainnya'] as $pc)
                                                                                                             <div>
                                                                                                                @if($d->status == 'dikembalikan')
                                                                                                                    <span class="text-gray-400 cursor-not-allowed font-semibold">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </span>
                                                                                                                @else
                                                                                                                    <a href="{{ route('items.show', [$pc['id'],'redirect' => url()->full()]) }}"
                                                                                                                    title="Lihat detail Item"
                                                                                                                    class="text-green-600 hover:underline">
                                                                                                                        {{ $pc['serial_number'] }}
                                                                                                                    </a>
                                                                                                                @endif
                                                                                                                    / {{ $pc['merk'] }}
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    @else
                                                                                                        -
                                                                                                    @endif</td>                                                     
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $d->nama_user }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $d->divisi ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $d->location->gedung ?? '-' }} - {{ $d->location->ruangan ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $d->tanggal_distribusi }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $d->keterangan }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm uppercase text-gray-900">
                            @if($d->status == 'dipakai')
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs">
                                    Dipakai
                                </span>
                            @elseif($d->status == 'partial')
                                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs">
                                    Sebagian Diretur
                                </span>
                            @else
                                <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs">
                                    Dikembalikan
                                </span>
                            @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="inline-flex gap-2">
                                    <!-- Edit pakai id distribusi pertama -->
                                    @if($d->status != 'dikembalikan')
                                    <a href="{{ route('distribution.edit', [$d->id, 'redirect' => url()->full()]) }}" title="Edit" class="btn btn-warning btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                    <!-- Hapus pakai id distribusi pertama -->
                                    {{-- <form action="{{ route('distribution.destroy', $d->id) }}" title="retur" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-violet-500 hover:bg-violet-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="M5.516 14.224c-2.262-2.432-2.222-6.244.128-8.611a6.07 6.07 0 0 1 3.414-1.736L8.989 1.8a8.1 8.1 0 0 0-4.797 2.351c-3.149 3.17-3.187 8.289-.123 11.531l-1.741 1.752l5.51.301l-.015-5.834zm6.647-11.959l.015 5.834l2.307-2.322c2.262 2.434 2.222 6.246-.128 8.611a6.07 6.07 0 0 1-3.414 1.736l.069 2.076a8.12 8.12 0 0 0 4.798-2.35c3.148-3.172 3.186-8.291.122-11.531l1.741-1.754z"/>
                                            </svg>
                                        </button>
                                    </form> --}}
                                    @if($d->status != 'dikembalikan')
                                        <button title="retur" class="btn btn-purple btn-icon"
                                            onclick="openReturnModal({{ $d->id }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="M5.516 14.224c-2.262-2.432-2.222-6.244.128-8.611a6.07 6.07 0 0 1 3.414-1.736L8.989 1.8a8.1 8.1 0 0 0-4.797 2.351c-3.149 3.17-3.187 8.289-.123 11.531l-1.741 1.752l5.51.301l-.015-5.834zm6.647-11.959l.015 5.834l2.307-2.322c2.262 2.434 2.222 6.246-.128 8.611a6.07 6.07 0 0 1-3.414 1.736l.069 2.076a8.12 8.12 0 0 0 4.798-2.35c3.148-3.172 3.186-8.291.122-11.531l1.741-1.754z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                    <!-- Jika Data Kosong -->
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 md:px-4 py-8">
                                  <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">
                                        @if($hasActiveFilter)
                                            Tidak ada hasil yang cocok
                                        @else
                                            Belum ada data distribusi
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
                {{ $distribution->links() }}
            </div>
        </div>

    </div>
</div>

<!-- RETURN MODAL -->
<div id="returnModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="flex min-h-screen w-full items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-2xl">

        <h2 class="mb-6 text-xl font-bold text-gray-900">
            Pengembalian Barang 1 SET
        </h2>

        <form id="returnForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Lokasi Penyimpanan
                </label>

                <select name="storage_location_id" class="popup-select w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" required>
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($warehouseLocations as $location)
                        <option value="{{ $location->id }}">
                            {{ $location->gedung }} -
                            {{ $location->ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Kondisi Barang
                </label>

                <select name="condition_status" class="popup-select w-full rounded-lg border border-gray-300 px-4 py-3 text-sm" required>
                    <option value="available">
                        Normal / Tersedia
                    </option>
                    <option value="maintenance">
                        Rusak / Pemeliharaan
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Keterangan Kondisi
                </label>

                <textarea
                    name="condition_note"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm"
                    rows="3"
                    placeholder="Contoh: Monitor bergaris, printer mati total"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    onclick="closeReturnModal()"
                    class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </button>
            </div>
            
        </form>

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
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('search');

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });

});

$(document).ready(function() {
    var $input = $('#search');
    var $suggestions = $('#suggestions');
    var $hidden = $('#item_id_hidden');
    var searchDelay;

    $input.on('input', function() {
        var query = $(this).val().trim();
        $hidden.val(''); // reset hidden
        clearTimeout(searchDelay);

        if (query.length < 1) {
            $suggestions.empty().hide();
            return;
        }

        searchDelay = setTimeout(function() {
            $.ajax({
                url: '{{ route('distribution.search_distribution') }}',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                        return;
                    }
                    var html = '';
                        $.each(data, function(i, item) {
                        html += `
                            <div class="px-3 py-2 cursor-pointer hover:bg-green-100"
                                data-id="${item.id}"
                                data-serial="${item.serial_number}"
                                data-text="${item.text}">
                                ${item.text}
                            </div>
                            `;
                    });
                    $suggestions.html(html).show();
                }
            });
        }, 300);
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

// Pop Up Modal
function openReturnModal(id)
{
    document.getElementById('returnModal')
        .classList.remove('hidden');

    document.getElementById('returnModal')
        .classList.add('flex');

    document.getElementById('returnForm')
        .action = `/distribution/${id}/return`;
}

function closeReturnModal()
{
    document.getElementById('returnModal')
        .classList.add('hidden');

    document.getElementById('returnModal')
        .classList.remove('flex');
}

$('#filter_gedung').on('change', function () {

    let gedung = $(this).val();

    $.ajax({
        url: '/get-ruangan',
        data: { gedung: gedung },
        success: function(data) {

            let html = '<option value="">Semua Ruangan</option>';

            data.forEach(r => {
                html += `
                    <option value="${r.id}">
                        ${r.ruangan}
                    </option>
                `;
            });

            $('#filter_ruangan').html(html);
        }
    });

});

</script>
@endsection
