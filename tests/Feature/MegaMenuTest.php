<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\DatabaseSeeder;

class MegaMenuTest extends TestCase
{
    use RefreshDatabase; // Mereset database otomatis setiap kali test dijalankan

    protected function setUp(): void
    {
        parent::setUp();
        // Jalankan seeder agar ada data untuk dites
        $this->seed(DatabaseSeeder::class);
    }

    public function test_halaman_home_dapat_dibuka_dan_menampilkan_produk_pilihan()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('PRODUK EKSKLUSIF ⚡');
        // Memastikan Nike Pegasus muncul di halaman depan karena status is_featured = true
        $response->assertSee('Nike Air Zoom Pegasus 40'); 
    }

    public function test_menu_sale_menampilkan_produk_diskon()
    {
        $response = $this->get('/products?type=sale');

        $response->assertStatus(200);
        // Memastikan judul terganti
        $response->assertSee('SALE');
        // Memastikan produk yang diskon muncul
        $response->assertSee('Adidas Samba OG Black');
    }

    public function test_mega_menu_filter_bekerja_dengan_akurat()
    {
        $response = $this->get('/products?gender=Laki-laki&category=Sepatu&subcategory=Sepatu+Lari');

        $response->assertStatus(200);
        // Memastikan judul merangkai dengan benar
        $response->assertSee('KATEGORI LAKI-LAKI - SEPATU - SEPATU LARI');
        // Memastikan Nike (Laki-laki) muncul
        $response->assertSee('Nike Air Zoom Pegasus 40');
        // Memastikan Adidas (Unisex/Bukan sepatu lari) tidak muncul
        $response->assertDontSee('Adidas Samba OG Black'); 
    }
}