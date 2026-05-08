@extends('layouts.app')

@section('content')
@php
    $selectedKategori = $filters['kategori'] ?? [];
    $selectedMerk = $filters['merk'] ?? [];
@endphp
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Master Data Barang</h1>
            <p class="text-sm md:text-base text-gray-600 mt-1">Kelola data perangkat IT (PC, Monitor, Printer, Scanner)</p>
        </div>
        <a href="{{ route('items.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 shadow-lg hover:shadow-xl whitespace-nowrap text-sm md:text-base">
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
    <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ request('search') || !empty($selectedKategori) || !empty($selectedMerk) ? 'open' : '' }}>
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
                    <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}" placeholder="Cari S/N, Service Tag, Merk, Type, Processor, OS, PO..." class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
                    <input type="hidden" name='item_id' id="item_id_hidden">
                    <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
                    @if(request('search'))
                        <span class="absolute right-3 top-3 text-gray-400 text-sm font-semibold">{{ strlen(request('search')) }} char</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Tekan Enter atau klik Cari untuk mencari di semua field</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <!-- Kategori Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <details class="relative filter-dropdown">
                        <summary class="filter-summary list-none w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg bg-white cursor-pointer flex items-center justify-between gap-3 transition duration-200">
                            <span class="text-gray-700 truncate">
                                {{ empty($selectedKategori) ? '-- Semua Kategori --' : collect($selectedKategori)->map(fn ($kategori) => $kategoriOptions[$kategori] ?? $kategori)->implode(', ') }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($kategoriOptions as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="kategori[]" value="{{ $value }}" {{ in_array($value, $selectedKategori, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span>{{ $label }}</span>
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
                            <span class="text-gray-700 truncate">
                                {{ empty($selectedMerk) ? '-- Semua Merk --' : implode(', ', $selectedMerk) }}
                            </span>
                            <span class="text-gray-400 text-xs">Pilih</span>
                        </summary>
                        <div class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-h-64 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($merkList as $merk)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                        <input type="checkbox" name="merk[]" value="{{ $merk }}" {{ in_array($merk, $selectedMerk, true) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span>{{ $merk }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </details>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
                            <option value="">Semua</option>
                            <option value="used" {{ request('status')=='used'?'selected':'' }}>Digunakan</option>
                            <option value="available" {{ request('status')=='available'?'selected':'' }}>Tersedia</option>
                        </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-end gap-2 md:gap-3 sm:col-span-2 lg:col-span-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2 text-sm md:text-base shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    <a href="{{ route('items.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2 text-sm md:text-base shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk))
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
                                <a href="{{ route('items.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if(!empty($selectedKategori))
                            @php
                                $kategoriQuery = request()->query();
                                unset($kategoriQuery['kategori']);
                            @endphp
                            <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Kategori: <strong>{{ collect($selectedKategori)->map(fn ($kategori) => $kategoriOptions[$kategori] ?? $kategori)->implode(', ') }}</strong></span>
                                <a href="{{ route('items.index', $kategoriQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if(!empty($selectedMerk))
                            @php
                                $merkQuery = request()->query();
                                unset($merkQuery['merk']);
                            @endphp
                            <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                <span>Merk: <strong>{{ implode(', ', $selectedMerk) }}</strong></span>
                                <a href="{{ route('items.index', $merkQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif


                        <!-- Clear All Button -->
                        <a href="{{ route('items.index') }}" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-full inline-flex items-center gap-1 text-xs md:text-sm font-semibold transition duration-200">
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

    <!-- Daftar Master Data Barang -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Result Counter -->
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $items->total() }}</span>
                <span>Data Item Ditemukan</span>
                @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk))
                    <span class="text-gray-500">(dari total database)</span>
                @endif
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $items->currentPage() }}</span> dari <span class="font-semibold">{{ $items->lastPage() }}</span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-max divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Merk</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Type</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">S/N</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Service Tag</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Processor</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">RAM</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Tahun</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-3 md:px-4 py-3 md:py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-gray-50 transition duration-150">
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
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->merk ? Str::limit($item->merk, 10) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->type ? Str::limit($item->type) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm font-mono text-gray-700"><a href="{{ route('items.show', $item->id) }}"
                                                                                                                                                class="text-blue-600 hover:underline font-semibold">

                                                                                                                                                {{ $item->serial_number }}

                                                                                                                                            </a></td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm font-mono text-gray-700 hidden lg:table-cell">{{ $item->service_tag ? Str::limit($item->service_tag, 8) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->processor ? Str::limit($item->processor, 8) : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700">{{ $item->ram_gb ? $item->ram_gb . 'G' : '-' }}</td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-xs uppercase md:text-sm text-gray-700 hidden md:table-cell">{{ $item->tahun ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                               @if(in_array($item->kategori, ['Printer Kertas', 'Printer Barcode']))
                                    @if($item->isUsed())
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-800">Digunakan</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-800">Tersedia</span>
                                    @endif

                                @else

                                    {{-- selain printer --}}
                                    @if($item->status == 'used')
                                        <span class="px-2 py-1 rounded bg-red-100 text-red-800">Digunakan</span>
                                    @else
                                        <span class="px-2 py-1 rounded bg-green-100 text-green-800">Tersedia</span>
                                    @endif

                                @endif
                            </td>
                            <td class="px-3 md:px-4 py-3 md:py-4 whitespace-nowrap text-center text-xs font-medium">
                                <div class="flex gap-1 md:gap-2 justify-center flex-wrap">

                                    <!-- Tombol Edit -->
                                    <a href="{{ route('items.edit', $item->id) }}" title="Edit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150 flex items-center gap-1">
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
                            <td colspan="10" class="px-3 md:px-4 py-8">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">
                                        @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk))
                                            Tidak ada hasil yang cocok
                                        @else
                                            Belum ada data master barang
                                        @endif
                                    </p>
                                    <p class="text-xs md:text-sm text-gray-500 mb-4">
                                        @if(request('search') || !empty($selectedKategori) || !empty($selectedMerk))
                                            Coba ubah filter atau pencarian Anda
                                        @else
                                            Silakan tambahkan data perangkat terlebih dahulu
                                        @endif
                                    </p>
                                    <a href="{{ route('items.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-xs md:text-sm transition duration-200">+ Tambah Barang</a>
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

        if (query.length < 1) {
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

$('select[name="status"]').on('change', function() {
    $(this).closest('form').submit();
});

</script>
@endsection
