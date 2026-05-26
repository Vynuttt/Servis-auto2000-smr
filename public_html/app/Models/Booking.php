<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne, BelongsToMany};
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'tracking_code',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'vehicle_id',
        'vehicle_plate',
        'vehicle_model',
        'mechanic_id',
        'booking_date',
        'booking_time',
        'scheduled_at',
        'service_type',
        'complaint_note',
        'notes',
        'status',
        'source_channel',
        'sla_minutes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'scheduled_at' => 'datetime',
        'sla_minutes' => 'integer',
    ];

    // ===== RELATIONSHIPS =====
    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class, 'booking_id');
    }

    /**
     * Relasi many-to-many ke services via pivot booking_services
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_services', 'booking_id', 'service_id')
            ->withPivot(['qty', 'price', 'subtotal'])
            ->withTimestamps();
    }

    /**
     * Alternatif: akses pivot langsung
     */
    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }

    // ===== HELPER METHODS =====

    /**
     * Generate unique booking code: BK-YYYYMMDD-####
     */
    public static function generateBookingCode(): string
    {
        $prefix = 'BK-' . now()->format('Ymd') . '-';
        
        $lastBooking = static::whereDate('created_at', now()->toDateString())
            ->where('booking_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('booking_code');

        $seq = 1;
        if ($lastBooking && preg_match('/-(\d{4})$/', $lastBooking, $matches)) {
            $seq = (int)$matches[1] + 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate random tracking code (8 chars alphanumeric)
     */
    public static function generateTrackingCode(): string
    {
        return strtoupper(\Illuminate\Support\Str::random(8));
    }

    /**
     * Create Work Order from this booking
     */
    public function createWorkOrder(): WorkOrder
    {
        // Cek apakah sudah ada WO
        if ($this->workOrder) {
            throw new \Exception("Work Order sudah dibuat untuk booking ini: {$this->workOrder->wo_number}");
        }

        // Parse scheduled_at untuk planned_start
        $plannedStart = $this->scheduled_at ?? Carbon::parse("{$this->booking_date} {$this->booking_time}");
        
        // Hitung planned_finish berdasarkan SLA
        $slaMinutes = $this->sla_minutes ?? 120; // default 2 jam
        $plannedFinish = $plannedStart->copy()->addMinutes($slaMinutes);

        // Create WO
        $wo = WorkOrder::create([
            'booking_id' => $this->id,
            'customer_id' => $this->customer_id,
            'vehicle_id' => $this->vehicle_id,
            'mechanic_id' => $this->mechanic_id,
            'planned_start' => $plannedStart,
            'planned_finish' => $plannedFinish,
            'sla_minutes' => $slaMinutes,
            'status' => WorkOrder::S_PLANNED,
            'priority' => 'Regular',
            'notes' => $this->complaint_note,
        ]);

        // Update booking status
        $this->update(['status' => 'Checked-In']);

        return $wo;
    }

    /**
     * Get total price dari services
     */
    public function getTotalPriceAttribute(): float
    {
        return (float)($this->bookingServices()->sum('subtotal') ?? 0);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['Booked', 'Confirmed']) 
            && !$this->workOrder;
    }

    /**
     * Cancel booking
     */
    public function cancel(?string $reason = null): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Booking tidak dapat dibatalkan');
        }

        $this->update([
            'status' => 'Cancelled',
            'notes' => ($this->notes ?? '') . "\n[Cancelled] " . ($reason ?? 'No reason provided'),
        ]);
    }

    // ===== SCOPES =====

    public function scopeBooked($query)
    {
        return $query->where('status', 'Booked');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
            ->whereIn('status', ['Booked', 'Confirmed']);
    }
}