@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Koreksi Serial Number</h1>
            <p class="mt-1 text-gray-600">Perbaiki SN typo tanpa menghapus riwayat perubahan.</p>
        </div>

        <a href="{{ $redirect }}" class="btn btn-secondary">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50 p-4 text-sm font-medium text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500">Tanggal Masuk</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $barang_masuk->tanggal_masuk ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500">Supplier</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $barang_masuk->supplier ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500">Nomor PO</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $barang_masuk->po_number ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500">Total Barang</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $barang_masuk->items->count() }}</p>
            </div>
        </div>
    </section>

    <section class="mb-6 rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <form action="{{ route('barang_masuk.update_koreksi_sn', $barang_masuk->id) }}" method="POST">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="mb-5">
                <label class="mb-2 block text-sm font-semibold text-gray-700">Alasan Koreksi <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500" required>{{ old('reason') }}</textarea>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] divide-y divide-gray-200">
                    <thead class="bg-green-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Merk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Serial Number Baru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($barang_masuk->items as $item)
                            @php
                                $isUsed = $item->isUsed();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">{{ $item->kategori ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">{{ $item->merk ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm uppercase text-gray-700">{{ $item->type ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($isUsed)
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Dipakai</span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Belum dipakai</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text"
                                        name="serial_numbers[{{ $item->id }}]"
                                        value="{{ old('serial_numbers.' . $item->id, $item->serial_number) }}"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500"
                                        required>
                                    <p class="mt-1 text-xs text-gray-500">SN saat ini: {{ $item->serial_number }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-wrap gap-3 border-t pt-5">
                <button type="submit" class="btn btn-success">
                    Simpan Koreksi
                </button>
                <a href="{{ $redirect }}" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Riwayat Koreksi SN</h2>
            <p class="text-sm text-gray-600">Catatan SN lama, SN baru, user, dan alasan koreksi.</p>
        </div>

        @if($corrections->count())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[840px] divide-y divide-gray-200">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">SN Lama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">SN Baru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-white">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($corrections as $correction)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $correction->created_at?->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $correction->old_serial_number }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $correction->new_serial_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $correction->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $correction->reason }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4 text-sm font-medium text-yellow-700">
                Belum ada riwayat koreksi SN untuk barang masuk ini.
            </div>
        @endif
    </section>
</div>
@endsection
