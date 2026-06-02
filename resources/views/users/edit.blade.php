@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
        <p class="text-gray-600 mt-1">Perbarui akun dan hak akses user</p>
    </div>

    <form method="POST" action="{{ route('users.update', $user->id) }}" class="bg-white rounded-xl shadow-lg p-6 space-y-5">
        @csrf
        @method('PUT')
        @include('users.form', ['button' => 'Update'])
    </form>
</div>
@endsection