@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
  @php

    $activeDistributions = $item->distributionItems
        ->filter(function($d) {
            return $d->distribution &&
                $d->distribution->status == 'dipakai';
        });

    @endphp

    <!-- Header -->
     <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 md:gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Show Master Data Barang</h1>
            <p class="text-xs md:text-sm text-gray-600 mt-1">Master data perangkat IT Keseluruhan</p>
        </div>
        <a href="{{ route('items.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-6 py-2 md:py-3 rounded-lg font-semibold flex items-center gap-2 transition duration-200 whitespace-nowrap text-xs md:text-base">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden sm:inline">Kembali</span>
            <span class="sm:hidden">Kembali</span>
        </a>
    </div>

    <!-- Detail Item -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8 mt-6">

        <h1 class="text-2xl font-bold mb-6">
            Detail Item
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="font-semibold text-gray-600">
                    Serial Number
                </label>
                <p>{{ $item->serial_number }}</p>
            </div>

            <div>
                <label class="font-semibold text-gray-600">
                    Merk
                </label>
                <p>{{ $item->merk }}</p>
            </div>

            <div>
                <label class="font-semibold text-gray-600">
                    Type
                </label>
                <p>{{ $item->type }}</p>
            </div>

            <div>
                <label class="font-semibold text-gray-600">
                    Kategori
                </label>
                <p>{{ $item->kategori }}</p>
            </div>

            <div>
                <label class="font-semibold text-gray-600">
                    Lokasi Penyimpanan
                </label>
                <p>
                    {{ $item->storageLocation->gedung ?? '-' }}
                    -
                    {{ $item->storageLocation->ruangan ?? '-' }}
                </p>
            </div>

            <div>
                <label class="font-semibold text-gray-600">
                    Status
                </label>
                <p>{{ $item->status }}</p>
            </div>

        </div>

    </div>

    <!-- DEVICE DETAIL -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8 mt-6">

        <h2 class="text-xl font-bold mb-6 text-gray-800">
            Device Detail
        </h2>

        @if($item->device_detail)

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="font-semibold text-gray-600">
                        PC Name
                    </label>

                    <p>{{ $item->device_detail->pc_name ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        User Account
                    </label>

                    <p>{{ $item->device_detail->user_account ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        IP Address
                    </label>

                    <p>{{ $item->device_detail->ip_address ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        Connection Type
                    </label>

                    <p>{{ $item->device_detail->connection_type ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        Shared Name
                    </label>

                    <p>{{ $item->device_detail->shared_name ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        Port
                    </label>

                    <p>{{ $item->device_detail->port ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        MAC LAN
                    </label>

                    <p>{{ $item->device_detail->mac_lan ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        MAC WIFI
                    </label>

                    <p>{{ $item->device_detail->mac_wifi ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        OS Version
                    </label>

                    <p>{{ $item->device_detail->os_version ?? '-' }}</p>
                </div>

                <div>
                    <label class="font-semibold text-gray-600">
                        Office Version
                    </label>

                    <p>{{ $item->device_detail->office_version ?? '-' }}</p>
                </div>

            </div>

            <!-- NOTES -->
            <div class="mt-6">

                <label class="font-semibold text-gray-600">
                    Catatan
                </label>

                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                    {{ $item->device_detail->notes ?? '-' }}
                </div>

            </div>

        @else

            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">

                Device detail belum tersedia.

            </div>

        @endif

    </div>
    <!-- DISTRIBUSI AKTIF -->
    <div class="bg-white rounded-xl shadow-lg p-3 md:p-6 lg:p-8 mt-6">

        <h2 class="text-xl font-bold mb-6 text-gray-800">

            Distribusi Aktif

        </h2>

        @if($activeDistributions->count())

            <div class="overflow-x-auto">

                <table class="w-full border-collapse">

                    <thead>

                        <tr class="bg-gray-100">

                            <th class="border px-4 py-2 text-left">
                                User
                            </th>

                            <th class="border px-4 py-2 text-left">
                                Lokasi
                            </th>

                            <th class="border px-4 py-2 text-left">
                                Tanggal
                            </th>

                            <th class="border px-4 py-2 text-left">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($activeDistributions as $distributionItem)

                            <tr>

                                <td class="border px-4 py-2">

                                    {{ $distributionItem->distribution->nama_user ?? '-' }}

                                </td>

                                <td class="border px-4 py-2">

                                    @if($distributionItem->distribution->location)

                                        {{ $distributionItem->distribution->location->gedung }}
                                        -
                                        {{ $distributionItem->distribution->location->ruangan }}

                                    @else

                                        -

                                    @endif

                                </td>

                                <td class="border px-4 py-2">

                                    {{ $distributionItem->distribution->tanggal_distribusi ?? '-' }}

                                </td>

                                <td class="border px-4 py-2">

                                    <span class="px-2 py-1 rounded bg-red-100 text-red-800">

                                        Dipakai

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">

                Item sedang tidak digunakan.

            </div>

        @endif

    </div>

</div>
@endsection
