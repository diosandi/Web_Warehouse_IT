<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_berkalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_item_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_cek');
            $table->string('periode_bulan', 7);
            $table->enum('kondisi', ['baik', 'perlu_perbaikan'])->default('baik');
            $table->json('checklist')->nullable();
            $table->text('catatan')->nullable();
            $table->string('teknisi')->nullable();
            $table->enum('status', ['sudah_dicek', 'perlu_perbaikan'])->default('sudah_dicek');
            $table->timestamps();

            $table->index(['periode_bulan', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_berkalas');
    }
};
