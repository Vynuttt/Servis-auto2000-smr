<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking - {{ $booking->booking_code }}</title>
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
        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .services-table th {
            background: #e74c3c;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .services-table td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        .services-table tr:nth-child(even) {
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
        .badge-booked { background: #ffc107; color: #000; }
        .badge-checked-in { background: #17a2b8; }
        .badge-in-service { background: #007bff; }
        .badge-completed { background: #28a745; }
        .badge-cancelled { background: #6c757d; }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .work-order-info {
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
                <p>Service Booking System</p>
                <p>Jl. K.H. Wahid Hasyim No. 99, Sempaja, Samarinda</p>
                <p>Telp: (0541) 742000 | Email: info@auto2000.co.id</p>
            </div>
        </div>
    </div>

    {{-- Document Title --}}
    <div class="document-title">
        <h2>BOOKING DETAIL</h2>
        <p>{{ $booking->booking_code }}</p>
    </div>

    {{-- Booking Information --}}
    <div class="info-section">
        <h3>Informasi Booking</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Booking Code:</div>
                <div class="info-value"><strong>{{ $booking->booking_code }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="badge badge-{{ strtolower(str_replace(' ', '-', $booking->status)) }}">
                        {{ $booking->status }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Booking Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Time Slot:</div>
                <div class="info-value">{{ $booking->booking_time ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Service Type:</div>
                <div class="info-value">{{ $booking->service_type ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created:</div>
                <div class="info-value">{{ $booking->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Customer Information --}}
    <div class="info-section">
        <h3>Informasi Customer</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $booking->customer->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $booking->customer->email ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $booking->customer->phone ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Vehicle Information --}}
    <div class="info-section">
        <h3>Informasi Kendaraan</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Plate Number:</div>
                <div class="info-value"><strong>{{ $booking->vehicle->plate_number ?? '-' }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Brand:</div>
                <div class="info-value">{{ $booking->vehicle->brand ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Model:</div>
                <div class="info-value">{{ $booking->vehicle->model ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Year:</div>
                <div class="info-value">{{ $booking->vehicle->year ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <div class="info-section">
        <h3>Layanan yang Dipesan</h3>
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
                @foreach($booking->bookingServices as $index => $bs)
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
    @if($booking->notes)
    <div class="info-section">
        <h3>Catatan</h3>
        <p style="padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6;">
            {{ $booking->notes }}
        </p>
    </div>
    @endif

    {{-- Work Order Info (if exists) --}}
    @if($booking->workOrder)
    <div class="work-order-info">
        <strong>Work Order:</strong> {{ $booking->workOrder->wo_number }} 
        | <strong>Status:</strong> {{ $booking->workOrder->status }}
        @if($booking->workOrder->mechanic)
        | <strong>Mechanic:</strong> {{ $booking->workOrder->mechanic->name }}
        @endif
        @if($booking->workOrder->stall)
        | <strong>Stall:</strong> {{ $booking->workOrder->stall->name }}
        @endif
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini dicetak pada {{ $printedAt }}</p>
        <p>Auto2000 Service Booking System - Terima kasih atas kepercayaan Anda</p>
    </div>
</body>
</html>