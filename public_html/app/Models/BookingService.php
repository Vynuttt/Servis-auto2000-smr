<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingService extends Model
{
    use HasFactory;

    protected $table = 'booking_services';
    
    protected $fillable = [
        'booking_id',
        'service_id',
        'qty',
        'price',
        // subtotal is GENERATED ALWAYS column, tidak perlu di fillable
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Timestamps bisa diaktifkan jika tabel punya created_at/updated_at
    public $timestamps = true;

    // ===== RELATIONSHIPS =====

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ===== HELPER METHODS =====

    /**
     * Calculate subtotal (qty * price)
     * Note: Ini sebenarnya sudah auto-calculated di database via GENERATED column
     */
    public function calculateSubtotal(): float
    {
        return (float)$this->qty * (float)$this->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->subtotal, 0, ',', '.');
    }
}