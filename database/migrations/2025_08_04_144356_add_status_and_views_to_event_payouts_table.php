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
    Schema::table('event_payouts', function (Blueprint $table) {
        $table->string('status')->default('pending')->after('prize_amount'); // pending, confirmed
        $table->unsignedBigInteger('total_views')->after('rank');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_payouts', function (Blueprint $table) {
            //
        });
    }
};
