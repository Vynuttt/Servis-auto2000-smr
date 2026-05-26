<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Popularitas Layanan</title>
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
            max-width: 200px; /* Sesuaikan ukurannya */
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
        }
        tr:nth-child(even) {
            background-color: #f9fafb; 
        }
        .footer {
            margin-top: 30px; 
            font-size: 0.9em; 
            color: #888;
            text-align: right;
        }
        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{-- Logo ditempatkan di atas --}}
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo Auto2000" class="logo">
            
            {{-- Judul di bawah logo --}}
            <h1>Laporan Popularitas Layanan</h1>
            <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 35%;">Nama Layanan</th>
                    <th style="width: 20%;">Kode Servis</th>
                    <th style="width: 20%; text-align: center;">Total Dipesan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $index => $service)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $service['service_name'] }}</td>
                        <td><strong>{{ $service['service_code'] }}</strong></td>
                        <td style="text-align: center;">
                            <strong>{{ $service['total_bookings'] }}</strong> kali
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="no-data">
                            Tidak ada data layanan untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}
        </div>
    </div>
</body>
</html>