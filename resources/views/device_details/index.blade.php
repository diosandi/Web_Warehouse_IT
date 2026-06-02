@extends('layouts.app')

@section('content')
@php
    $hasActiveFilter = request()->filled('search') || request()->filled('merk');
@endphp
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Perangkat</h1>
            <p class="text-gray-600 mt-1">Kelola detail perangkat untuk item berserial number</p>
        </div>
        {{-- <a href="{{ route('device_details.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah
        </a> --}}
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

     <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Filter & Cari Detail Perangkat</h2>
            </div>
            <svg class="w-5 h-5 text-gray-500 transition duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </summary>
        <div class="px-4 md:px-6 pb-4 md:pb-6">
        <form action="{{ route('device_details.index') }}" method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div>
                <div class="relative">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">🔍 Cari Detail Perangkat <span class="text-red-500">*</span></label>
                    <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-xs uppercase" placeholder="Cari nama SN atau IP">
                    <input type="hidden" name="item_id" id="item_id_hidden">
                    <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    @error('item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <p class="text-xs text-gray-500 mt-1">Tekan Enter atau klik Cari untuk mencari di semua field</p>
            </div>


            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <!-- Kategori Filter -->
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Merk</label>
                        <select name="merk" class="w-full px-3 md:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10 text-xs uppercase">
                            <option class="text-xs uppercase" value="">Semua</option>
                             @foreach($merks as $merk)
                                <option class="text-xs uppercase" value="{{ $merk }}" {{ request('merk') == $merk ? 'selected' : '' }}>
                                    {{ $merk }}
                                </option>
                            @endforeach
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
                    <a href="{{ route('device_details.index') }}" class="btn btn-secondary btn-block">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Bersihkan</span>
                    </a>
                </div>
                
                <!-- Active Filters Display -->
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
                                    <a href="{{ route('device_details.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif
                            @if(request('merk'))
                                @php
                                    $merkQuery = request()->query();
                                    unset($merkQuery['merk']);
                                @endphp
                                <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2 text-xs md:text-sm">
                                    <span>Merk: <strong>{{ request('merk') }}</strong></span>
                                    <a href="{{ route('device_details.index', $merkQuery) }}" class="hover:text-purple-900 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif

                            <!-- Clear All Button -->
                            <a href="{{ route('device_details.index') }}" class="btn btn-soft-danger btn-sm">
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


    <!-- Daftar Device Details -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

         <!-- Result Counter -->
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $deviceDetails->total() }}</span>
                <span>Data Detail Perangkat Ditemukan</span>
                @if($hasActiveFilter)
                    <span class="text-gray-500">(dari total database)</span>
                @endif
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $deviceDetails->currentPage() }}</span> dari <span class="font-semibold">{{ $deviceDetails->lastPage() }}</span>
            </div>
        </div>

        <!--Table-->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama PC</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Akun Pengguna</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Serial Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Jenis Koneksi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Nama Sharing</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">OS Version</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($deviceDetails as $index => $deviceDetail)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-900">{{ $deviceDetails->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">
                                 @if(optional($deviceDetail->item)->kategori === 'PC')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">PC</span>
                                @elseif(optional($deviceDetail->item)->kategori === 'Monitor')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Monitor</span>
                                @elseif(optional($deviceDetail->item)->kategori === 'Printer Kertas')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">P.Kertas</span>
                                @elseif(optional($deviceDetail->item)->kategori === 'Printer Barcode')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">P.Barcode</span>
                                @elseif(optional($deviceDetail->item)->kategori === 'Scanner')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Scanner</span>
                                @elseif(optional($deviceDetail->item)->kategori === 'Lainnya')
                                    <span class="px-2 md:px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">Lainnya</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->pc_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->user_account ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase font-semibold text-gray-900"><a href="{{ route('items.show', [$deviceDetail->item, 'redirect' => url()->full()]) }}"
                                                                                                                                                class="text-green-600 hover:text-green-800 hover:underline font-semibold">

                                                                                                                                                {{ optional($deviceDetail->item)->serial_number ?? '-' }}

                                                                                                                                            </a></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ optional($deviceDetail->item)->merk ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->connection_type ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->shared_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm uppercase text-gray-700">{{ $deviceDetail->os_version ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="inline-flex gap-2">
                                    <!-- Tombol Edit -->
                                    <a href="{{ route('device_details.edit', [$deviceDetail, 'redirect' => url()->full()]) }}" title="Edit" class="btn btn-warning btn-icon">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('device_details.destroy', [$deviceDetail, 'redirect' => url()->full()]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus detail perangkat ini?');">
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
                            <td colspan="10" class="px-3 md:px-4 py-8">
                                  <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">
                                        @if(request()->filled('search') || request()->filled('merk'))
                                            Tidak ada hasil yang cocok
                                        @else
                                            Belum ada data master barang
                                        @endif
                                    </p>
                                    {{-- <p class="text-xs md:text-sm text-gray-500 mb-4">silakan tambahkan data terlebih dahulu</p>
                                    <a href="{{ route('device_details.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-xs md:text-sm transition duration-200">+ Tambah Device Detail</a> --}}
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
                {{ $deviceDetails->links() }}
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    var $input = $('#search');
    var $suggestions = $('#suggestions');
    var $hidden = $('#item_id_hidden');

    $input.on('input', function() {
        var query = $(this).val();
        $hidden.val(''); // reset hidden
        if (query.length < 3) {
            $suggestions.empty().hide();
            return;
        }
        $.ajax({
            url: '{{ route('device_details.search_device_details') }}',
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
                            data-text="${item.text}">
                            ${item.text}
                        </div>
                    `;
                });
                $suggestions.html(html).show();
            }
        });
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
