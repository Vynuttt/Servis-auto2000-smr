<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('booking_code', 30)->unique();
            $table->char('tracking_code', 8)->nullable()->unique();

            // customers / users
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email', 120)->nullable();
            $table->string('customer_phone', 30)->nullable();

            // vehicles
            $table->unsignedBigInteger('vehicle_id');
            $table->string('vehicle_plate', 20)->nullable();
            $table->string('vehicle_model', 60)->nullable();

            // mechanic
            $table->unsignedBigInteger('mechanic_id')->nullable();

            // booking times
            $table->date('booking_date');
            $table->time('booking_time');
            $table->dateTime('scheduled_at')->nullable();

            // channel & SLA
            $table->enum('source_channel', ['Web', 'Walk-in', 'Phone', 'WhatsApp'])->default('Web');
            $table->integer('sla_minutes')->nullable()->default(120);

            // service info
            $table->string('service_type', 100)->nullable()->default('General Service');
            $table->text('notes')->nullable();
            $table->text('complaint_note')->nullable();

            $table->enum('status', [
                'Booked',
                'Checked-In',
                'In-Service',
                'Ready',
                'Completed',
                'Cancelled',
                'No-Show',
                'Converted',
            ])->default('Booked');

            // kolom work_order_id TANPA foreign key dulu
            $table->unsignedBigInteger('work_order_id')->nullable();

            $table->timestamps();

            // foreign key lain (kalau ini tidak error boleh kamu biarkan)
            $table->foreign('customer_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('mechanic_id')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('vehicle_id')
                ->references('id')->on('vehicles')
                ->onDelete('cascade');

            // >>> HAPUS / KOMENTARI BAGIAN INI <<<
            // $table->foreign('work_order_id')
            //     ->references('id')->on('work_orders')
            //     ->onDelete('set null');

            // unique constraints
            $table->unique(['vehicle_id', 'booking_date', 'booking_time'], 'vehicle_date_time_unique');
            $table->unique(['mechanic_id', 'booking_date', 'booking_time'], 'mechanic_date_time_unique');

            // indexes
            $table->index('scheduled_at', 'idx_bookings_scheduled_at');
            $table->index('customer_id', 'idx_bookings_customer_id');
            $table->index('mechanic_id', 'idx_bookings_mechanic_id');
            $table->index('work_order_id', 'idx_bookings_wo');
            $table->index(['customer_id', 'booking_date'], 'idx_booking_customer_date');
            $table->index(['vehicle_id', 'booking_date'], 'idx_booking_vehicle_date');
            $table->index(['status', 'booking_date'], 'idx_booking_status_date');
            $table->index(['tracking_code', 'customer_email'], 'idx_booking_tracking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
