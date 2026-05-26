<!DOCTYPE html>
<html lang="en" x-data="monitor()" x-init="boot()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Service Monitor</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { color-scheme: dark; }
        body { margin:0; background:#0f172a; color:#e2e8f0; font-family: ui-sans-serif, system-ui, -apple-system; }
        .wrap { padding: 16px 20px; }
        table { width:100%; border-collapse:collapse; table-layout:fixed; }
        th, td { border:1px solid #1f2937; padding:10px; text-align:center; vertical-align:middle; }
        /* Warna header diubah ke Merah Auto2000 */
        th { background:#DC2626; color:#fff; font-weight:700; font-size:14px; }
        td { font-size:14px; min-height:64px; }
        .stall { font-weight:700; background:#111827; }
        .plate { font-weight:600; }
        .cell { display:flex; align-items:center; justify-content:center; gap:8px; min-height:64px; }
        .badge { display:inline-block; border-radius:9999px; padding:2px 8px; font-size:12px; font-weight:700; }
        
        /* WARNA TELAH DIUBAH */
        .b-start { background:#DC2626; color:#fff; }       /* In-Progress (Merah Auto2000) */
        .b-wait  { background:#ca8a04; color:#111; }       /* Waiting (Warning - Tetap) */
        .b-qc    { background:#0D6EFD; color:#fff; }       /* QC (Astra Blue) */
        .b-wash  { background:#10b981; color:#111; }       /* Wash (Success - Tetap) */
        .b-final { background:#22c55e; color:#111; }       /* Final (Success - Tetap) */
        .b-ready { background:#16a34a; color:#fff; }       /* Ready (Success - Tetap) */
        
        .small { font-size:12px; opacity:.8; }
        .ts { font-size:12px; opacity:.8; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
        .title { font-weight:800; font-size:20px; letter-spacing:.5px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="title">Monitoring Servis</div>
            <div class="small">Updated: <span x-text="updatedAt"></span></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:120px">Stall</th>
                    <th style="width:140px">No Polisi / Plat</th>
                    <th>Check-in</th>
                    <th>Menunggu Stall Kosong</th>
                    <th>Servis Dimulai (In&nbsp;Progress)</th>
                    <th>Quality Control (QC)</th>
                    <th>Cuci Mobil (Car Wash)</th>
                    <th>Final Inspection / Test Drive</th>
                    <th>Siap Diambil / Selesai</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="r in rows" :key="r.stall">
                    <tr>
                        <td class="stall" x-text="r.stall"></td>
                        <td class="plate" x-text="r.plate ?? '-'"></td>

                        <td>
                            <div class="cell" x-show="r.checkin">
                                <div>
                                    <div class="small" x-text="r.checkin?.wo"></div>
                                    <div class="ts" x-text="r.checkin?.start ? ('Start '+r.checkin.start) : ''"></div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.waiting">
                                <span class="badge b-wait">Waiting</span>
                                <div>
                                    <div class="small" x-text="r.waiting?.wo"></div>
                                    <div class="ts" x-text="r.waiting?.plate"></div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.inprogress">
                                <span class="badge b-start">In-Progress</span>
                                <div>
                                    <div class="small" x-text="r.inprogress?.wo"></div>
                                    <div class="ts" x-text="timeRange(r.inprogress)"></div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.qc">
                                <span class="badge b-qc">QC</span>
                                <div class="small" x-text="r.qc?.wo"></div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.wash">
                                <span class="badge b-wash">Wash</span>
                                <div class="small" x-text="r.wash?.wo"></div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.final">
                                <span class="badge b-final">Final</span>
                                <div class="small" x-text="r.final?.wo"></div>
                            </div>
                        </td>

                        <td>
                            <div class="cell" x-show="r.ready">
                                <span class="badge b-ready">Ready</span>
                                <div class="small" x-text="r.ready?.wo"></div>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <script>
        function monitor(){
            return {
                rows: [],
                updatedAt: '',
                timer: null,
                async load(){
                    const res = await fetch('{{ route('public.monitor.data') }}', {cache:'no-store'});
                    const json = await res.json();
                    this.rows = json.rows ?? [];
                    this.updatedAt = json.updated_at ?? '';
                },
                boot(){
                    this.load();
                    this.timer = setInterval(()=> this.load(), 10000);
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) return;
                        this.load();
                    });
                },
                timeRange(p){ 
                    if(!p) return '';
                    let s = p.start ? p.start : '';
                    let e = p.eta   ? (' → '+p.eta) : '';
                    return (s || e) ? (s+e) : '';
                }
            }
        }
    </script>
</body>
</html>