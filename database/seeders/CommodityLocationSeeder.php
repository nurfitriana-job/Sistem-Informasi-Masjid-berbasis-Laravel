<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommodityLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            'Ruang Salat Utama',       // Ruang salat utama di masjid
            'Ruang Salat Wanita',      // Ruang salat khusus untuk wanita
            'Ruang Takmir',            // Ruang untuk pengurus masjid
            'Ruang Wudhu Laki-laki',   // Ruang wudhu untuk laki-laki
            'Ruang Wudhu Wanita',      // Ruang wudhu untuk wanita
            'Ruang Perpustakaan',      // Ruang perpustakaan masjid
            'Ruang Koperasi',          // Ruang koperasi masjid
            'Ruang Parkir',            // Area parkir kendaraan
            'Ruang Audio Visual',      // Ruang untuk peralatan multimedia dan audio visual
            'Ruang Multimedia',        // Ruang untuk kegiatan multimedia
        ];

        for ($i = 1; $i < count($locations); $i++) {
            DB::table('commodity_locations')->insert([
                'name' => $locations[$i],
                'description' => 'Ruangan ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($i = 1; $i < 6; $i++) {
            DB::table('commodity_locations')->insert([
                'name' => 'Kelas ' . $i,
                'description' => 'Ruangan Kelas ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
