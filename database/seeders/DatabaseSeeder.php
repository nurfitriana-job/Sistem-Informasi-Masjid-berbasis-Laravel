<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
        ]);

        $roles = [
            'admin',
            'user',
            'imam',
            'pengurus',
            'jamaah',
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $user->assignRole('admin');

        $this->call([
            SliderSeeder::class,
            ShieldSeeder::class,
            CommodityLocationSeeder::class,
            CommodityAcquisitionSeeder::class,
            CommoditySeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }
}
