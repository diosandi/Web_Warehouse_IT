<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('distribution_items', 'return_condition_status')) {
            Schema::table('distribution_items', function (Blueprint $table) {
                $table->enum('return_condition_status', ['available','maintenance'])
                    ->nullable()
                    ->after('returned_at');
            });
        }

        DB::table('distribution_items')
            ->join('items', 'items.id', '=', 'distribution_items.item_id')
            ->where('distribution_items.status', 'dikembalikan')
            ->whereNull('distribution_items.return_condition_status')
            ->update([
                'distribution_items.return_condition_status' => DB::raw(
                    "CASE WHEN items.status = 'maintenance' THEN 'maintenance' ELSE 'available' END"
                ),
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('distribution_items', 'return_condition_status')) {
            Schema::table('distribution_items', function (Blueprint $table) {
                $table->dropColumn('return_condition_status');
            });
        }
    }
};
