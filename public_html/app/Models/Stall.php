<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stall extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'stall_id');
    }

    // ===== HELPER METHODS =====

    /**
     * Get active work orders
     */
    public function activeWorkOrders(): HasMany
    {
        return $this->workOrders()->whereIn('status', [
            'Checked-In', 'Waiting', 'In-Progress', 'QC', 'Wash', 'Final'
        ]);
    }

    /**
     * Check if stall is available
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->activeWorkOrders()->count() === 0;
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? "Stall {$this->code}";
    }

    // ===== SCOPES =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->whereDoesntHave('workOrders', function($q) {
                $q->whereIn('status', ['Checked-In', 'Waiting', 'In-Progress', 'QC', 'Wash', 'Final']);
            });
    }
}