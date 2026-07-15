@php
    $isCreate = request()->routeIs('users.create');
    $selectedRole = old('role', $user->role ?? 'admin');
    $selectedStatus = (string) old('is_active', isset($user->is_active) ? (int) $user->is_active : 1);
@endphp

@if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
            required>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Username</label>
        <input type="text" name="username" value="{{ old('username', $user->username) }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-green-500"
            required>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Role</label>
        <select name="role" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <option value="admin" {{ $selectedRole === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="super_admin" {{ $selectedRole === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">Status</label>
        <select name="is_active" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
            <option value="1" {{ $selectedStatus === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ $selectedStatus === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-xs font-semibold uppercase text-gray-500 mb-2">
            {{ $isCreate ? 'Password' : 'Password Baru' }}
        </label>
        <input type="password" name="password"
            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
            {{ $isCreate ? 'required' : '' }}>

        @if(! $isCreate)
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti password.</p>
        @endif
    </div>
</div>

<div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
    <button type="submit" class="btn btn-success">{{ $button }}</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
</div>
