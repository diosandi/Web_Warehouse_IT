<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY status ENUM('available','used','maintenance','retired','vendor') NOT NULL DEFAULT 'available'");
            DB::statement("ALTER TABLE locations MODIFY type ENUM('warehouse','distribution','maintenance','vendor') NOT NULL DEFAULT 'warehouse'");
        }

        $vendorLocation = DB::table('locations')
            ->where('gedung', 'Vendor')
            ->where('ruangan', 'Vendor')
            ->first();

        if ($vendorLocation) {
            DB::table('locations')
                ->where('id', $vendorLocation->id)
                ->update([
                    'type' => 'vendor',
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('locations')->insert([
                'gedung' => 'Vendor',
                'ruangan' => 'Vendor',
                'type' => 'vendor',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('items')
            ->where('status', 'vendor')
            ->update([
                'status' => 'maintenance',
                'updated_at' => now(),
            ]);

        DB::table('locations')
            ->where('type', 'vendor')
            ->update([
                'type' => 'warehouse',
                'updated_at' => now(),
            ]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE items MODIFY status ENUM('available','used','maintenance','retired') NOT NULL DEFAULT 'available'");
            DB::statement("ALTER TABLE locations MODIFY type ENUM('warehouse','distribution','maintenance') NOT NULL DEFAULT 'warehouse'");
        }
    }
};
