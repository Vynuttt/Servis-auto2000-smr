<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderApproval extends Model
{
    protected $table = 'work_order_approvals';

    protected $fillable = [
        'work_order_id',
        'mechanic_id',
        'request_type',
        'requested_status',
        'description',
        'status',
        'admin_note',
        'approved_by',
    ];

    // RELATIONS
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
