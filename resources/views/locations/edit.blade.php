@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Lokasi</h1>
                <p class="text-xs md:text-sm text-gray-600 mt-1">Edit Data Lokasi : <strong>{{$location->gedung}}</strong></p>
            </div>
            <a href="{{ $redirect }}" class="btn btn-secondary">
                <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="hidden sm:inline">Kembali</span>
                <span class="sm:hidden">Kembali</span>
            </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8">
        <form action="{{ route('locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <!-- Type Lokasi -->
            <div class="mb-6">
                <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                    Jenis Lokasi <span class="text-red-500">*</span>
                </label>
                <select
                    name="type"
                    id="type"
                    class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        <option value="">-- Pilih Kategori --</option>
                            @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" {{ $location->type == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                </select>
                        @error('kategori')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
            </div>

            <!-- Nama Gedung -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Gedung <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="gedung"
                    id="gedung"
                    value="{{ old('gedung', $location->gedung) }}"
                    class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                    @error('gedung') border-red-500 @enderror"
                    placeholder="Contoh : CMU 1"
                    required
                >
                @error('gedung')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi -->
            <div class="mb-6">
                <label for="ruangan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Ruangan
                </label>
                <input
                    type="text"
                    name="ruangan"
                    id="ruangan"
                    value="{{ old('ruangan', $location->ruangan) }}"
                    class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('ruangan') border-red-500 @enderror"
                    placeholder="Contoh: Lt 1 IT"
                >
                @error('ruangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Aksi -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button
                    type="submit"
                    class="btn btn-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui
                </button>
                <a
                    href="{{ $redirect }}"
                    class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
