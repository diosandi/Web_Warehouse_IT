@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Distribusi Barang</h1>
            <p class="text-gray-600 mt-1">Edit Input Barang Distribusi</p>
        </div>
        <a href="{{ route('distribution.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200">
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
        <form action="{{ route('distribution.update', $distribution->id) }}" method="POST" class="">
            @csrf
            @method('PUT')

            <!-- USER -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama User</label>
                <input type="text" name="nama_user" value="{{ $distribution->nama_user }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <!-- DIVISI -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                <input type="text" name="divisi" value="{{ $distribution->divisi }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- LOKASI -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi <span class="text-red-500">*</span></label>
                <!-- GEDUNG -->
                <select id="gedung" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <option value="">-- Pilih Gedung --</option>
                        @foreach($gedungList as $g)
                            <option value="{{ $g }}" 
                                {{ $distribution->location->gedung == $g ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                </select><br><br>
                    <!-- RUANGAN -->
                <select name="location_id" id="ruangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                    <option value="">-- Pilih Ruangan --</option>
                </select>
            </div>

            <!-- TANGGAL -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_distribusi" value="{{ $distribution->tanggal_distribusi }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
            </div>

            <hr class="my-6">

            <h2 class="font-bold mb-4">Pilih Device <span class="text-red-500">*</span></h2>

            <!-- PC -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">PC</label>
                <input type="text"
                    id="pc_search"
                    value="{{ $pc_selected->serial_number ?? '' }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN PC...">

                <input type="hidden" name="items[]" id="pc_id"
                    value="{{ $pc_selected->id ?? '' }}">

                <div id="pc_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
                
            </div>

            <!-- Monitor -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Monitor</label>
                <input type="text"
                    id="monitor_search"
                    value="{{ $monitor_selected->serial_number ?? '' }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Monitor...">

                <input type="hidden" name="items[]" id="monitor_id"
                    value="{{ $monitor_selected->id ?? '' }}">

                <div id="monitor_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
            </div>

            <!-- Printer Kertas -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Printer Kertas</label>
                <input type="text"
                    id="printer_kertas_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Printer Kertas...">

                <div id="printer_kertas_selected" class="mt-2 flex flex-wrap gap-2">
                    @foreach($printer_kertas_selected as $printer)
                        <input type="hidden" name="printer_kertas_ids[]" value="{{ $printer->id }}">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded flex items-center gap-1">
                            {{ $printer->serial_number }}
                            <button type="button" class="remove-item text-red-500" data-id="{{ $printer->id }}">×</button>
                        </span>
                    @endforeach
                </div>

                <div id="printer_kertas_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
            </div>

            <!-- Printer Barcode -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Printer Barcode</label>
                <input type="text"
                    id="printer_barcode_search"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Printer Barcode...">

                <div id="printer_barcode_selected" class="mt-2 flex flex-wrap gap-2">
                    @foreach($printer_barcode_selected as $printer)
                        <input type="hidden" name="printer_barcode_ids[]" value="{{ $printer->id }}">
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded flex items-center gap-1">
                            {{ $printer->serial_number }}
                            <button type="button" class="remove-item text-red-500" data-id="{{ $printer->id }}">×</button>
                        </span>
                    @endforeach
                </div>

                <div id="printer_barcode_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
            </div>

            <!-- Scanner -->
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Scanner</label>
               <input type="text"
                    id="scanner_search"
                    value="{{ $scanner_selected->serial_number ?? '' }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Ketik SN Scanner...">

                <input type="hidden" name="items[]" id="scanner_id"
                    value="{{ $scanner_selected->id ?? '' }}">

                <div id="scanner_suggestions"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
            </div>

            <!-- KETERANGAN -->
            <div class="mb-4">
                <label>Keterangan</label>
                <textarea name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ $distribution->keterangan }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update
                </button>

                <a href="{{ route('distribution.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>Batal
                </a>
            </div>

        </form>
    </div>

</div>
<script>
function setupSearch(inputId, suggestionId, hiddenId, kategori, containerId = null, inputName = null) {
    let $input = $(inputId);
    let $suggestions = $(suggestionId);
    let $hidden = $(hiddenId);
    let timeout = null;

    $input.on('input', function () {
        let query = $(this).val();
        $hidden.val('');

        clearTimeout(timeout);

        if (query.length < 1) {
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

    $suggestions.on('click', 'div', function () {
        let id = $(this).data('id');
        let text = $(this).data('text');

        // MULTI SELECT (printer)
        if (containerId && inputName) {
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
            // SINGLE SELECT (PC, Monitor, Scanner)
            $input.val(text);
            $hidden.val(id);
        }

        $suggestions.hide();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest(inputId + ', ' + suggestionId).length) {
            $suggestions.hide();
        }
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

    if (valid && ($('#printer_kertas_search').val() !== '' || $('#printer_barcode_search').val() !== '')) {
        alert('Harus pilih SN printer dari daftar, tidak boleh input manual!');
        valid = false;
    }

    if (!valid) e.preventDefault();
});

$(document).ready(function () {
    setupSearch('#pc_search', '#pc_suggestions', '#pc_id', 'PC');
    setupSearch('#monitor_search', '#monitor_suggestions', '#monitor_id', 'Monitor');
    setupSearch('#scanner_search', '#scanner_suggestions', '#scanner_id', 'Scanner');

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
});

$(document).on('click', '.remove-item', function () {
    let id = $(this).data('id');
    let $container = $(this).closest('div');

    $(this).parent().remove();
    $container.find(`input[value="${id}"]`).remove();
});

//Search ambil ruangan
$(document).ready(function() {

    let selectedGedung = $('#gedung').val();
    let selectedLocationId = "{{ $distribution->location_id }}";

    if (selectedGedung) {
        loadRuangan(selectedGedung, selectedLocationId);
    }

    $('#gedung').on('change', function() {
        let gedung = $(this).val();
        loadRuangan(gedung, null);
    });

    function loadRuangan(gedung, selectedId = null) {
        $.ajax({
            url: '/get-ruangan',
            data: { gedung: gedung },
            success: function(data) {

                let html = '<option value="">-- Pilih Ruangan --</option>';

                data.forEach(r => {
                    let selected = (r.id == selectedId) ? 'selected' : '';

                    html += `<option value="${r.id}" ${selected}>
                                ${r.ruangan}
                             </option>`;
                });

                $('#ruangan').html(html);
            }
        });
    }

});
</script>
@endsection
