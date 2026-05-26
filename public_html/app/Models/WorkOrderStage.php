<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkOrderStage extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','position','is_final'];

    public $timestamps = true;

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'current_stage_id');
    }

    /** Scope urut by position */
    public function scopeOrdered($q)
    {
        return $q->orderBy('position');
    }
}
