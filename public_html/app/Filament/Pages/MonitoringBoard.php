<?php

namespace App\Filament\Pages;

use App\Models\WorkOrder;
use App\Models\Stall;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\WorkOrderResource;

class MonitoringBoard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'Monitoring';
    protected static ?string $title           = 'Monitoring Board';
    protected static string $view             = 'filament.pages.monitoring-board';

    /** Hanya admin/owner yang bisa lihat */
    public static function shouldRegisterNavigation(): bool
    {
        $u = Auth::user();
        if (! $u) return false;

        /** @var \App\Models\User $u */
        return method_exists($u, 'isAdminOrOwner') ? $u->isAdminOrOwner() : true;
    }

    // ====== STATE FILTER ======
    public string $date;            // YYYY-MM-DD
    public string $startTime;       // HH:mm
    public string $endTime;         // HH:mm
    public ?int $mechanicId = null; // opsional
    public bool $showFilters = true;

    // polling interval (detik)
    public int $pollSeconds = 10;

    public function mount(): void
    {
        $today = now()->toDateString();
        $this->date      = $today;
        $this->startTime = '08:00';
        $this->endTime   = '17:00';
    }

    // ====== HELPERS DATA ======
    public function getWindow(): array
    {
        $start = Carbon::parse("{$this->date} {$this->startTime}");
        $end   = Carbon::parse("{$this->date} {$this->endTime}");

        if ($end->lte($start)) {
            $end = $start->copy()->addHours(9);
        }

        $minutes = max(1, $start->diffInMinutes($end));
        return compact('start', 'end', 'minutes');
    }

    /** Ticks tiap 30 menit */
    public function getTimeTicks(): array
    {
        $w = $this->getWindow();
        $ticks = [];
        $c = $w['start']->copy();
        while ($c->lte($w['end'])) {
            $ticks[] = $c->copy();
            $c->addMinutes(30);
        }
        return $ticks;
    }

    /** KPI */
    public function getKpis(): array
    {
        $qBase = WorkOrder::query();

        $active = (clone $qBase)
            ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED])
            ->count();

        $doneToday = (clone $qBase)
            ->where('status', WorkOrder::S_DONE)
            ->whereDate('actual_finish', $this->date)
            ->count();

        $overdue = (clone $qBase)
            ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED])
            ->where('planned_finish', '<', now())
            ->count();

        $w = $this->getWindow();
        $stallsCount = max(1, Stall::count());
        $totalWindowMins = $stallsCount * $w['minutes'];

        $scheduledMins = (clone $qBase)
            ->whereNotIn('status', [WorkOrder::S_CANCELLED])
            ->whereDate('planned_start', $this->date)
            ->get()
            ->sum(function (WorkOrder $wo) {
                $s = $wo->planned_start;
                $e = $wo->planned_finish ?? $s->copy()->addMinutes((int)($wo->sla_minutes ?: 60));
                return max(0, $s->diffInMinutes($e));
            });

        $util = $totalWindowMins > 0 ? round(($scheduledMins / $totalWindowMins) * 100) : 0;

        return [
            'active'     => $active,
            'done_today' => $doneToday,
            'overdue'    => $overdue,
            'util'       => $util,
        ];
    }

    public function getStalls()
    {
        return Stall::orderBy('name')->get(['id', 'name']);
    }

    public function getActiveWorkOrders()
    {
        $w = $this->getWindow();

        $q = WorkOrder::with(['stall:id,name', 'mechanic:id,name', 'customer:id,name', 'vehicle:id,plate_number'])
            ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED]);

        if ($this->mechanicId) {
            $q->where('mechanic_id', $this->mechanicId);
        }

        // Overlap dengan window
        $q->where(function ($qq) use ($w) {
            $qq->where('planned_start', '<', $w['end'])
               ->where(function ($qq2) use ($w) {
                   $qq2->where('planned_finish', '>', $w['start'])
                       ->orWhereNull('planned_finish');
               });
        });

        return $q->get();
    }

    // ====== UTIL UNTUK VIEW ======
    public function statusColor(string $status): string
    {
        return match ($status) {
            WorkOrder::S_IN_PROGRESS => 'bg-blue-500',
            WorkOrder::S_WAITING     => 'bg-amber-500',
            WorkOrder::S_QC          => 'bg-cyan-600',
            WorkOrder::S_WASH        => 'bg-cyan-700',
            WorkOrder::S_FINAL       => 'bg-emerald-600',
            WorkOrder::S_DONE        => 'bg-emerald-700',
            WorkOrder::S_CANCELLED   => 'bg-gray-500',
            WorkOrder::S_CHECKED_IN  => 'bg-slate-500',
            WorkOrder::S_PLANNED     => 'bg-indigo-500',
            default                  => 'bg-slate-400',
        };
    }

    /** Legend untuk warna status */
    public function getLegend(): array
    {
        return [
            ['label' => 'Planned',     'class' => $this->statusColor(WorkOrder::S_PLANNED)],
            ['label' => 'Checked-In',  'class' => $this->statusColor(WorkOrder::S_CHECKED_IN)],
            ['label' => 'Waiting',     'class' => $this->statusColor(WorkOrder::S_WAITING)],
            ['label' => 'In-Progress', 'class' => $this->statusColor(WorkOrder::S_IN_PROGRESS)],
            ['label' => 'QC',          'class' => $this->statusColor(WorkOrder::S_QC)],
            ['label' => 'Wash',        'class' => $this->statusColor(WorkOrder::S_WASH)],
            ['label' => 'Final',       'class' => $this->statusColor(WorkOrder::S_FINAL)],
            ['label' => 'Done',        'class' => $this->statusColor(WorkOrder::S_DONE)],
        ];
    }

    /** hitung style left/width (%) kartu WO relatif terhadap window */
    public function computeBlockStyle(WorkOrder $wo): array
    {
        $w = $this->getWindow();
        $s = $wo->planned_start ?? $w['start'];
        $e = $wo->planned_finish ?? $s->copy()->addMinutes((int)($wo->sla_minutes ?: 60));

        // clamp ke window
        if ($s->lt($w['start'])) $s = $w['start']->copy();
        if ($e->gt($w['end']))   $e = $w['end']->copy();

        $total = max(1, $w['minutes']); // menit window
        $left  = max(0, $w['start']->diffInMinutes($s));
        $span  = max(1, $s->diffInMinutes($e));

        $leftPct  = min(100, round(($left / $total) * 100, 2));
        $widthPct = min(100 - $leftPct, round(($span / $total) * 100, 2));

        return ['left' => $leftPct, 'width' => $widthPct];
    }

    public function hasRequest(WorkOrder $wo): ?string
    {
        return $wo->pendingRequestedStatus();
    }

    /** Link ke halaman edit WO (Filament resource) */
    public function woEditUrl(WorkOrder $wo): ?string
    {
        return class_exists(WorkOrderResource::class)
            ? WorkOrderResource::getUrl('edit', ['record' => $wo])
            : null;
    }
}
    