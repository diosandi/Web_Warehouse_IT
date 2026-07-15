<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->string('pc_name')->nullable();
            $table->string('user_account')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('mac_lan')->nullable();
            $table->string('mac_wifi')->nullable();
            $table->string('connection_type')->nullable(); // LAN / USB / WIFI / HDMI / VGA / DP
            $table->string('port')->nullable();
            $table->string('shared_name')->nullable();


            $table->string('os_version')->nullable();
            $table->string('build')->nullable();

            $table->string('office_version')->nullable();
            $table->string('office_key')->nullable();

            $table->string('antivirus')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_details');
    }
};
