<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkOrderStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::parse('2025-10-10 11:57:42');

        DB::table('work_order_stages')->insert([
            [
                'id' => 1,
                'name' => 'Check-in / Verifikasi Berkas',
                'slug' => 'check_in',
                'position' => 1,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Menunggu Stall Kosong',
                'slug' => 'waiting_stall',
                'position' => 2,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Servis Dimulai (In Progress)',
                'slug' => 'in_progress',
                'position' => 3,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Quality Control (QC)',
                'slug' => 'qc',
                'position' => 4,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Cuci Mobil (Car Wash)',
                'slug' => 'car_wash',
                'position' => 5,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Final Inspection / Test Drive',
                'slug' => 'final_check',
                'position' => 6,
                'is_final' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Siap Diambil / Selesai',
                'slug' => 'ready',
                'position' => 7,
                'is_final' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}