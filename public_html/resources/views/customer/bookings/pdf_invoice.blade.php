<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->booking_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #DC2626; /* DIUBAH DARI biru (#0d6efd) ke merah */
        }
        .header p {
            font-size: 1.1em;
            margin: 5px 0 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 1.3em;
            color: #DC2626; /* DIUBAH DARI biru (#0d6efd) ke merah */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .total-row th {
            background-color: #FEF2F2; /* DIUBAH DARI biru muda (#f0f8ff) ke merah muda */
            font-size: 1.1em;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Auto2000</h1>
            <p>Bukti Selesai Servis (Work Order)</p>
        </div>

        <div class="section">
            <h2>Informasi Booking & Kendaraan</h2>
            <table class="info-table">
                <tr>
                    <td>Booking Code</td>
                    <td>{{ $booking->booking_code }}</td>
                </tr>
                <tr>
                    <td>Tanggal Servis</td>
                    <td>{{ $booking->scheduled_at ? $booking->scheduled_at->format('d F Y') : $booking->booking_date }}</td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Plat Nomor</td>
                    <td>{{ $booking->vehicle->plate_number ?? $booking->vehicle_plate }}</td>
                </tr>
                <tr>
                    <td>Model Kendaraan</td>
                    <td>{{ $booking->vehicle->full_name ?? $booking->vehicle_model }}</td>
                </tr>
                <tr>
                    <td>Status Akhir</td>
                    <td><strong>{{ $booking->status }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Detail Layanan</h2>
            @if($booking->services && $booking->services->count() > 0)
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Deskripsi</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->services as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->description ?? '-' }}</td>
                                </tr>
                        @endforeach
                    </tbody>
                    </table>
            @else
                <p>{{ $booking->service_type }}</p>
            @endif
        </div>

        @if($booking->complaint_note)
        <div class="section">
            <h2>Keluhan / Catatan Awal</h2>
            <p style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                {{ $booking->complaint_note }}
            </p>
        </div>
        @endif
        
        @if($booking->workOrder && $booking->workOrder->notes)
        <div class="section">
            <h2>Catatan Mekanik (Hasil Servis)</h2>
            <p style="border: 1px solid #ddd; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                {{ $booking->workOrder->notes }}
            </p>
        </div>
        @endif

        <div class="footer">
            <p>Terima kasih telah melakukan servis di Auto2000.</p>
            <p>&copy; {{ date('Y') }} Auto2000. All rights reserved.</p>
        </div>
    </div>
</body>
</html>