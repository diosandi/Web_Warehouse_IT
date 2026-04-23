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
            <a href="{{ route('locations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-6 py-2 md:py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 whitespace-nowrap text-xs md:text-base">
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
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update
                </button>
                <a 
                    href="{{ route('locations.index') }}" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2">
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
