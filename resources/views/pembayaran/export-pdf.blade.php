<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; font-size: 24px; }
        .header h3 { margin: 5px 0; font-size: 20px; }
        .header p { margin: 5px 0; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; border: 1px solid #ddd; }
        
        .potongan-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .potongan-table th, 
        .potongan-table td { 
            border: 1px solid #ddd; 
            padding: 8px;
            text-align: left;
        }
        .text-right { text-align: right; }
        .total-section { 
            margin-top: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>MADRASAH DINIYAH TAKMILIYAH AWALIYAH</h2>
        <h3>RAUDLATUL MUTA'ALLIMIN CIBENCOY</h3>
        <p>CISAAT-SUKABUMI</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama</strong></td>
            <td width="35%">{{ $siswa->name }}</td>
            <td width="15%"><strong>Kelas</strong></td>
            <td width="35%">{{ $siswa->kelas->name }}</td>
        </tr>
        <tr>
            <td><strong>Kategori</strong></td>
            <td>{{ $siswa->category }}</td>
            <td><strong>Tahun Ajaran</strong></td>
            <td>{{ $tahunAktif->year_name }}</td>
        </tr>
    </table>

    <div class="total-section">
        <h4>TOTAL TABUNGAN: Rp {{ number_format($totalTabungan, 0, ',', '.') }}</h4>
    </div>

    <div class="potongan-section">
        <h4>BIAYA ADMIN ({{ $adminFeePercentage }}%): Rp {{ number_format($adminFee, 0, ',', '.') }}</h4>
        
        <h4>RINCIAN POTONGAN:</h4>
        <table class="potongan-table">
            <tr>
                <th width="70%">Item</th>
                <th width="30%" class="text-right">Jumlah</th>
            </tr>
            @foreach($potongan as $key => $value)
            <tr>
                <td>
                    {{ str_replace('_', ' ', $key) }} 
                    @if($key == 'SPP' && $value > 0)
                    ({{ $siswa->bulanTertunggak ?? 0 }} Bulan)
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($value, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td>TOTAL POTONGAN</td>
                <td class="text-right">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="total-section">
        <h3>SISA TABUNGAN: Rp {{ number_format($sisaTabungan, 0, ',', '.') }}</h3>
    </div>

    <div class="footer">
        <p>Cibencoy, {{ Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br>
        <p>_________________________</p>
        <p>Admin Keuangan</p>
    </div>
</body>
</html>