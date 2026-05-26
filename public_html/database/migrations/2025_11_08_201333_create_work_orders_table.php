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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 30)->unique();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('restrict');
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('mechanic_profile_id')->nullable();
            $table->foreignId('stall_id')->nullable()->constrained('stalls')->onDelete('set null');
            $table->enum('priority', ['Regular', 'Urgent', 'Rework'])->default('Regular');
            $table->enum('status', [
                'Planned',
                'Checked-In',
                'Waiting',
                'In-Progress',
                'QC',
                'Wash',
                'Final',
                'Done',
                'Cancelled'
            ])->default('Planned');
            $table->foreignId('current_stage_id')->nullable()->constrained('work_order_stages')->onDelete('set null')->onUpdate('cascade');
            $table->datetime('planned_start')->nullable();
            $table->datetime('planned_finish')->nullable();
            $table->datetime('actual_start')->nullable();
            $table->datetime('actual_finish')->nullable();
            $table->integer('sla_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('status', 'idx_wo_status');
            $table->index('planned_start', 'idx_wo_planned_start');
            $table->index(['stall_id', 'planned_start'], 'idx_wo_stall_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};