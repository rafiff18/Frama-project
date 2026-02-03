<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->foreignId('obat_id')
                  ->after('penjualan_id')
                  ->constrained('obat')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->dropForeign(['obat_id']);
            $table->dropColumn('obat_id');
        });
    }
};
