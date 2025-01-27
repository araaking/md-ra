<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .header h2 { 
            margin: 0; 
            font-size: 24px; 
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin: 10px 0;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .amount {
            text-align: right;
            float: right;
        }
        .total-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>MADRASAH DINIYAH TAKMILIYAH AWALIYAH</h2>
        <h3>RAUDLATUL MUTA'ALLIMIN CIBENCOY</h3>
        <p>CISAAT-SUKABUMI</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <strong>Nama:</strong> {{ $bukuTabungan->siswa->name }}
        </div>
        <div class="info-row">
            <strong>Kelas:</strong> {{ $bukuTabungan->siswa->kelas->name }}
        </div>
        <div class="info-row">
            <strong>Total Tabungan:</strong> 
            <span class="amount">Rp {{ number_format($bukuTabungan->total_simpanan - $sisaCicilan, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <strong>Sisa Cicilan:</strong> 
            <span class="amount">Rp {{ number_format($sisaCicilan, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <strong>Total Keseluruhan:</strong> 
            <span class="amount">Rp {{ number_format($bukuTabungan->total_simpanan, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="section-title">Biaya Admin</div>
    <div class="info-row">
        Adm ({{ $adminPercentage }}%): 
        <span class="amount">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
    </div>

    <div class="section-title">Potongan</div>
    <div class="info-row">
        IKK: <span class="amount">Rp {{ number_format($deductions['ikk'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        SPP ({{ $bukuTabungan->siswa->bulanTertunggak ?? 0 }} Bulan): 
        <span class="amount">Rp {{ number_format($deductions['spp'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        Uang Pangkal: <span class="amount">Rp {{ number_format($deductions['initial_fee'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        UAM: <span class="amount">Rp {{ number_format($deductions['uam'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        THB: <span class="amount">Rp {{ number_format($deductions['thb'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        Foto: <span class="amount">Rp {{ number_format($deductions['photo'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        Raport: <span class="amount">Rp {{ number_format($deductions['report_card'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        Tunggakan Tahun Lalu: <span class="amount">Rp {{ number_format($deductions['previous_arrears'], 0, ',', '.') }}</span>
    </div>
    <div class="info-row">
        Pinjaman: <span class="amount">Rp {{ number_format($deductions['loan'], 0, ',', '.') }}</span>
    </div>

    <div class="total-section">
        <div class="info-row">
            Jumlah Potongan: <span class="amount">Rp {{ number_format($deductions['total'], 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="total-section" style="margin-top: 20px;">
        <div class="info-row">
            Sisa Tabungan: <span class="amount">Rp {{ number_format($remainingSavings, 0, ',', '.') }}</span>
        </div>
    </div>
</body>
</html>