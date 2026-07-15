<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('old_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('new_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('source')->default('master_edit');
            $table->string('event')->default('status_changed');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('old_condition_note')->nullable();
            $table->text('new_condition_note')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['item_id', 'created_at']);
            $table->index(['source', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_status_histories');
    }
};
