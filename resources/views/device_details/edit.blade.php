@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Device Details</h1>
            <p class="text-gray-600 mt-1">Update data device details {{ optional($device_detail->item)->serial_number ?? '-' }}</p>
        </div>
        <a href="{{ route('device_details.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <form action="{{ route('device_details.update', $device_detail) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- item SN autocomplete -->
                <div class="relative">
                    <label for="sn_search" class="block text-sm font-medium text-gray-700 mb-2">Serial Number <span class="text-red-500">*</span></label>
                    <input type="text" name="sn_search" id="sn_search" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Ketik Serial Number..." required value="{{ old('sn_search', optional($device_detail->item)->serial_number) }} - {{ old('sn_search', optional($device_detail->item)->merk) }} - {{ old('sn_search', optional($device_detail->item)->kategori) }}">
                    <input type="hidden" name="item_id" id="item_id_hidden" value="{{ old('item_id', $device_detail->item_id) }}">
                    <div id="sn_suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto"></div>
                    @error('item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- PC Name-->        
                <div>
                    <label for="pc_name" class="block text-sm font-medium text-gray-700 mb-2">PC Name<span class="text-red-500">*</span></label>
                    <input type="text" name="pc_name" id="pc_name" value="{{ old('pc_name', $device_detail->pc_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('pc_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                 <!-- User Account -->        
                <div>
                    <label for="user_account" class="block text-sm font-medium text-gray-700 mb-2">User Account<span class="text-red-500">*</span></label>
                    <input type="text" name="user_account" id="user_account" value="{{ old('user_account', $device_detail->user_account) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('user_account')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ip address -->
                <div>
                    <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">IP Address <span class="text-red-500">*</span></label>
                    <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address', $device_detail->ip_address) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('ip_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- mac lan -->
                <div>
                    <label for="mac_lan" class="block text-sm font-medium text-gray-700 mb-2">Mac LAN</label>
                    <input type="text" name="mac_lan" id="mac_lan" value="{{ old('mac_lan', $device_detail->mac_lan) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('mac_lan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- mac wifi -->
                <div>
                    <label for="mac_wifi" class="block text-sm font-medium text-gray-700 mb-2">Mac Wifi</label>
                    <input type="text" name="mac_wifi" id="mac_wifi" value="{{ old('mac_wifi', $device_detail->mac_wifi) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('mac_wifi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- os version -->
                <div>
                    <label for="os_version" class="block text-sm font-medium text-gray-700 mb-2">OS Version</label>
                    <input type="text" name="os_version" id="os_version" value="{{ old('os_version', $device_detail->os_version) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('os_version')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- build -->
                <div>
                    <label for="build" class="block text-sm font-medium text-gray-700 mb-2">Build</label>
                    <input type="text" name="build" id="build" value="{{ old('build', $device_detail->build) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('build')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- office version -->
                <div>
                    <label for="office_version" class="block text-sm font-medium text-gray-700 mb-2">Office Version</label>
                    <input type="text" name="office_version" id="office_version" value="{{ old('office_version', $device_detail->office_version) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('office_version')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- office key -->
                <div>
                    <label for="office_key" class="block text-sm font-medium text-gray-700 mb-2">Office Key</label>
                    <input type="text" name="office_key" id="office_key" value="{{ old('office_key', $device_detail->office_key) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('office_key')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- antivirus -->
                <div>
                    <label for="antivirus" class="block text-sm font-medium text-gray-700 mb-2">Antivirus</label>
                    <input type="text" name="antivirus" id="antivirus" value="{{ old('antivirus', $device_detail->antivirus) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="">
                    @error('antivirus')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row gap-2 md:gap-4 border-t pt-4 md:pt-6 mt-4 md:mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan
                </button>

                <a href="{{ route('device_details.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center gap-2">
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
        if (query.length < 1) {
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
