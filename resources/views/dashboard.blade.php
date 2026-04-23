@extends('layouts.app')

@section('content')
<br>
<div class="container mx-auto px-4 py-12">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-1">Selamat datang di Warehouse IT RSCM</p>
    </div>
    {{-- card 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-1-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Warehouse</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">0</h3> 
                </div>
                <div class="bg-blue-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 2 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-1-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Barang</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">0</h3> 
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
        {{-- card 3 --}}    
        <div class="bg-white rounded-xl shadow-lg p-6 border-1-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Total Barang Masuk</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">0</h3> 
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>      
        {{-- card 4 --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-1-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>           
                    <p class="text-gray-500 text-sm font-medium">Total Barang Keluar</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">0</h3>    
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </div>
            </div>
        </div>      
    </div>
    
<!-- Welcome Message -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-8 text-white">
        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-4 rounded-lg">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-green-100"><strong>Login berhasil!</strong></p>
            </div>
        </div>
    </div>
</div>
@endsection