<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MechanicProfile extends Model
{
    protected $table = 'mechanic_profiles';

    protected $fillable = [
        'user_id','employee_code','specialization','shift','is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
