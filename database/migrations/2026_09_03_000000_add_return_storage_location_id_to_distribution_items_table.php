<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('distribution_items', 'return_storage_location_id')) {
            Schema::table('distribution_items', function (Blueprint $table) {
                $table->foreignId('return_storage_location_id')
                    ->nullable()
                    ->after('return_condition_status')
                    ->constrained('locations')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('distribution_items', 'return_storage_location_id')) {
            Schema::table('distribution_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('return_storage_location_id');
            });
        }
    }
};
