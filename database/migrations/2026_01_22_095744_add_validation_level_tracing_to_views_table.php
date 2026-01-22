<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->integer('vl_at_time')->nullable()->after('cpm_at_time');
            $table->integer('adjusted_vl')->nullable()->after('vl_at_time');
            $table->integer('viewer_vl')->nullable()->after('adjusted_vl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->dropColumn(['vl_at_time', 'adjusted_vl', 'viewer_vl']);
        });
    }
};
