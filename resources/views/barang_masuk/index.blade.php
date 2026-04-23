@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Barang Masuk</h1>
                <p class="text-gray-600 mt-1">Kelola Barang Masuk</p>
        </div>

        <a href="{{ route('barang_masuk.create') }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            Tambah 
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filter Section -->
     <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ request('search') ? 'open' : '' }}>
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
                <label for="search" class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">🔍 Cari Barang Masuk</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari user, SN, Merk" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
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
                        <select name="kategori" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10">
                            <option value="">Semua</option>
                            <option value="pc">PC</option>
                            <option value="monitor">Monitor</option>
                            <option value="printer_kertas">Printer Kertas</option>
                            <option value="printer_barcode">Printer Barcode</option>
                            <option value="scanner">Scanner</option>
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
            </div>
             <div class="flex items-end gap-2 md:gap-3 sm:col-span-2 lg:col-span-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2 text-sm md:text-base shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    <a href="{{ route('barang_masuk.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-2 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2 text-sm md:text-base shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                </div>
        </form>
        </div>
     </details>

     <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.536-9.536a1 1 0 011.414-1.414L10 8.586l2.121-2.121a1 1 0 111.414 1.414L11.414 10l2.121 2.121a1 1 0 01-1.414 1.414L10 11.414l-2.121 2.121a1 1 0 01-1.414-1.414L8.586 10 6.464 7.879z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!--DAFTAR BARANG MASUK -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Total Item</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($barang_masuk as $bm)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bm->tanggal_masuk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bm->items->first()->kategori ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bm->items->first()->merk ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bm->supplier ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $bm->items->count() ?? 0 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $bm->keterangan ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="inline-flex gap-2">
                                <!-- DETAIL -->
                                <a href="{{ route('barang_masuk.show', $bm->id) }}" title="Detail" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150  flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="1" cy="1" r="1" transform="matrix(1 0 0 -1 11 9)" fill="#1C274C"></circle>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17V11 M 7 3.33782 C 8.47087 2.48697 10.1786 2 12 2 C 17.5228 2 22 6.47715 22 12 C 22 17.5228 17.5228 22 12 22 C 6.47715 22 2 17.5228 2 12 C 2 10.1786 2.48697 8.47087 3.33782 7"></path>
                                    </svg>
                                </a>
                                <!-- EDIT -->
                                <a href="{{ route('barang_masuk.edit', $bm->id) }}" title="Edit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <!-- HAPUS -->
                                <form action="{{ route('barang_masuk.destroy', $bm->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button title="Hapus" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-xs font-semibold transition duration-150 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-3 md:px-4 py-8">
                                  <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm md:text-base text-gray-600 font-semibold mb-2">Belum ada barang masuk</p>
                                    <p class="text-xs md:text-sm text-gray-500 mb-4">silakan tambahkan data terlebih dahulu</p>
                                    <a href="{{ route('barang_masuk.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold text-xs md:text-sm transition duration-200">+ Tambah Device Detail</a>
                                  </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <!-- PAGINATION -->
            <div class="bg-white px-3 md:px-4 py-4 border-t border-gray-200 overflow-x-auto">
                <div class="flex justify-center md:justify-end">
                    @if($barang_masuk->hasPages()) {{ $barang_masuk->links() }} @endif
                </div>
            </div>
        </div>
    </div>  

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('search');

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
    });

    // filter tetap auto submit
    document.querySelectorAll('select, input[type="checkbox"]').forEach(el => {
        el.addEventListener('change', function () {
            this.form.submit();
        });
    });

});
</script>
@endsection