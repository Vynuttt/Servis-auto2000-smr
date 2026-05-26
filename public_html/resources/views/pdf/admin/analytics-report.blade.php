<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan Analitik</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #333; 
        }
        .container { 
            width: 100%; 
            padding: 20px; 
        }
        .header {
            margin-bottom: 30px;
            width: 100%;
        }
        .header .logo {
            display: block;
            max-width: 200px; /* Anda bisa sesuaikan ukurannya */
            height: auto;
            margin-bottom: 20px; /* Jarak antara logo dan judul */
        }
        .header h1 { 
            margin: 0; 
            color: #dc2626; 
        }
        .header p { 
            margin: 5px 0 0; 
            color: #666;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #dc2626; 
            color: white;
            font-weight: bold;
            width: 40%; 
        }
        tr:nth-child(even) {
            background-color: #f9fafb; 
        }
        .value { 
            font-size: 1.2em; 
            font-weight: bold; 
        }
        .footer {
            margin-top: 30px; 
            font-size: 0.9em; 
            color: #888;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Logo ditempatkan di atas --}}
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo Auto2000" class="logo">

            {{-- Judul di bawah logo --}}
            <h1>Laporan Ringkasan Analitik</h1>
            <p>Untuk periode: {{ $startDate }} - {{ $endDate }}</p>
        </div>

        <table>
            <tbody>
                <tr>
                    <th>Total Booking Dibuat</th>
                    <td class="value">{{ $totalBookings }}</td>
                </tr>
                <tr>
                    <th>Total Booking Selesai</th>
                    <td class="value">{{ $completedBookings }}</td>
                </tr>
                {{-- <tr>
                    <th>Total Pendapatan (dari Servis)</th>
                    <td class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr> --}}
            </tbody>
        </table>

        <div class="footer">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>