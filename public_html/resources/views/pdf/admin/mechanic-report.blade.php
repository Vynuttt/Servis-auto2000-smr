<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kinerja Mekanik</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            color: #333; 
        }
        .container { 
            width: 100%; 
            padding: 20px; 
        }
        /* CSS header diperbaiki di sini */
        .header {
            margin-bottom: 30px;
            width: 100%;
        }
        /* CSS logo diperbaiki di sini */
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
            <h1>Laporan Kinerja Mekanik</h1>
            <p>Periode: {{ $startDate }} - {{ $endDate }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 35%;">Nama Mekanik</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 20%; text-align: center;">Total WO Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mechanics as $index => $mechanic)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $mechanic->name }}</td>
                        <td><strong>{{ $mechanic->email }}</strong></td>
                        <td style="text-align: center;">
                            <strong>{{ $mechanic->completed_work_orders_count }}</strong> WO
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="no-data">
                            Tidak ada data mekanik untuk periode ini.
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