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
Schema::create('penerimaan_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('penerimaan_id')->constrained('penerimaan')->cascadeOnDelete();
    $table->foreignId('obat_id')->constrained('obat');
    $table->integer('qty');
    $table->decimal('harga', 15, 2);
    $table->timestamps();
});

}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('penerimaan_details');
}

};
