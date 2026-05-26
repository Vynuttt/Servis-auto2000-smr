<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number','work_order_id','subtotal_services','subtotal_parts',
        'discount_total','tax','grand_total','status','issued_at','due_at'
    ];

    protected $casts = [
        'subtotal_services' => 'decimal:2',
        'subtotal_parts'    => 'decimal:2',
        'discount_total'    => 'decimal:2',
        'tax'               => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'issued_at'         => 'datetime',
        'due_at'            => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
