@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Tambah User</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Buat akun baru untuk pengelola Warehouse IT</p>
    </div>

    <form method="POST" action="{{ route('users.store') }}" class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 space-y-5">
        @csrf
        @include('users.form', ['button' => 'Simpan'])
    </form>
</div>
@endsection
