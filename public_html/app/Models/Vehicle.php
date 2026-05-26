<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'color',
        'vin',
        'engine_number',
        'transmission',
        'fuel_type',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'vehicle_id');
    }

    // ===== HELPER METHODS =====

    /**
     * Find or create vehicle by plate number
     */
    public static function findOrCreateByPlate(string $plateNumber, ?int $customerId = null, array $additionalData = []): self
    {
        // Normalize plate number (uppercase, remove spaces)
        $plateNumber = strtoupper(str_replace(' ', '', $plateNumber));

        // Cari vehicle existing
        $vehicle = static::where('plate_number', $plateNumber)->first();

        if ($vehicle) {
            // Update customer_id jika kosong dan ada customerId baru
            if (!$vehicle->customer_id && $customerId) {
                $vehicle->update(['customer_id' => $customerId]);
            }
            return $vehicle;
        }

        // Create new vehicle
        return static::create(array_merge([
            'customer_id' => $customerId,
            'plate_number' => $plateNumber,
            'is_active' => true,
        ], $additionalData));
    }

    /**
     * Get full name display
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->brand,
            $this->model,
            $this->year,
        ]);

        return implode(' ', $parts) ?: 'Unknown Vehicle';
    }

    /**
     * Get display name with plate
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->plate_number} - {$this->full_name}";
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}