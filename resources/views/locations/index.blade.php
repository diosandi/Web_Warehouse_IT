@extends('layouts.app')

@section('content')
@php
    $selectedType = $selectedType ?? ($filters['type'] ?? []);
    $typeLabels = [
        'warehouse' => 'Warehouse',
        'distribution' => 'Distribution',
        'maintenance' => 'Maintenance',
    ];
@endphp
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Master Lokasi</h1>
            <p class="text-gray-600 mt-1">Kelola data lokasi/ruangan penyimpanan barang</p>
        </div>
        <a href="{{ route('locations.create', ['redirect' => url()->full()]) }}" class="btn btn-success">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
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

    <!-- Filter Selection -->
    <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ request('search') || !empty($selectedGedung) || !empty($selectedRuangan) || !empty($selectedType) ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Filter & Cari Lokasi</h2>
            </div>
            <svg class="w-5 h-5 text-gray-500 transition duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </summary>
        <div class="px-4 md:px-6 pb-4 md:pb-6">
        <form action="{{ route('locations.index') }}" method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div>
                <label for="search" class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">🔍 Cari Lokasi</label>
                <div class="relative">
                    <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}" placeholder="Cari nama gedung dan ruangan" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase ">
                    <input type="hidden" name="location_id" id="location_id_hidden" > 
                    <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    @if(request('search'))
                        <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold ">{{ strlen(request('search')) }} char</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1 ">Tekan Enter atau klik Cari untuk mencari di semua field</p>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <!-- Gedung Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Gedung</label>
                    <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200 text-xs uppercase">
                            <span class="text-gray-700 truncate">
                                {{ empty($selectedGedung) ? '-- Semua Gedung --' : implode(', ', $selectedGedung) }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto text-xs uppercase">
                            <div class="space-y-2">
                                @foreach($gedungList as $gedung)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="gedung[]" value="{{ $gedung }}" {{ in_array($gedung, $selectedGedung, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span>{{ $gedung }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Ruangan Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Ruangan</label>
                   <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200 text-xs uppercase">
                            <span class="text-gray-700 truncate">
                                {{ empty($selectedRuangan) ? '-- Semua Ruangan --' : implode(', ', $selectedRuangan) }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto text-xs uppercase">
                            <div class="space-y-2">
                                @foreach($ruanganList as $ruangan)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="ruangan[]" value="{{ $ruangan }}" {{ in_array($ruangan, $selectedRuangan, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span>{{ $ruangan }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>
                <!-- Filter Type Distribution -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Tipe</label>
                        <select name="type" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option value="">Semua</option>
                            <option value="warehouse" {{ request('type')=='warehouse'?'selected':'' }}>gedung</option>
                            <option value="distribution" {{ request('type')=='distribution'?'selected':'' }}>distribusi</option>
                            <option value="maintenance" {{ request('type')=='maintenance'?'selected':'' }}>pemeliharaan</option>
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
                    <a href="{{ route('locations.index') }}" class="btn btn-secondary btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Bersihkan</span>
                    </a>
                </div>

            <!-- Active Filters Display -->
            @if(request('search') || !empty($selectedGedung) || !empty($selectedRuangan) || request('type'))
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
                                <a href="{{ route('locations.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if(!empty($selectedGedung))
                            @php
                                $gedungQuery = request()->query();
                                unset($gedungQuery['gedung']);
                            @endphp
                            <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                 <span>Gedung: <strong>{{ implode(', ', $selectedGedung) }}</strong></span>
                                <a href="{{ route('locations.index', $gedungQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if(!empty($selectedRuangan))
                            @php
                                $ruanganQuery = request()->query();
                                unset($ruanganQuery['ruangan']);
                            @endphp
                            <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Ruangan: <strong>{{ implode(', ', $selectedRuangan) }}</strong></span>
                                <a href="{{ route('locations.index', $ruanganQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if(request('type'))
                            @php
                                $typeQuery = request()->query();
                                unset($typeQuery['type']);
                            @endphp
                            <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Tipe: <strong>{{ $typeLabels[request('type')] ?? request('type') }}</strong></span>
                                <a href="{{ route('locations.index', $typeQuery) }}" class="hover:text-purple-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif

                        <!-- Clear All Button -->
                        <a href="{{ route('locations.index') }}" class="btn btn-soft-danger btn-sm">
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

    <!-- Daftar Locations -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Result Counter -->
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $locations->total() }}</span>
                <span>Data Lokasi Ditemukan</span>
                @if(request('search') || !empty($selectedGedung) || !empty($selectedRuangan) || request('type'))
                    <span class="text-gray-500">(dari total database)</span>
                @endif
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $locations->currentPage() }}</span> dari <span class="font-semibold">{{ $locations->lastPage() }}</span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Gedung</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Ruangan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tipe Lokasi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($locations as $index => $loc)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $locations->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 uppercase">
                                            {{ $loc->gedung }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 uppercase">
                                {{ $loc->ruangan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 uppercase">
                                {{ $loc->type ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('locations.edit', [$loc->id, 'redirect' => url()->full()]) }}" title="Edit" class="btn btn-warning btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('locations.destroy', [$loc->id, 'redirect' => url()->full()]) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus gudang ini?')">
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
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">
                                        @if(request('search') || !empty($selectedGedung) || !empty($selectedRuangan))
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
                {{ $locations->links() }}
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
    var $hidden = $('#location_id_hidden');
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
                url: '{{ route('locations.search_locations') }}',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                        return;
                    }
                    var html = '';
                    $.each(data, function(i, location) {
                        html += '<div class="px-3 py-2 cursor-pointer hover:bg-green-100" data-id="'+location.id+'" data-text="'+location.text+'">'+location.text+'</div>';
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
