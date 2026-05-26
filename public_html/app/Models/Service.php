<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Jika tabel services tidak punya timestamps
    public $timestamps = false;

    // ===== RELATIONSHIPS =====

    /**
     * Relasi many-to-many dengan bookings via pivot
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_services', 'service_id', 'booking_id')
            ->withPivot(['qty', 'price', 'subtotal'])
            ->withTimestamps();
    }

    // ===== HELPER METHODS =====

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)($this->base_price ?? 0), 0, ',', '.');
    }

    /**
     * Check if service is available
     */
    public function isAvailable(): bool
    {
        return $this->is_active === true;
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit($limit);
    }
}