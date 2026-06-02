@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Distribusi Barang</h1>
            <p class="text-gray-600 mt-1">Input Barang Distribusi</p>
        </div>
        <a href="{{ $redirect }}" class="btn btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.536-9.536a1 1 0 011.414-1.414L10 8.586l2.121-2.121a1 1 0 111.414 1.414L11.414 10l2.121 2.121a1 1 0 01-1.414 1.414L10 11.414l-2.121 2.121a1 1 0 01-1.414-1.414L8.586 10 6.464 7.879z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- FORM -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('distribution.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <!-- USER -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama User</label>
                <input type="text" name="nama_user" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                <input type="text" name="divisi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- LOKASI -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                    <!-- GEDUNG -->
                    <select id="gedung" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        <option value="">-- Pilih Gedung --</option>
                        @foreach($gedungList as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                    <br><br>
                    <!-- RUANGAN -->
                    <select name="location_id" id="ruangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                        <option value="">-- Pilih Ruangan --</option>
                    </select>
            </div>

            

            <!-- TANGGAL -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_distribusi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            </div>

            <hr class="my-6">

            <h2 class="font-bold text-lg mb-4">Pilih Device <span class="text-red-500">*</span></h2>

            <!-- PC -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">PC</label>
                <input type="text" id="pc_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN PC...">

                <input type="hidden" name="items[]" id="pc_id">

                <div id="pc_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

            <!-- Monitor -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Monitor</label>
                <input type="text" id="monitor_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Monitor...">

                <input type="hidden" name="items[]" id="monitor_id">

                <div id="monitor_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

            <!-- Printer Kertas -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Printer Kertas</label>
                <input type="text" id="printer_kertas_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Printer Kertas...">

               
                {{-- <input type="hidden" name="items[]" id="printer_kertas_id"> --}}

                <div id="printer_kertas_selected" class="mt-2 flex flex-wrap gap-2"></div>
                <div id="printer_kertas_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

            <!-- Printer Kertas Barcode -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Printer Barcode</label>
                <input type="text" id="printer_barcode_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Printer Barcode...">

                
                {{-- <input type="hidden" name="items[]" id="printer_barcode_id"> --}}

                <div id="printer_barcode_selected" class="mt-2 flex flex-wrap gap-2"></div>
                <div id="printer_barcode_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

            <!-- Scanner -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Scanner</label>
                <input type="text" id="scanner_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Scanner...">

                <input type="hidden" name="items[]" id="scanner_id">

                <div id="scanner_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

             <!-- Lainnya -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lainnya</label>
                <input type="text" id="lainnya_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Lainnya...">

                
                {{-- <input type="hidden" name="items[]" id="printer_barcode_id"> --}}

                <div id="lainnya_selected" class="mt-2 flex flex-wrap gap-2"></div>
                <div id="lainnya_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto">
                </div>
            </div>

            <!-- KETERANGAN -->
            <div class="mb-4">
                <label class="font-semibold">Keterangan</label>
                <textarea name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
            </div>

             <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button type="submit" class="btn btn-success">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan 
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
// Search Device tambah data barang
function setupSearch(inputId, suggestionId, hiddenId, kategori, containerId = null, inputName = null) {
    let $input = $(inputId);
    let $suggestions = $(suggestionId);
    let $hidden = $(hiddenId);
    let timeout = null;

    $input.on('input', function () {
        let query = $(this).val();
        $hidden.val('');

        clearTimeout(timeout);

        if (query.length < 3) {
            $suggestions.hide();
            return;
        }

        timeout = setTimeout(function () {
            $.ajax({
                url: '{{ route("distribution.search_items") }}',
                data: {
                    q: query,
                    kategori: kategori
                },
                success: function (data) {

                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada</div>').show();
                        return;
                    }

                    let html = '';
                    data.forEach(item => {
                        html += `
                        <div class="px-3 py-2 cursor-pointer hover:bg-green-100"
                            data-id="${item.id}"
                            data-text="${item.serial_number}">
                            ${item.serial_number} - ${item.merk}
                        </div>`;
                    });

                    $suggestions.html(html).show();
                }
            });
        }, 300);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest(inputId + ', ' + suggestionId).length) {
            $suggestions.hide();
        }
    });

    $suggestions.on('click', 'div', function () {
    let id = $(this).data('id');
    let text = $(this).data('text');

    // 👉 MULTI SELECT (printer)
    if (containerId && inputName) {

        // Cegah duplicate
        if ($(containerId + ' input[value="'+id+'"]').length) return;

        let input = `<input type="hidden" name="${inputName}[]" value="${id}">`;

        let badge = `
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded flex items-center gap-1">
                ${text}
                <button type="button" class="remove-item text-red-500" data-id="${id}">×</button>
            </span>
        `;

        $(containerId).append(input).append(badge);
        $input.val('');

    } else {
        // 👉 SINGLE SELECT (PC, Monitor, Scanner)
        $input.val(text);
        $hidden.val(id);
    }

    $suggestions.hide();
    });
}

$('form').on('submit', function(e) {

    let valid = true;

    // cek semua hidden input items[]
    $('input[name="items[]"]').each(function() {
        let visibleInput = $(this).prev('input[type="text"]');

        if (visibleInput.val() !== '' && $(this).val() === '') {
            alert('Harus pilih SN dari daftar, tidak boleh input manual!');
            valid = false;
            return false;
        }
    });

    if (!valid) e.preventDefault();
});

$(document).ready(function () {
    setupSearch('#pc_search', '#pc_suggestions', '#pc_id', 'PC');
    setupSearch('#monitor_search', '#monitor_suggestions', '#monitor_id', 'Monitor');
    setupSearch('#scanner_search', '#scanner_suggestions', '#scanner_id', 'Scanner');

     // MULTI
    setupSearch(
        '#printer_kertas_search',
        '#printer_kertas_suggestions',
        null,
        'Printer Kertas',
        '#printer_kertas_selected',
        'printer_kertas_ids'
    );

    setupSearch(
        '#printer_barcode_search',
        '#printer_barcode_suggestions',
        null,
        'Printer Barcode',
        '#printer_barcode_selected',
        'printer_barcode_ids'
    );

    setupSearch(
        '#lainnya_search',
        '#lainnya_suggestions',
        null,
        'Lainnya',
        '#lainnya_selected',
        'lainnya_ids'
    );
});

$(document).on('click', '.remove-item', function () {
    let id = $(this).data('id');
    let $container = $(this).closest('div');

    $(this).parent().remove(); // hapus badge
    $container.find(`input[value="${id}"]`).remove(); // hapus hidden
});

//Search ambil ruangan
$('#gedung').on('change', function() {
    let gedung = $(this).val();

    if (!gedung) {
    $('#ruangan').html('<option>Pilih gedung dulu</option>');
    return;
    }

    $('#ruangan').html('<option>Loading...</option>');

    $.ajax({
        url: '/get-ruangan',
        data: { gedung: gedung },
        success: function(data) {
            let html = '<option value="">-- Pilih Ruangan --</option>';

            data.forEach(r => {
                html += `<option value="${r.id}">${r.ruangan}</option>`;
            });

            $('#ruangan').html(html);
        }
    });
});
</script>
@endsection
