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
    Schema::table('users', function (Blueprint $table) {
        // Menambahkan kolom untuk saldo saat ini
        $table->decimal('balance', 15, 2)->default(0.00)->after('role');
        
        // Menambahkan kolom untuk total yang sudah ditarik
        $table->decimal('total_withdrawn', 15, 2)->default(0.00)->after('balance');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
