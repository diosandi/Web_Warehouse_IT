@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">Detail Master Data Barang</h1>
            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400 mt-1">Input data perangkat IT baru</p>
        </div>
        <a href="{{ $redirect }}" class="btn bg-gray-500 hover:bg-gray-600">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden sm:inline">Kembali</span>
            <span class="sm:hidden">Kembali</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-3 md:p-6 lg:p-8">

            <!-- 3-Column Grid for Lg, 1-Column otherwise -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <!-- COLUMN 1: IDENTIFIKASI -->
                <div class="lg:border-l-4 border-blue-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 dark:text-gray-100 dark:bg-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-blue-200">📋 Identifikasi</h3>

                    <!-- Kategori -->
                    <div class="mb-3 md:mb-4">
                        <label for="kategori" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Kategori</label>
                        <input type="text" value="{{$item->kategori ?? '-'}}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>

                    <!-- Merk -->
                    <div class="mb-3 md:mb-4">
                        <label for="merk" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Merk</label>
                        <input type="text" name="merk" id="merk" value="{{$item->merk ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>

                    <!-- Type -->
                    <div class="mb-3 md:mb-4">
                        <label for="type" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Type/Series</label>
                        <input type="text" name="type" id="type" value="{{$item->type ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>

                    <!-- Serial Number -->
                    <div class="mb-3 md:mb-4">
                        <label for="serial_number" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Serial Number (S/N)</label>
                        <input type="text" name="serial_number" id="serial_number" value="{{ $item->serial_number ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>

                    </div>

                    <!-- Service Tag -->
                    <div class="mb-0">
                        <label for="service_tag" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Service Tag </label>
                        <input type="text" name="service_tag" id="service_tag" value="{{ $item->service_tag ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('service_tag')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- COLUMN 2: SPESIFIKASI TEKNIS -->
                <div class="lg:border-l-4 border-purple-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 dark:text-gray-100 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-purple-200">⚙️ Spesifikasi</h3>

                    <!-- Processor -->
                    <div class="mb-3 md:mb-4">
                        <label for="processor" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Processor (CPU)</label>
                        <input type="text" name="processor" id="processor" value="{{ $item->processor ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('processor')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RAM -->
                    <div class="mb-3 md:mb-4">
                        <label for="ram_gb" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">RAM (GB)</label>
                        <input type="number" name="ram_gb" id="ram_gb" value="{{ $item->ram_gb }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('ram_gb')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Storage -->
                    <div class="mb-3 md:mb-4">
                        <label for="storage_gb" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Storage (GB)</label>
                        <input type="number" name="storage_gb" id="storage_gb" value="{{ $item->storage_gb }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('storage_gb')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VGA -->
                    <div class="mb-3 md:mb-4">
                        <label for="vga" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">VGA/GPU</label>
                        <input type="text" name="vga" id="vga" value="{{ $item->vga ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('vga')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- OS -->
                    <div class="mb-0">
                        <label for="os" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Operating System</label>
                        <input type="text" name="os" id="os" value="{{ $item->os ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('os')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- COLUMN 3: ADMINISTRATIF -->
                <div class="lg:border-l-4 border-green-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 dark:text-gray-100 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-green-200">📊 Administratif</h3>

                    <!-- Asset -->
                    <div class="mb-3 md:mb-4">
                        <label for="asset" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Asset/Kepemilikan</label>
                        <input type="text" name="asset" id="asset" value="{{ $item->asset ?? '-' }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                    </div>

                    <!-- Tahun -->
                    <div class="mb-3 md:mb-4">
                        <label for="tahun" class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">Tahun</label>
                        <input type="number" name="tahun" id="tahun" value="{{ $item->tahun }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                        @error('tahun')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Lokasi Penyimpanan -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">
                            Lokasi Penyimpanan
                        </label>
                        <select
                            name="storage_location_id"
                            class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>
                            <option value="">
                                -- Pilih Lokasi --
                            </option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" class="text-sm uppercase" {{ $item->storage_location_id == $location->id ? 'selected' : '' }}>
                                    {{ $location->gedung }}
                                    -
                                    {{ $location->ruangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Keterangan -->
                    <div class="mb-3 md:mb-4">
                        <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">
                            Status
                        </label>

                        <select name="status" id="status" {{ $item->status == 'used' ? 'disabled' : '' }}
                            class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled>

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

                            <option value="vendor"
                                {{ old('status', $item->status) == 'vendor' ? 'selected' : '' }}>
                                Dibawa Vendor
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
                        <label class="block text-xs md:text-sm font-medium text-gray-700 dark:text-gray-100 mb-1 md:mb-2">
                            Keterangan Kondisi
                        </label>

                        <textarea
                            name="condition_note"
                            id="condition_note"
                            rows="3"
                            class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed" disabled
                            placeholder="Contoh: LCD rusak, motherboard mati, dll"
                        >{{ old('condition_note', $item->condition_note) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <a href="{{ $redirect }}" class="btn bg-gray-500 hover:bg-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
