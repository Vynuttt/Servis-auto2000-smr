<?php

namespace App\Models;

// TAMBAHKAN DUA BARIS INI
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// -------------------------

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ... (Komentar @property Anda sudah benar) ...

// TAMBAHKAN 'implements FilamentUser' DI SINI
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'employee_number',
        'address',
        'birthdate',
        'gender',
        'emergency_contact',
        'is_on_duty',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthdate' => 'date',
        'is_active' => 'boolean',
        'is_on_duty' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function mechanicProfile(): HasOne
    {
        return $this->hasOne(MechanicProfile::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'customer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function assignedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'mechanic_id');
    }

    public function assignedWorkOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'mechanic_id');
    }

    public function customerWorkOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'customer_id');
    }

    // ===== ROLE METHODS =====

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists()
            || optional($this->role)->slug === $slug;
    }

    public function hasAnyRole(array $slugs): bool
    {
        if ($this->roles()->whereIn('slug', $slugs)->exists()) {
            return true;
        }
        
        return in_array(optional($this->role)->slug, $slugs, true);
    }

    public function isAdminOrOwner(): bool
    {
        return $this->hasAnyRole(['admin', 'owner']);
    }

    public function isMechanic(): bool
    {
        return $this->hasRole('mechanic');
    }

    // ===== QUERY SCOPES =====

    public function scopeHasRole(Builder $query, string $slug): Builder
    {
        return $query->whereHas('roles', fn($q) => $q->where('slug', $slug));
    }

    public function scopeHasAnyRole(Builder $query, array $slugs): Builder
    {
        return $query->whereHas('roles', fn($q) => $q->whereIn('slug', $slugs));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeOnDuty(Builder $query): Builder
    {
        return $query->where('is_on_duty', true);
    }


    // ✅ TAMBAHKAN METHOD PENTING DI BAWAH INI
    // Saya tambahkan backslash (\) di depan \Filament\Panel untuk memperbaiki error Intelephense
    public function canAccessPanel(Panel $panel): bool
    {
         if ($panel->getId() === 'admin') {
            // Izinkan Admin, Owner, ATAU Mekanik
            return $this->isAdminOrOwner() || $this->isMechanic();
        }
        return false;
    }
}

