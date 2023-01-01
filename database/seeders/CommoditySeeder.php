<?php

namespace Database\Seeders;

use App\Models\CommodityLocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommoditySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carbon = new Carbon;

        $commodity_locations = CommodityLocation::all();

        $commodities = [
            'Meja Pengurus Masjid',
            'Kursi Sholat',
            'Karpet Sholat',
            'Al-Qur\'an',
            'Mikrofon',
            'Speaker',
            'Lampu Dinding',
            'Lampu Gantung',
            'Lemari Al-Qur\'an',
            'Papan Pengumuman',
            'Sajadah',
            'Tempat Sedekah',
            'Dispenser',
            'AC Masjid',
            'Kipas Angin Dinding',
            'Tempat Air Zamzam',
            'Pengeras Suara',
            'Tikar Sholat',
            'Rak Sepatu Masjid',
            'Tempat Wudu',
        ];

        // Daftar merek barang (disesuaikan)
        $brands = [
            'Informa',
            'IKEA',
            'Sanken',
            'Panasonic',
            'Sharp',
            'Toshiba',
            'Dove\'s',
            'Funika',
            'Atria',
            'Vivere',
        ];

        // Daftar bahan komoditas (disesuaikan untuk masjid)
        $materials = [
            'Kayu Solid',
            'Aluminium',
            'MDF (Medium Density Fibreboard)',
            'Karet',
            'Karpet Wol',
            'Stainless Steel',
            'Rotan',
            'Plastik',
        ];

        for ($i = 1; $i <= count($commodities); $i++) {
            DB::table('commodities')->insert([
                'commodity_acquisition_id' => mt_rand(1, 2),
                'commodity_location_id' => mt_rand(1, count($commodity_locations)),
                'item_code' => 'BRG-' . mt_rand(1000, 9000) . mt_rand(100, 900),
                'name' => $commodities[array_rand($commodities)],
                'brand' => $brands[array_rand($brands)],
                'material' => $materials[array_rand($materials)],
                'year_of_purchase' => mt_rand(2010, date('Y')),
                'condition' => mt_rand(1, 3),
                'quantity' => mt_rand(50, 200),
                'price' => mt_rand(5000, 500000),
                'price_per_item' => mt_rand(2500, 150000),
                'note' => 'Keterangan barang',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
