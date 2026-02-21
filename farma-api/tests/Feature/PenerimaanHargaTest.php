<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Obat;
use App\Models\Supplier;
use Laravel\Sanctum\Sanctum;

class PenerimaanHargaTest extends TestCase
{
    use RefreshDatabase;

    public function test_harga_jual_naik_otomatis_saat_restock_mahal()
    {
        // 1. Setup User Superadmin
        $user = User::factory()->create(['role' => 'superadmin']);
        Sanctum::actingAs($user);

        // 2. Setup Supplier
        $supplier = Supplier::factory()->create();

        // 3. Setup Obat dg Harga Lama
        // Harga Beli: 10,000 | Harga Jual: 15,000 (Margin awal 50%}
        $obat = Obat::create([
            'kode_obat' => 'OBT-TES-123',
            'nama_obat' => 'Paracetamol Test',
            'kategori' => 'Obat Bebas',
            'satuan' => 'Strip',
            'stok' => 10,
            'stok_minimal' => 5,
            'harga_beli' => 10000,
            'harga_jual' => 15000,
            'tgl_kadaluarsa' => now()->addYear(),
        ]);

        // 4. Simulasi Restock dengan Harga lebih Mahal
        // Beli harga 14.000. 
        // 20% margin dari 14.000 = 14.000 + 2.800 = 16.800
        // Dibulatkan ke atas (kelipatan 500) = 17.000
        $response = $this->postJson('/api/penerimaan', [
            'supplier_id' => $supplier->id,
            'no_faktur' => 'INV-TEST-001',
            'items' => [
                [
                    'obat_id' => $obat->id,
                    'jumlah' => 50,
                    'harga_satuan' => 14000 
                ]
            ]
        ]);

        $response->assertStatus(201);

        // 5. Verifikasi Harga & Stok di Database
        $obatRefreshed = Obat::find($obat->id);
        
        $this->assertEquals(60, $obatRefreshed->stok, "Stok tidak bertambah dengan benar.");
        $this->assertEquals(14000, $obatRefreshed->harga_beli, "Harga beli tidak terupdate.");
        $this->assertEquals(17000, $obatRefreshed->harga_jual, "Harga jual gagal dihitung dengan margin 20% + pembulatan 500.");
    }
}
