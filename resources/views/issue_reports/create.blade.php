@extends('layouts.app')

@section('content')
@php
    $selectedCategory = old('issue_category', 'device');
@endphp

<br>
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Buat Laporan Kendala</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kirim laporan kendala perangkat atau kendala lainnya.</p>
        </div>
        <a href="{{ route('issue_reports.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($reportableItems->isEmpty())
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg mb-6">
            Belum ada perangkat aktif yang terhubung ke akun ini. Kamu tetap bisa pilih kategori Lainnya untuk laporan yang tidak terikat perangkat.
        </div>
    @endif

    <form method="POST" action="{{ route('issue_reports.store') }}" enctype="multipart/form-data" class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Kategori Kendala <span class="text-red-500">*</span></label>
            <select name="issue_category" id="issue_category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                @foreach($categoryOptions as $category => $label)
                    <option value="{{ $category }}" {{ $selectedCategory === $category ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div id="device-field">
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Perangkat <span class="text-red-500">*</span></label>
            <select name="distribution_item_id" id="distribution_item_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" {{ $selectedCategory === 'device' ? 'required' : '' }}>
                <option value="">-- Pilih perangkat --</option>
                @foreach($reportableItems as $distributionItem)
                    @php
                        $item = $distributionItem->item;
                        $distribution = $distributionItem->distribution;
                        $location = $distribution?->location;
                        $selected = (string) $selectedDistributionItemId === (string) $distributionItem->id;
                    @endphp
                    <option value="{{ $distributionItem->id }}" {{ $selected ? 'selected' : '' }}>
                        {{ $item->kategori ?? '-' }} - {{ $item->serial_number ?? '-' }} - {{ $item->merk ?? '-' }}
                        | {{ $location->gedung ?? '-' }} - {{ $location->ruangan ?? '-' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Judul Kendala <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: PC tidak bisa menyala"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Prioritas <span class="text-red-500">*</span></label>
            <select name="priority" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                @foreach($priorityOptions as $priority => $label)
                    <option value="{{ $priority }}" {{ old('priority', 'normal') === $priority ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Deskripsi Kendala <span class="text-red-500">*</span></label>
            <textarea name="description" rows="6" placeholder="Jelaskan kendala, kapan terjadi, dan hal yang sudah dicoba."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">Bukti / Foto / Video</label>
            <input type="file" name="evidence" accept="image/*,video/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Format yang didukung: JPG, JPEG, PNG, WebP, MP4, WebM, AVI, MOV, MKV. Maksimal 10 MB.</p>
        </div>

        <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
            <button type="submit" class="btn btn-success">Kirim Laporan</button>
            <a href="{{ route('issue_reports.index') }}" class="btn bg-gray-500 hover:bg-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('issue_category');
        const deviceField = document.getElementById('device-field');
        const deviceSelect = document.getElementById('distribution_item_id');

        function syncDeviceField() {
            const usesDevice = categorySelect?.value === 'device';

            if (deviceField) {
                deviceField.classList.toggle('hidden', !usesDevice);
            }

            if (deviceSelect) {
                deviceSelect.required = usesDevice;
                deviceSelect.disabled = !usesDevice;

                if (!usesDevice) {
                    deviceSelect.value = '';
                }
            }
        }

        categorySelect?.addEventListener('change', syncDeviceField);
        syncDeviceField();
    });
</script>
@endsection
