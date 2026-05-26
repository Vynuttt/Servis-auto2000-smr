<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory;

    // ===== ENUM STATUS =====
    public const S_PLANNED     = 'Planned';
    public const S_CHECKED_IN  = 'Checked-In';
    public const S_WAITING     = 'Waiting';
    public const S_IN_PROGRESS = 'In-Progress';
    public const S_QC          = 'QC';
    public const S_WASH        = 'Wash';
    public const S_FINAL       = 'Final';
    public const S_DONE        = 'Done';
    public const S_CANCELLED   = 'Cancelled';

    protected $fillable = [
        'wo_number',
        'booking_id',
        'customer_id',
        'vehicle_id',
        'stall_id',
        'mechanic_id',
        'planned_start',
        'planned_finish',
        'actual_start',
        'actual_finish',
        'sla_minutes',
        'priority',
        'status',
        'notes',
        'current_stage_id',
    ];

    protected $casts = [
        'planned_start'  => 'datetime',
        'planned_finish' => 'datetime',
        'actual_start'   => 'datetime',
        'actual_finish'  => 'datetime',
    ];

    // ===== LIFECYCLE HOOKS =====

    /**
     * Hook: sinkron stage & actual times saat status berubah.
     */
    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if ($model->isDirty('status')) {
                // Sinkron stage
                $model->current_stage_id = $model->stageIdForStatus($model->status);

                // Auto waktu aktual
                if ($model->status === self::S_IN_PROGRESS && is_null($model->actual_start)) {
                    $model->actual_start = now();
                }
                if ($model->status === self::S_DONE && is_null($model->actual_finish)) {
                    $model->actual_finish = now();
                }
            }
        });
    }

    // ===== STATIC FACTORY METHOD =====

    /**
     * Create Work Order from Booking.
     */
    public static function createFromBooking(Booking $booking): self
    {
        // Cek apakah sudah ada WO untuk booking ini
        if ($booking->workOrder) {
            throw new \Exception("Work Order already exists: {$booking->workOrder->wo_number}");
        }

        // Parse scheduled_at untuk planned_start
        $plannedStart = $booking->scheduled_at
            ?? \Carbon\Carbon::parse("{$booking->booking_date} {$booking->booking_time}");

        // Hitung planned_finish berdasarkan SLA
        $slaMinutes = $booking->sla_minutes ?? 120; // default 2 jam
        $plannedFinish = $plannedStart->copy()->addMinutes($slaMinutes);

        // Create WO
        $wo = static::create([
            'booking_id'     => $booking->id,
            'customer_id'    => $booking->customer_id,
            'vehicle_id'     => $booking->vehicle_id,
            'mechanic_id'    => $booking->mechanic_id,
            'planned_start'  => $plannedStart,
            'planned_finish' => $plannedFinish,
            'sla_minutes'    => $slaMinutes,
            'status'         => self::S_PLANNED,
            'priority'       => 'Regular',
            'notes'          => $booking->complaint_note,
        ]);

        // Update booking status
        $booking->update(['status' => 'Checked-In']);

        // Log creation
        WorkOrderLog::create([
            'work_order_id' => $wo->id,
            'user_id'       => Auth::id(),
            'action'        => 'created',
            'old_value'     => null,
            'new_value'     => self::S_PLANNED,
            'notes'         => "Work Order created from Booking {$booking->booking_code}",
        ]);

        return $wo;
    }

    // ===== RELATIONS =====

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function stall()
    {
        return $this->belongsTo(Stall::class);
    }

    public function currentStage()
    {
        return $this->belongsTo(WorkOrderStage::class, 'current_stage_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkOrderLog::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WorkOrderApproval::class);
    }

    // ===== LOGGING =====

    /**
     * Tulis log yang SELALU mengisi kolom 'stage' (wajib).
     */
    public function addLog(string $stage, ?string $remarks = null): void
    {
        $this->logs()->create([
            'stage'      => $stage,
            'started_at' => now(),
            'by_user_id' => Auth::id(),
            'remarks'    => $remarks,
        ]);
    }

    // ===== STATE TRANSITIONS =====

    protected function allowedTransitions(): array
    {
        return [
            self::S_PLANNED     => [self::S_CHECKED_IN, self::S_IN_PROGRESS, self::S_WAITING],
            self::S_CHECKED_IN  => [self::S_WAITING, self::S_IN_PROGRESS],
            self::S_WAITING     => [self::S_IN_PROGRESS],
            self::S_IN_PROGRESS => [self::S_WAITING, self::S_QC, self::S_WASH, self::S_FINAL, self::S_DONE],
            self::S_QC          => [self::S_WAITING, self::S_WASH, self::S_FINAL, self::S_DONE],
            self::S_WASH        => [self::S_WAITING, self::S_FINAL, self::S_DONE],
            self::S_FINAL       => [self::S_WAITING, self::S_DONE],
            self::S_DONE        => [],
            self::S_CANCELLED   => [],
        ];
    }

    public function canTransition(string $to): bool
    {
        $from = $this->status;
        $map  = $this->allowedTransitions();
        return isset($map[$from]) && in_array($to, $map[$from], true);
    }

    /**
     * Map STATUS -> stage_id (slug pada work_order_stages).
     */
    protected function stageIdForStatus(string $status): ?int
    {
        static $bySlug = null;

        if ($bySlug === null) {
            $bySlug = WorkOrderStage::query()->pluck('id', 'slug')->toArray();
        }

        $mapStatusToSlug = [
            self::S_PLANNED     => 'check_in',
            self::S_CHECKED_IN  => 'check_in',
            self::S_WAITING     => 'waiting_stall',
            self::S_IN_PROGRESS => 'in_progress',
            self::S_QC          => 'qc',
            self::S_WASH        => 'car_wash',
            self::S_FINAL       => 'final_check',
            self::S_DONE        => 'ready',
            self::S_CANCELLED   => null,
        ];

        $slug = $mapStatusToSlug[$status] ?? null;

        return $slug && isset($bySlug[$slug]) ? $bySlug[$slug] : null;
    }

    /**
     * Ubah status + sinkron stage + tulis log.
     */
    public function transitionTo(string $to, ?string $remarks = null): void
    {
        if (!$this->canTransition($to)) {
            throw new \DomainException("Illegal transition from {$this->status} to {$to}");
        }

        $updates = ['status' => $to];

        if ($to === self::S_IN_PROGRESS && is_null($this->actual_start)) {
            $updates['actual_start'] = now();
        }
        if ($to === self::S_DONE && is_null($this->actual_finish)) {
            $updates['actual_finish'] = now();
        }

        $updates['current_stage_id'] = $this->stageIdForStatus($to);

        $this->fill($updates)->save();

        // LOG: stage selalu diisi dengan status terkini
        $this->addLog($to, $remarks);

        if (class_exists(\App\Events\WorkOrderStatusChanged::class)) {
            event(new \App\Events\WorkOrderStatusChanged($this));
        }
    }

    // ===== PROGRESS CALCULATION =====

    public function progressPct(): int
    {
        return match ($this->status) {
            self::S_PLANNED     => 0,
            self::S_CHECKED_IN  => 10,
            self::S_WAITING     => 15,
            self::S_IN_PROGRESS => 50,
            self::S_QC          => 75,
            self::S_WASH        => 85,
            self::S_FINAL       => 95,
            self::S_DONE        => 100,
            self::S_CANCELLED   => 0,
            default             => 0,
        };
    }

    // ===== REQUEST–APPROVAL FLOW =====

    /**
     * Ada request pending? Kembalikan status yang diminta (atau null).
     */
    public function pendingRequestedStatus(): ?string
    {
        $p = $this->approvals()->where('status', 'pending')->latest('id')->first();
        return $p?->requested_status;
    }

    /**
     * Mekanik mengajukan perubahan status (tanpa mengubah status WO).
     */
    public function requestTransition(string $targetStatus, string $requestLabel, ?string $note = null): void
    {
        // Cegah duplikat jika masih ada pending
        if ($this->pendingRequestedStatus()) {
            return;
        }

        $map = [
            self::S_IN_PROGRESS => 'start',
            self::S_WAITING     => 'hold',
            self::S_QC          => 'qc',
            self::S_WASH        => 'wash',
            self::S_FINAL       => 'final',
            self::S_DONE        => 'done',
        ];

        $this->approvals()->create([
            'mechanic_id'      => Auth::id(),
            'request_type'     => $map[$targetStatus] ?? 'other',
            'requested_status' => $targetStatus,
            'description'      => $note,
            'status'           => 'pending',
        ]);

        // LOG: catat permintaan—stage diisi status saat ini agar tidak null
        $this->addLog(
            $this->status,
            "Requested: {$requestLabel} → {$targetStatus}" . ($note ? " ({$note})" : '')
        );
    }

    /**
     * Admin menyetujui: update status WO + tutup approval + tulis log.
     */
    public function approveRequest(WorkOrderApproval $approval, int $adminId, ?string $adminNote = null): void
    {
        // Ubah status via API resmi supaya times & stage ikut
        $this->transitionTo(
            $approval->requested_status,
            "Approved by #{$adminId}" . ($adminNote ? " ({$adminNote})" : '')
        );

        // Tutup approval
        $approval->update([
            'status'      => 'approved',
            'approved_by' => $adminId,
            'admin_note'  => $adminNote,
        ]);

        // (Opsional) tambahan log ringkas; stage selalu diisi status terkini
        $this->addLog(
            $this->status,
            "Approved: {$approval->request_type} → {$approval->requested_status}"
        );
    }

    /**
     * Admin menolak: tutup approval + log.
     */
    public function rejectRequest(WorkOrderApproval $approval, int $adminId, string $adminNote): void
    {
        $approval->update([
            'status'      => 'rejected',
            'approved_by' => $adminId,
            'admin_note'  => $adminNote,
        ]);

        // LOG: tetap isi stage dengan status saat ini
        $this->addLog(
            $this->status,
            "Rejected: {$approval->request_type}" . ($adminNote ? " ({$adminNote})" : '')
        );
    }
}