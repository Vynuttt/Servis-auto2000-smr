<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    /**
     * Generate booking code harian: BK-YYYYMMDD-####
     */
    protected function makeBookingCode(): string
    {
        $prefix = 'BK-' . now()->format('Ymd') . '-';

        // Ambil kode terakhir di hari ini, lalu naikkan seq
        $last = Booking::whereDate('created_at', now()->toDateString())
            ->where('booking_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('booking_code');

        $seq = 1;
        if ($last && preg_match('/-(\d{4})$/', (string) $last, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] ??= 'Booked';
        // seed dulu kode (nanti bisa di-regenerate kalau bentur unique)
        $data['booking_code'] = $this->makeBookingCode();

        return $data;
    }

    /**
     * Create dengan retry kecil kalau bentur unique booking_code (1062).
     * Tetap lempar ValidationException untuk benturan slot jadwal.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return static::getModel()::create($data);
            } catch (QueryException $e) {
                $sqlState   = $e->errorInfo[0] ?? null;      // '23000' untuk integrity constraint
                $driverCode = (int)($e->errorInfo[1] ?? 0);  // 1062 untuk duplicate entry (MySQL)
                $msg        = (string)($e->errorInfo[2] ?? '');

                if ($sqlState === '23000' && $driverCode === 1062) {
                    // 1) Benturan index unik booking_code → regenerate & retry.
                    if (str_contains($msg, 'unique_booking_code') || str_contains($msg, 'booking_code')) {
                        // bikin kode baru & coba lagi
                        $data['booking_code'] = $this->makeBookingCode();
                        continue;
                    }

                    // 2) Benturan unik jadwal (vehicle_id+date+time) → validasi form
                    if (str_contains($msg, 'vehicle_date_time_unique')
                        || str_contains($msg, 'bookings_vehicle_id_booking_date_booking_time_unique')) {
                        throw ValidationException::withMessages([
                            'booking_time' => 'Slot waktu ini sudah terpakai untuk kendaraan & tanggal tersebut.',
                        ]);
                    }
                }

                // Unknown error → lempar lagi biar ketahuan
                throw $e;
            }
        }

        // Kalau sampai sini, regenerasi gagal terus (sangat jarang)
        throw ValidationException::withMessages([
            'booking_code' => 'Gagal membuat kode booking unik. Coba simpan lagi.',
        ]);
    }
}
