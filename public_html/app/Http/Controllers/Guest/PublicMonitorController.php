<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Stall;

class PublicMonitorController extends Controller
{
    public function index()
    {
        // halaman TV (blade saja, data pakai AJAX)
        return view('public.monitor');
    }

    public function data()
    {
        // Ambil stall + WO aktif (bukan Done/Cancelled)
        $stalls = Stall::query()
            ->where('is_active', 1)
            ->orderBy('code')
            ->get(['id','code']);

        $activeWos = WorkOrder::query()
            ->with(['vehicle:id,plate_number,model', 'customer:id,name'])
            ->whereNotIn('status', ['Done','Cancelled'])
            ->get([
                'id','wo_number','stall_id','status','vehicle_id','customer_id',
                'planned_start','planned_finish','actual_start'
            ]);

        // Kelompokkan per stall, lalu petakan ke kolom status
        $statusCols = [
            'Checked-In'   => 'checkin',
            'Waiting'      => 'waiting',
            'In-Progress'  => 'inprogress',
            'QC'           => 'qc',
            'Wash'         => 'wash',
            'Final'        => 'final',
        ];

        $rows = [];
        foreach ($stalls as $s) {
            $rows[$s->id] = [
                'stall'      => $s->code,
                'plate'      => null,          // kolom "No pol/Plat" (opsional)
                'checkin'    => null,
                'waiting'    => null,
                'inprogress' => null,
                'qc'         => null,
                'wash'       => null,
                'final'      => null,
                'ready'      => null,
            ];
        }

        foreach ($activeWos as $wo) {
            $sid = $wo->stall_id;
            if (!isset($rows[$sid])) continue;

            $payload = [
                'wo'      => $wo->wo_number,
                'plate'   => optional($wo->vehicle)->plate_number,
                'model'   => optional($wo->vehicle)->model,
                'cust'    => optional($wo->customer)->name,
                'start'   => optional($wo->actual_start)?->format('H:i'),
                'eta'     => optional($wo->planned_finish)?->format('H:i'),
            ];

            // Tampilkan plat di kolom pertama kalau ada
            if (!$rows[$sid]['plate'] && $payload['plate']) {
                $rows[$sid]['plate'] = $payload['plate'];
            }

            $key = $statusCols[$wo->status] ?? null;
            if ($key) {
                $rows[$sid][$key] = $payload;
            }
            if ($wo->status === 'Done') {
                $rows[$sid]['ready'] = $payload;
            }
        }

        return response()->json([
            'updated_at' => now()->toDateTimeString(),
            'rows'       => array_values($rows), // array numerik utk front-end
        ]);
    }
}