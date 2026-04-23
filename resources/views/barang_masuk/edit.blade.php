@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Barang Masuk</h1>
            <p class="text-gray-600 mt-1">Input Edit Barang Masuk</p>
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 mb-4">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </div>
        <a href="{{ route('barang_masuk.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>
    <!-- From -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('barang_masuk.update', $barang_masuk->id) }}" method="POST">
        @csrf
        @method('PUT')
            <!-- Kategori -->
            <div class="mb-3 md:mb-4">
                <label for="kategori" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="kategori" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="PC" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'PC') ? 'selected' : '' }}>PC</option>
                    <option value="Monitor" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Monitor') ? 'selected' : '' }}>Monitor</option>
                    <option value="Printer kertas" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Printer kertas') ? 'selected' : '' }}>Printer kertas</option>
                    <option value="Printer barcode" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Printer barcode') ? 'selected' : '' }}>Printer barcode</option>
                    <option value="Scanner" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Scanner') ? 'selected' : '' }}>Scanner</option>
                </select>
            </div>

            <!-- Merk -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Merk</label>
                <input type="text" name="merk" value="{{ old('merk', $barang_masuk->items->first()->merk ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Supplier -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Supplier</label>
                <input type="text" name="supplier" value="{{ old('supplier', $barang_masuk->supplier ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Tanggal -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $barang_masuk->tanggal_masuk ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"required>
            </div>

            <!-- SERIAL NUMBER -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Serial Number</label>

                <div id="sn-wrapper">

                    {{-- PRIORITAS: old input --}}
                    @if(old('serial_numbers'))
                        @foreach (old('serial_numbers') as $sn)
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="serial_numbers[]" value="{{ $sn }}"
                                    class="w-full px-3 py-2 border rounded-lg">
                                <button type="button" onclick="removeSN(this)" 
                                    class="bg-red-500 text-white px-3 rounded">✕</button>
                            </div>
                        @endforeach

                    {{-- DATA DARI DATABASE --}}
                    @elseif(isset($items) && count($items))
                        @foreach ($items as $item)
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="serial_numbers[]" value="{{ $item->serial_number }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <button type="button" onclick="removeSN(this)" 
                                    class="bg-red-500 text-white px-3 rounded">✕</button>
                            </div>
                        @endforeach

                    {{-- DEFAULT --}}
                    @else
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="serial_numbers[]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <button type="button" onclick="removeSN(this)" 
                                class="bg-red-500 text-white px-3 rounded">✕</button>
                        </div>
                    @endif

                </div>

                <button type="button" onclick="addSN()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 mt-2 rounded-lg font-semibold transition duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    + Tambah SN
                </button>
            </div>

            <!-- Keterangan -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Keterangan</label>
                <textarea name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('keterangan', $barang_masuk->keterangan ?? '') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </button>

                <a href="{{ route('barang_masuk.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>Batal
                </a>
            </div>
        </form>
    </div>

</div>
<script>
function addSN() {
    let html = `
    <div class="flex gap-2 mb-2">
        <input type="text" name="serial_numbers[]" 
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
        <button type="button" onclick="removeSN(this)" 
            class="bg-red-500 text-white px-3 rounded">✕</button>
    </div>
    `;
    document.getElementById('sn-wrapper').insertAdjacentHTML('beforeend', html);
}

function removeSN(button) {
    button.parentElement.remove();
}
</script>

@endsection
