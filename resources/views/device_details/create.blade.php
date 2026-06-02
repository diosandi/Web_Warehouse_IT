@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Detail Perangkat Baru</h1>
            <p class="text-gray-600 mt-1">Input item spesifik dengan serial number</p>
        </div>
        <a href="{{ $redirect }}" class="btn btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8">
        <form action="{{ route('device_details.store') }}" method="POST">
            @csrf

            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                <div class="lg:border-l-4 border-blue-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-blue-200">📋 Identifikasi</h3>
                        <!-- item SN autocomplete -->
                        <div class="relative mb-3 md:mb-4">
                            <label for="sn_search" class="block text-sm font-medium text-gray-700  mb-1 md:mb-2">Serial Number <span class="text-red-500">*</span></label>
                            <input type="text" value="{{ $item->serial_number }}" name="sn_search" id="sn_search" autocomplete="off" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Ketik Serial Number..." readonly required>
                            <input type="hidden" name="item_id" id="item_id_hidden" value="{{ $item->id }}">
                            <div id="sn_suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
                            @error('item_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- PC Name -->
                        <div class="mb-3 md:mb-4">
                            <label for="pc_name" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Nama PC </label>
                            <input type="text" name="pc_name" id="pc_name" value="{{ old('pc_name') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('pc_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- User Account -->
                        <div class="mb-0">
                            <label for="user_account" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Akun Pengguna </span></label>
                            <input type="text" name="user_account" id="user_account" value="{{ old('user_account') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('user_account')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                </div>

                <div class="lg:border-l-4 border-purple-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-purple-200">🛜 Koneksi & Network</h3>
                        <!-- ip address -->
                        <div class="mb-3 md:mb-4">
                            <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">IP Address <span class="text-yellow-500">( jika tidak ada : - )</span> <span class="text-red-500">*</span></label>
                            <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="" required>
                            @error('ip_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- mac lan -->
                        <div class="mb-3 md:mb-4">
                            <label for="mac_lan" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Mac LAN</label>
                            <input type="text" name="mac_lan" id="mac_lan" value="{{ old('mac_lan') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('mac_lan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- mac wifi -->
                        <div class="mb-3 md:mb-4">
                            <label for="mac_wifi" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Mac Wifi</label>
                            <input type="text" name="mac_wifi" id="mac_wifi" value="{{ old('mac_wifi') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('mac_wifi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Connection type -->
                        <div class="mb-3 md:mb-4">
                            <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Jenis Koneksi</label>
                            <select name="connection_type" id="connection_type" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>">
                                <option value="">-- Pilih Jenis Koneksi --</option>
                                <option value="LAN">LAN</option>
                                <option value="USB">USB</option>
                                <option value="WIFI">WIFI</option>
                            </select>
                            @error('connection_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shared name -->
                        <div class="mb-3 md:mb-4">
                            <label for="shared_name" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Nama Sharing</label>
                            <input type="text" name="shared_name" id="shared_name" value="{{ old('shared_name') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('shared_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Port -->
                        <div class="mb-0">
                            <label for="port" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Port</label>
                            <input type="text" name="port" id="port" value="{{ old('port') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="USB001 / TCP/IP">
                            @error('port')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                </div>

                <div class="lg:border-l-4 border-green-500 lg:pl-4">
                    <h3 class="text-base md:text-lg font-bold text-gray-700 mb-3 md:mb-4 pb-2 lg:pb-0 lg:border-none border-b-2 border-green-200">💻 System & Software</h3>
                        <!-- os version -->
                        <div class="mb-3 md:mb-4">
                            <label for="os_version" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Versi OS</label>
                            <input type="text" name="os_version" id="os_version" value="{{ old('os_version') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('os_version')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- build -->
                        <div class="mb-3 md:mb-4">
                            <label for="build" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Build</label>
                            <input type="text" name="build" id="build" value="{{ old('build') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('build')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- office version -->
                        <div class="mb-3 md:mb-4">
                            <label for="office_version" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Versi Office</label>
                            <input type="text" name="office_version" id="office_version" value="{{ old('office_version') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('office_version')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- office key -->
                        <div class="mb-3 md:mb-4">
                            <label for="office_key" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Office Key</label>
                            <input type="text" name="office_key" id="office_key" value="{{ old('office_key') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('office_key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- antivirus -->
                        <div class="mb-0">
                            <label for="antivirus" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Antivirus</label>
                            <input type="text" name="antivirus" id="antivirus" value="{{ old('antivirus') }}" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                            @error('antivirus')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Catatan</label>
                <textarea name="catatan" id="catatan" rows="3" class="w-full px-2 md:px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                @error('catatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
<script>
$(document).ready(function() {
    var $input = $('#sn_search');
    var $suggestions = $('#sn_suggestions');
    var $hidden = $('#item_id_hidden');

    $input.on('input', function() {
        var query = $(this).val();
        $hidden.val(''); // reset hidden
        if (query.length < 3) {
            $suggestions.empty().hide();
            return;
        }
        $.ajax({
            url: '{{ route('device_details.search_items') }}',
            data: { q: query },
            dataType: 'json',
            success: function(data) {
                if (data.length === 0) {
                    $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                    return;
                }
                var html = '';
                $.each(data, function(i, item) {
                    html += '<div class="px-3 py-2 cursor-pointer hover:bg-green-100" data-id="'+item.id+'" data-text="'+item.serial_number+'">'+item.serial_number+' - '+item.merk+' - '+item.kategori+'</div>';
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
        if (!$(e.target).closest('#sn_search, #sn_suggestions').length) {
            $suggestions.hide();
        }
    });
});
</script>
@endsection
