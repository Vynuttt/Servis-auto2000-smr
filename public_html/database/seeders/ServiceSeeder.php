<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'id' => 1,
                'code' => 'SVC001',
                'name' => 'Ganti Oli',
                'description' => 'Ganti oli mesin standar',
                'base_price' => 150000.00,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'code' => 'SVC002',
                'name' => 'Tune Up',
                'description' => 'Tune up mesin lengkap',
                'base_price' => 300000.00,
                'is_active' => true,
            ],
            [
                'id' => 3,
                'code' => 'SVC003',
                'name' => 'AC Service',
                'description' => 'Service AC kendaraan',
                'base_price' => 250000.00,
                'is_active' => true,
            ],
            [
                'id' => 4,
                'code' => 'SVC004',
                'name' => 'Body Repair',
                'description' => 'Perbaikan body kendaraan',
                'base_price' => 500000.00,
                'is_active' => true,
            ],
            [
                'id' => 5,
                'code' => 'SVC005',
                'name' => 'Brake Service',
                'description' => 'Service rem lengkap',
                'base_price' => 200000.00,
                'is_active' => true,
            ],
            [
                'id' => 6,
                'code' => 'SVC006',
                'name' => 'Tire Rotation',
                'description' => 'Rotasi ban',
                'base_price' => 100000.00,
                'is_active' => true,
            ],
            [
                'id' => 7,
                'code' => 'SVC007',
                'name' => 'Servis Ringan',
                'description' => 'Servis ringan adalah perawatan rutin dan berkala pada kendaraan yang berfokus pada komponen-komponen cepat aus (fast-moving parts) dan tidak memerlukan pembongkaran mesin atau transmisi yang rumit. Tujuannya adalah untuk menjaga kondisi dasar kendaraan dan mencegah kerusakan yang lebih serius.',
                'base_price' => 0.00,
                'is_active' => true,
            ],
        ]);
    }
}