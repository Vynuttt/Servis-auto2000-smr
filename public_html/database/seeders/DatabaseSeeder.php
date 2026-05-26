<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeder untuk data master
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
            StallSeeder::class,
            WorkOrderStageSeeder::class,
            UserSeeder::class, // Test users
        ]);

        $this->command->info('');
        $this->command->info('🎉 All seeders completed successfully!');
        $this->command->info('');
    }
}