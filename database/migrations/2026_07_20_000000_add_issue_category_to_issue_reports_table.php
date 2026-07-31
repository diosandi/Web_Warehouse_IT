<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->string('issue_category')->default('device')->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('issue_reports', function (Blueprint $table) {
            $table->dropColumn('issue_category');
        });
    }
};
