<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    public function users()
{
    return $this->belongsToMany(\App\Models\User::class, 'user_roles', 'role_id', 'user_id')
        ->withTimestamps();
}

}
