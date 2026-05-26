<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Work Order - {{ $workOrder->wo_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #e74c3c;
            padding-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .logo {
            display: table-cell;
            width: 150px;
            vertical-align: middle;
        }
        .logo img {
            max-width: 120px;
            height: auto;
        }
        .company-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .company-info h1 {
            color: #e74c3c;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #666;
            font-size: 11px;
        }
        .document-title {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
        }
        .document-title h2 {
            color: #e74c3c;
            font-size: 20px;
            margin-bottom: 5px;
        }
        .document-title p {
            color: #666;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-section h3 {
            background: #e74c3c;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            padding: 6px 12px;
            font-weight: bold;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .info-value {
            display: table-cell;
            padding: 6px 12px;
            border: 1px solid #dee2e6;
        }
        .services-table, .logs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .services-table th, .logs-table th {
            background: #e74c3c;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .services-table td, .logs-table td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        .services-table tr:nth-child(even),
        .logs-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .badge-planned { background: #6c757d; }
        .badge-checked-in { background: #17a2b8; }
        .badge-waiting { background: #ffc107; color: #000; }
        .badge-in-progress { background: #007bff; }
        .badge-qc { background: #6f42c1; }
        .badge-wash { background: #20c997; }
        .badge-final { background: #28a745; }
        .badge-done { background: #28a745; }
        .badge-cancelled { background: #6c757d; }
        .badge-urgent { background: #dc3545; }
        .badge-rework { background: #fd7e14; }
        .badge-regular { background: #007bff; }
        .progress-bar {
            background: #f8f9fa;
            height: 25px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e74c3c;
        }
        .progress-fill {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
            height: 100%;
            text-align: center;
            color: white;
            font-size: 11px;
            line-height: 25px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .timeline {
            margin-top: 15px;
        }
        .timeline-item {
            padding: 10px 10px 10px 15px;
            border-left: 3px solid #e74c3c;
            margin-left: 20px;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .timeline-item-time {
            font-size: 10px;
            color: #666;
            margin-bottom: 4px;
        }
        .timeline-item-user {
            font-weight: bold;
            margin-bottom: 4px;
            color: #e74c3c;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-box {
            display: inline-block;
            background: #f8f9fa;
            border: 2px solid #e74c3c;
            padding: 15px 25px;
            min-width: 300px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .total-label {
            display: table-cell;
            font-weight: bold;
            padding-right: 20px;
        }
        .total-value {
            display: table-cell;
            text-align: right;
        }
        .grand-total {
            border-top: 2px solid #e74c3c;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 16px;
            color: #e74c3c;
        }
        .booking-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px;
            margin-top: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="header-content">
            <div class="logo">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Auto2000 Logo">
                @endif
            </div>
            <div class="company-info">
                <h1>AUTO 2000</h1>
                <p>Work Order Management System</p>
                <p>Jl. K.H. Wahid Hasyim No. 99, Sempaja, Samarinda</p>
                <p>Telp: (0541) 742000 | Email: info@auto2000.co.id</p>
            </div>
        </div>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        <h2>WORK ORDER DETAIL</h2>
        <p>{{ $workOrder->wo_number }}</p>
    </div>

    {{-- Work Order Information --}}
    <div class="info-section">
        <h3>Informasi Work Order</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">WO Number:</div>
                <div class="info-value"><strong>{{ $workOrder->wo_number }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Booking Code:</div>
                <div class="info-value">{{ $workOrder->booking->code ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $workOrder->status)) }}">
                        {{ $workOrder->status }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Priority:</div>
                <div class="info-value">
                    <span class="badge badge-{{ strtolower($workOrder->priority) }}">
                        {{ $workOrder->priority }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Mechanic:</div>
                <div class="info-value">{{ $workOrder->mechanic->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Stall:</div>
                <div class="info-value">{{ $workOrder->stall->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Current Stage:</div>
                <div class="info-value">{{ $workOrder->currentStage->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Planned Start:</div>
                <div class="info-value">{{ $workOrder->planned_start ? $workOrder->planned_start->format('d M Y H:i') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Planned Finish:</div>
                <div class="info-value">{{ $workOrder->planned_finish ? $workOrder->planned_finish->format('d M Y H:i') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created:</div>
                <div class="info-value">{{ $workOrder->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>

        {{-- Progress Bar --}}
        @if(method_exists($workOrder, 'progressPct'))
        <div style="margin-top: 15px;">
            <strong>Progress:</strong>
            <div class="progress-bar" style="margin-top: 8px;">
                <div class="progress-fill" style="width: {{ $workOrder->progressPct() }}%">
                    {{ $workOrder->progressPct() }}%
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Customer & Vehicle Information --}}
    <div class="info-section">
        <h3>Informasi Customer & Kendaraan</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value">{{ $workOrder->booking->customer->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $workOrder->booking->customer->email ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $workOrder->booking->customer->phone ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Plate Number:</div>
                <div class="info-value"><strong>{{ $workOrder->booking->vehicle->plate_number ?? '-' }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Brand:</div>
                <div class="info-value">{{ $workOrder->booking->vehicle->brand ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Model:</div>
                <div class="info-value">{{ $workOrder->booking->vehicle->model ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Year:</div>
                <div class="info-value">{{ $workOrder->booking->vehicle->year ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <div class="info-section">
        <h3>Layanan yang Dikerjakan</h3>
        <table class="services-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Service Code</th>
                    <th>Service Name</th>
                    <th style="width: 60px;" class="text-center">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->booking->bookingServices as $index => $bs)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $bs->service->code ?? '-' }}</td>
                    <td>{{ $bs->service->name ?? '-' }}</td>
                    <td class="text-center">{{ $bs->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Notes --}}
    @if($workOrder->notes)
    <div class="info-section">
        <h3>Catatan / Keluhan</h3>
        <p style="padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6;">
            {{ $workOrder->notes }}
        </p>
    </div>
    @endif

    {{-- Activity Logs --}}
    @if($workOrder->logs && $workOrder->logs->count() > 0)
    <div class="info-section">
        <h3>Activity Log (10 Aktivitas Terbaru)</h3>
        <div class="timeline">
            @foreach($workOrder->logs->take(10) as $log)
            <div class="timeline-item">
                <div class="timeline-item-time">
                    {{ $log->created_at->format('d M Y H:i:s') }}
                </div>
                <div class="timeline-item-user">
                    {{ $log->user->name ?? 'System' }}
                </div>
                <div>
                    {{ $log->action }}
                    @if($log->notes)
                    <br><small style="color: #666;">{{ $log->notes }}</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ $printedAt }}</p>
        <p>Auto2000 Work Order Management System - Terima kasih atas kepercayaan Anda</p>
    </div>
</body>
</html>