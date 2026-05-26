<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Owner',
                'slug' => 'owner',
                'description' => 'Pemilik cabang bengkel',
                'created_at' => '2025-10-08 05:17:14',
                'updated_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Service advisor/Kepala Bengkel',
                'created_at' => '2025-10-08 05:17:14',
                'updated_at' => null,
            ],
            [
                'id' => 3,
                'name' => 'Mechanic',
                'slug' => 'mechanic',
                'description' => 'Teknisi bengkel',
                'created_at' => '2025-10-08 05:17:14',
                'updated_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Pelanggan',
                'created_at' => '2025-10-08 05:17:14',
                'updated_at' => null,
            ],
        ]);
    }
}