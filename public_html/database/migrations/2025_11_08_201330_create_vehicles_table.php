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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('plate_number', 20)->unique();
            $table->string('brand', 50);
            $table->string('model', 100);
            $table->string('variant', 100)->nullable();
            $table->year('year')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('chassis_no', 50)->nullable();
            $table->string('engine_no', 50)->nullable();
            $table->string('vin', 50)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->string('transmission', 20)->nullable();
            $table->string('fuel_type', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('customer_id', 'vehicles_customer_id_index');
            $table->index('is_active', 'vehicles_is_active_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};