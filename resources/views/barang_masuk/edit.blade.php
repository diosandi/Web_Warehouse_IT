@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Barang Masuk</h1>
            <p class="text-gray-600 mt-1">Input Edit Barang Masuk</p>
        </div>
        <a href="{{ $redirect }}" class="btn btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Error Message  -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.536-9.536a1 1 0 011.414-1.414L10 8.586l2.121-2.121a1 1 0 111.414 1.414L11.414 10l2.121 2.121a1 1 0 01-1.414 1.414L10 11.414l-2.121 2.121a1 1 0 01-1.414-1.414L8.586 10 6.464 7.879z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            <ul class="list-disc pl-5 text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- From -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('barang_masuk.update', $barang_masuk->id) }}" method="POST">
        @csrf
        @method('PUT')
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <!-- Kategori -->
            <div class="mb-3 md:mb-4">
                <label for="kategori" class="block text-xs md:text-sm font-medium text-gray-700 mb-1 md:mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="kategori" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="PC" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'PC') ? 'selected' : '' }}>PC</option>
                    <option value="Monitor" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Monitor') ? 'selected' : '' }}>Monitor</option>
                    <option value="Printer Kertas" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Printer Kertas') ? 'selected' : '' }}>Printer kertas</option>
                    <option value="Printer Barcode" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Printer Barcode') ? 'selected' : '' }}>Printer barcode</option>
                    <option value="Scanner" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Scanner') ? 'selected' : '' }}>Scanner</option>
                    <option value="Lainnya" {{ (old('kategori', $barang_masuk->items->first()->kategori ?? '') == 'Lainnya') ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Merk -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Merk <span class="text-red-500">*</span></label>
                <input type="text" name="merk" value="{{ old('merk', $barang_masuk->items->first()->merk ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            </div>

            <!-- Type -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Tipe/Series <span class="text-red-500">*</span></label>
                <input type="text" name="type" value="{{ old('type', $barang_masuk->items->first()->type ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            </div>

            <!-- Asset -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Asset</label>
                <input type="text" name="supplier" value="{{ old('supplier', $barang_masuk->supplier ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- PO Number -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Nomor PO</label>
                <input type="text" name="po_number" value="{{ old('po_number', $barang_masuk->po_number ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Tanggal -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Tanggal Masuk <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $barang_masuk->tanggal_masuk ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            </div>

            <!-- SERIAL NUMBER -->
            <div class="block text-sm font-medium text-gray-700 mb-2">
                <label>Serial Number <span class="text-red-500">*</span></label>

                <div id="sn-wrapper">

                    {{-- PRIORITAS: old input --}}
                    @if(old('serial_numbers'))
                        @foreach (old('serial_numbers') as $index => $sn)
                            <div class="flex gap-2 mb-2">
                                <input type="hidden" name="item_ids[]" value="{{ old('item_ids.' . $index) }}">
                                <input type="text" name="serial_numbers[]" value="{{ $sn }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <button type="button" onclick="removeSN(this)"
                                    class="btn btn-danger btn-icon">✕</button>
                            </div>
                        @endforeach

                    {{-- DATA DARI DATABASE --}}
                    @elseif(isset($items) && count($items))
                    @foreach ($items as $item)
                            <div class="flex gap-2 mb-2">

                                {{-- ID ITEM --}}
                                <input type="hidden" name="item_ids[]" value="{{ $item->id }}">

                                {{-- SERIAL NUMBER --}}
                                <input type="text"
                                    name="serial_numbers[]"
                                    value="{{ $item->serial_number }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>

                                <button type="button"
                                    onclick="removeSN(this)"
                                    class="btn btn-danger btn-icon">
                                    ✕
                                </button>

                            </div>
                        @endforeach
                        {{-- @foreach ($items as $item)
                            <div class="flex gap-2 mb-2">
                                <input type="text" name="serial_numbers[]" value="{{ $item->serial_number }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <button type="button" onclick="removeSN(this)"
                                    class="btn btn-danger btn-icon">✕</button>
                            </div>
                        @endforeach --}}

                    {{-- DEFAULT --}}
                    @else
                        <div class="flex gap-2 mb-2">
                            <input type="text" name="serial_numbers[]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                            <button type="button" onclick="removeSN(this)"
                                class="btn btn-danger btn-icon">✕</button>
                        </div>
                    @endif

                </div>

                <button type="button" onclick="addSN()"
                    class="btn btn-success btn-sm mt-2">
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
                <button type="submit" class="btn btn-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui
                </button>

                <a href="{{ $redirect }}" class="btn btn-secondary">
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
            class="btn btn-danger btn-icon">✕</button>
    </div>
    `;
    document.getElementById('sn-wrapper').insertAdjacentHTML('beforeend', html);
}

function removeSN(button) {
    button.parentElement.remove();
}

document.querySelector("form").addEventListener("submit", function(e) {
    let inputs = document.querySelectorAll("input[name='serial_numbers[]']");
    let filled = 0;

    inputs.forEach(input => {
        if (input.value.trim() !== "") {
            filled++;
        }
    });

    // if (filled !== inputs.length) {
    //     e.preventDefault();
    //     alert("Semua Serial Number wajib diisi!");
    // }
});
</script>

@endsection
