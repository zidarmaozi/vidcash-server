<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('views', function (Blueprint $table) {
            // Add income tracking columns
            $table->decimal('income_amount', 15, 2)->default(0.00)->after('ip_address');
            $table->decimal('cpm_at_time', 15, 2)->default(0.00)->after('income_amount');
            $table->boolean('validation_passed')->default(false)->after('cpm_at_time');
            $table->boolean('income_generated')->default(false)->after('validation_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->dropColumn([
                'income_amount',
                'cpm_at_time', 
                'validation_passed',
                'income_generated'
            ]);
        });
    }
};
