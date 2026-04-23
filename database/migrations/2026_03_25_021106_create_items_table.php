<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // relasi ke barang masuk
            $table->foreignId('barang_masuk_id')
                  ->nullable()
                  ->constrained('barang_masuk')
                  ->onDelete('cascade');

            // data utama barang
            $table->enum('kategori', ['PC','Monitor','Printer Kertas','Printer Barcode','Scanner'])->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();

            // IDENTITAS UTAMA
            $table->string('serial_number')->unique();
            $table->string('service_tag')->nullable();

            // spesifikasi
            $table->string('processor')->nullable();
            $table->integer('ram_gb')->nullable();
            $table->integer('storage_gb')->nullable();
            $table->string('vga')->nullable();
            $table->string('os')->nullable();
            $table->year('tahun')->nullable();

            // status barang
            $table->enum('status', ['available','used','maintenance'])
                  ->default('available');
            // $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};