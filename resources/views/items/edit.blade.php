@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Master Data Barang</h1>
            <p class="text-xs md:text-sm text-gray-600 mt-1">Perbarui data perangkat IT</p>
        </div>
        <a href="{{ $redirect }}" class="btn btn-secondary">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden sm:inline">Kembali</span>
            <span class="sm:hidden">Kembali</span>
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.536-9.536a1 1 0 011.414-1.414L10 8.586l2.121-2.121a1 1 0 111.414 1.414L11.414 10l2.121 2.121a1 1 0 01-1.414 1.414L10 11.414l-2.121 2.121a1 1 0 01-1.414-1.414L8.586 10 6.464 7.879z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8">
        <form action="{{ route('items.update', $item->id) }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <!-- 3-Column Grid for Lg, 1-Column otherwise -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <!-- COLUMN 1: IDENTIFIKASI -->
                <div class="lg:border-l-4 border-blue-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-blue-200">📋 Identifikasi</h3>

                    <!-- Kategori -->
                    <div class="mb-3 md:mb-4">
                        <label for="kategori" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Kategori <span class="text-red-500">*</span></label>
                         @if($item->barang_masuk_id)
                            <input type="text" value="{{ $item->kategori }}" disabled class="bg-gray-100 cursor-not-allowed w-full px-3 py-2 border rounded-lg">
                            <input type="hidden" name="kategori" value="{{ $item->kategori }}">
                                <p class="text-sm text-red-500 mt-1">
                                    ⚠️ Kategori tidak bisa diubah karena berasal dari Barang Masuk
                                </p>
                        @else
                        <select name="kategori" id="kategori" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriOptions as $value => $label)
                                <option value="{{ $value }}" {{ $item->kategori == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @endif
                        @error('kategori')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merk -->
                    <div class="mb-3 md:mb-4">
                        <label for="merk" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Merk <span class="text-red-500">*</span></label>
                        @if($item->barang_masuk_id)
                            <input type="text" value="{{ $item->merk }}" disabled class="bg-gray-100 cursor-not-allowed w-full px-3 py-2 border rounded-lg">
                            <input type="hidden" name="merk" value="{{ $item->merk }}">
                                <p class="text-sm text-red-500 mt-1">
                                    ⚠️ Merk tidak bisa diubah karena berasal dari Barang Masuk
                                </p>
                        @else
                            <input type="text" name="merk" id="merk" value="{{ old('merk', $item->merk) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="ASUS, Dell, HP..." required>
                        @endif
                            @error('merk')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                    </div>

                    <!-- Type -->
                    <div class="mb-3 md:mb-4">
                        <label for="type" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Tipe/Series <span class="text-red-500">*</span></label>
                        @if($item->barang_masuk_id)
                            <input type="text" value="{{ $item->type}}" disabled class="bg-gray-100 cursor-not-allowed w-full px-3 py-2 border rounded-lg">
                            <input type="hidden" name="type" value="{{ $item->type }}">
                                <p class="text-sm text-red-500 mt-1">
                                    ⚠️ Tipe tidak bisa diubah karena berasal dari Barang Masuk
                                </p>
                        @else
                            <input type="text" name="type" id="type" value="{{ old('type', $item->type) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Model name" required>
                        @endif
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serial Number -->
                    <div class="mb-3 md:mb-4">
                        <label for="serial_number" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Serial Number (S/N) <span class="text-yellow-500">( jika tidak ada : - )</span> <span class="text-red-500">*</span></label>
                        <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $item->serial_number) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="SN123456..." required>
                        @error('serial_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Tag -->
                    <div class="mb-0">
                        <label for="service_tag" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Service Tag </label>
                        <input type="text" name="service_tag" id="service_tag" value="{{ old('service_tag', $item->service_tag) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Optional">
                        @error('service_tag')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- COLUMN 2: SPESIFIKASI TEKNIS -->
                <div class="lg:border-l-4 border-purple-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-purple-200">⚙️ Spesifikasi</h3>

                    <!-- Processor -->
                    <div class="mb-3 md:mb-4">
                        <label for="processor" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Processor (CPU)</label>
                        <input type="text" name="processor" id="processor" value="{{ old('processor', $item->processor) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="i7, Ryzen 5...">
                        @error('processor')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RAM -->
                    <div class="mb-3 md:mb-4">
                        <label for="ram_gb" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">RAM (GB)</label>
                        <input type="number" name="ram_gb" id="ram_gb" value="{{ old('ram_gb', $item->ram_gb) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="16" min="1">
                        @error('ram_gb')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Storage -->
                    <div class="mb-3 md:mb-4">
                        <label for="storage_gb" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Storage (GB)</label>
                        <input type="number" name="storage_gb" id="storage_gb" value="{{ old('storage_gb', $item->storage_gb) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="512" min="1">
                        @error('storage_gb')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VGA -->
                    <div class="mb-3 md:mb-4">
                        <label for="vga" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">VGA/GPU</label>
                        <input type="text" name="vga" id="vga" value="{{ old('vga', $item->vga) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="RTX 3060...">
                        @error('vga')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- OS -->
                    <div class="mb-0">
                        <label for="os" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Operating System</label>
                        <input type="text" name="os" id="os" value="{{ old('os', $item->os) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Windows 11...">
                        @error('os')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- COLUMN 3: ADMINISTRATIF -->
                <div class="lg:border-l-4 border-green-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-green-200">📊 Administratif</h3>

                    <!-- Tahun -->
                    <div class="mb-3 md:mb-4">
                        <label for="tahun" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Tahun</label>
                        <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $item->tahun) }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="2024" min="2000" max="2099">
                        @error('tahun')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($item->status != 'used')
                        <div class="mb-3 md:mb-4">
                            <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Lokasi Penyimpanan</label>
                                <select
                                    name="storage_location_id"
                                    class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">

                                    <option value="">
                                        -- Pilih Lokasi --
                                    </option>

                                    @foreach($locations as $location)

                                        <option
                                            value="{{ $location->id }}"
                                            {{ old('storage_location_id', $item->storage_location_id) == $location->id ? 'selected' : '' }}>

                                            {{ $location->gedung }}
                                            -
                                            {{ $location->ruangan }}

                                        </option>

                                    @endforeach

                                </select>
                        </div>
                    @endif

                    <div class="mb-3 md:mb-4">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">
                            Status
                        </label>

                        <select name="status" id="status" {{ $item->status == 'used' ? 'disabled' : '' }}
                            class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">

                            <option value="available"
                                {{ old('status', $item->status) == 'available' ? 'selected' : '' }}>
                                Tersedia
                            </option>

                            <option value="maintenance"
                                {{ old('status', $item->status) == 'maintenance' ? 'selected' : '' }}>
                                Pemeliharaan
                            </option>

                            <option value="retired"
                                {{ old('status', $item->status) == 'retired' ? 'selected' : '' }}>
                                Tidak Digunakan
                            </option>
                        </select>

                        @if($item->status == 'used')
                            <input type="hidden" name="status" value="used">
                        @endif

                        {{-- Info --}}
                        @if($item->status == 'used')
                            <p class="text-red-500 text-sm mt-1">
                                Barang sedang dipakai, status hanya bisa diubah dari distribution.
                            </p>
                        @endif
                    </div>

                    <div class="mb-0">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">
                            Keterangan Kondisi
                        </label>

                        <textarea
                            name="condition_note"
                            id="condition_note"
                            rows="3"
                            class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Contoh: LCD rusak, motherboard mati, dll"
                        >{{ old('condition_note', $item->condition_note) }}</textarea>
                    </div>

                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button type="submit" class="btn btn-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui
                </button>

                <a href="{{ $redirect }}" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>Batal</a>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const statusSelect = document.getElementById('status');
    const noteField = document.getElementById('condition_note');

    function toggleConditionNote() {

        const status = statusSelect.value;

        // note aktif hanya untuk maintenance/retired
        if (status === 'maintenance' || status === 'retired') {

            noteField.disabled = false;
            noteField.classList.remove('bg-gray-100');

        } else {

            noteField.disabled = true;
            noteField.classList.add('bg-gray-100');

            // optional kosongkan note
            noteField.value = '';
        }
    }

    toggleConditionNote();

    statusSelect.addEventListener('change', toggleConditionNote);

});
</script>
@endsection
