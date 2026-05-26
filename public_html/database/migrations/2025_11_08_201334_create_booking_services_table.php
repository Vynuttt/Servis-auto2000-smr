<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->integer('qty')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['booking_id', 'service_id'], 'uq_booking_service');
        });
        
        // Add generated column for subtotal (qty * price)
        // Note: Laravel doesn't support GENERATED columns in schema builder
        // So we need to use raw SQL
        DB::statement('ALTER TABLE booking_services ADD COLUMN subtotal DECIMAL(12,2) GENERATED ALWAYS AS (qty * price) STORED AFTER price');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};  