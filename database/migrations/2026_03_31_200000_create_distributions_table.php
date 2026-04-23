<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();

            // relasi utama

            $table->foreignId('location_id')
                  ->constrained('locations')
                  ->onDelete('cascade');

            // data user manual
            $table->string('nama_user')->nullable();
            $table->string('divisi')->nullable();

            // info distribusi
            $table->date('tanggal_distribusi');

            $table->enum('status', ['dipakai','dipinjam','dikembalikan'])
                  ->default('dipakai');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};