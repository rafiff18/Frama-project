<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->integer('qty')->after('obat_id');
            $table->decimal('harga', 15, 2)->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('penjualan_details', function (Blueprint $table) {
            $table->dropColumn(['qty', 'harga']);
        });
    }
};
