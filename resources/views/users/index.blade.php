@extends('layouts.app')

@section('content')
@php
    $roleLabels = [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
    ];

    $statusLabels = [
        'active' => 'Aktif',
        'inactive' => 'Nonaktif',
    ];

    $hasActiveFilter = request()->filled('search')
        || request()->filled('role')
        || request()->filled('status');
@endphp

<br>
<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kelola Pengguna</h1>
            <p class="text-gray-600 mt-1">Kelola akun Super Admin dan Admin</p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-success">
            <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <details class="bg-white rounded-xl shadow-lg mb-6 group" {{ $hasActiveFilter ? 'open' : '' }}>
        <summary class="list-none p-4 md:p-6 cursor-pointer flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Filter & Cari User</h2>
            </div>
            <span class="text-sm text-gray-500">Pilih</span>
        </summary>

        <div class="px-4 md:px-6 pb-4 md:pb-6">
            <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
                <div>
                    <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Cari User</label>

                    <div class="relative">
                        <input type="text" name="search" id="search" autocomplete="off" value="{{ request('search') }}"
                            placeholder="Cari nama atau username"
                            class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs uppercase">
                        <input type="hidden" name="user_id" id="user_id_hidden" value="{{ request('user_id') }}">
                        <div id="suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-56 overflow-auto text-xs uppercase"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Role</label>
                        <select name="role" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs uppercase">
                            <option value="">Semua</option>
                            <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-xs uppercase">
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                    <button type="submit" class="btn btn-success btn-block">Cari</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-block">Bersihkan</a>
                </div>

                @if($hasActiveFilter)
                    <div class="text-xs md:text-sm text-gray-600 pt-3 border-t border-gray-200">
                        <span class="font-semibold text-gray-700 block mb-2">Filter aktif:</span>

                        <div class="flex flex-wrap gap-2">
                            @if(request('search'))
                                @php
                                    $searchQuery = request()->query();
                                    unset($searchQuery['search'], $searchQuery['user_id']);
                                @endphp

                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2">
                                    <span>Cari: <strong>{{ request('search') }}</strong></span>
                                    <a href="{{ route('users.index', $searchQuery) }}" class="hover:text-yellow-900 font-bold text-lg leading-none">x</a>
                                </span>
                            @endif

                            @if(request('role'))
                                @php
                                    $roleQuery = request()->query();
                                    unset($roleQuery['role']);
                                @endphp

                                <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2">
                                    <span>Role: <strong>{{ $roleLabels[request('role')] ?? request('role') }}</strong></span>
                                    <a href="{{ route('users.index', $roleQuery) }}" class="hover:text-green-900 font-bold text-lg leading-none">x</a>
                                </span>
                            @endif

                            @if(request('status'))
                                @php
                                    $statusQuery = request()->query();
                                    unset($statusQuery['status']);
                                @endphp

                                <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full inline-flex items-center gap-2">
                                    <span>Status: <strong>{{ $statusLabels[request('status')] ?? request('status') }}</strong></span>
                                    <a href="{{ route('users.index', $statusQuery) }}" class="hover:text-blue-900 font-bold text-lg leading-none">x</a>
                                </span>
                            @endif

                            <a href="{{ route('users.index') }}" class="btn btn-soft-danger btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            Hapus Semua
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </details>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
            <div class="text-xs md:text-sm text-gray-600">
                <span class="font-semibold text-gray-800">{{ $users->total() }}</span>
                <span>Data User Ditemukan</span>
            </div>
            <div class="text-xs md:text-sm text-gray-600">
                Halaman <span class="font-semibold">{{ $users->currentPage() }}</span> dari <span class="font-semibold">{{ $users->lastPage() }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-600 to-green-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $users->firstItem() + $index }}</td>
                            <td class="px-6 py-4 text-sm font-semibold uppercase text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm font-mono uppercase text-gray-700">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-sm font-mono uppercase text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $roleLabels[$user->role] ?? $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($user->is_active)
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm font-medium text-gray-500">
                                Belum ada data user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    var $input = $('#search');
    var $suggestions = $('#suggestions');
    var $hidden = $('#user_id_hidden');
    var searchDelay;

    $input.on('input', function() {
        var query = $(this).val().trim();
        $hidden.val('');
        clearTimeout(searchDelay);

        if (query.length < 1) {
            $suggestions.empty().hide();
            return;
        }

        searchDelay = setTimeout(function() {
            $.ajax({
                url: '{{ route('users.search_users') }}',
                data: { q: query },
                dataType: 'json',
                success: function(data) {
                    if (data.length === 0) {
                        $suggestions.html('<div class="px-3 py-2 text-gray-500">Tidak ada hasil</div>').show();
                        return;
                    }

                    var html = '';

                    $.each(data, function(i, user) {
                        html += `
                            <div class="px-3 py-2 cursor-pointer hover:bg-green-100"
                                data-id="${user.id}"
                                data-text="${user.text}">
                                ${user.text}
                            </div>
                        `;
                    });

                    $suggestions.html(html).show();
                }
            });
        }, 250);
    });

    $suggestions.on('click', 'div[data-id]', function() {
        var id = $(this).data('id');
        var text = $(this).data('text');

        $input.val(text);
        $hidden.val(id);
        $suggestions.hide();
    });

    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#search, #suggestions').length) {
            $suggestions.hide();
        }
    });
});
</script>
@endsection