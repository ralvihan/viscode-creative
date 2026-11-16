<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan Viscode Creative</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #14213D; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #14213D; font-size: 20px; letter-spacing: 1px; }
        .header p { margin: 4px 0 0; color: #475569; font-size: 11px; }
        h3 { color: #14213D; border-left: 4px solid #2563EB; padding-left: 8px; margin-top: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: left; }
        th { background-color: #f1f5f9; color: #0f172a; font-weight: bold; }
        .text-right { text-align: right; }
        .summary-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 12px; margin-top: 10px; }
        .footer { margin-top: 40px; width: 100%; }
        .footer td { border: none; text-align: center; width: 50%; padding-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        @if(isset($report) && $report->status === 'DRAFT')
            <div style="background-color: #fef3c7; color: #92400e; padding: 4px 8px; border: 1px dashed #f59e0b; font-size: 10px; font-weight: bold; margin-bottom: 8px; display: inline-block;">
                DRAFT — LAPORAN BELUM FINAL / MASIH DALAM PENYUSUNAN
            </div>
        @elseif(isset($report))
            <div style="background-color: #ecfdf5; color: #065f46; padding: 4px 8px; border: 1px solid #10b981; font-size: 10px; font-weight: bold; margin-bottom: 8px; display: inline-block;">
                STATUS: {{ $report->status }} @if($report->finalized_at) (DIKUNCI PADA {{ \Carbon\Carbon::parse($report->finalized_at)->format('d/m/Y H:i') }}) @endif
            </div>
        @endif
        <h1>VISCODE CREATIVE</h1>
        <p>LAPORAN BULANAN USAHA — MATA KULIAH KEWIRAUSAHAAN (KELOMPOK 8)</p>
        <p><strong>Periode: {{ $periodName }}</strong></p>
    </div>

    <h3>1. Ringkasan Keuangan & Operasional</h3>
    <table>
        <tr>
            <th>Total Pemasukan Kas</th>
            <td class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            <th>Pesanan Masuk</th>
            <td class="text-right">{{ $ordersIn }} Proyek</td>
        </tr>
        <tr>
            <th>Total Pengeluaran</th>
            <td class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            <th>Proyek Selesai</th>
            <td class="text-right">{{ $ordersCompleted }} Proyek</td>
        </tr>
        <tr>
            <th>Laba Bersih Usaha</th>
            <td class="text-right"><strong>Rp {{ number_format($netProfit, 0, ',', '.') }}</strong></td>
            <th>Pelanggan Baru</th>
            <td class="text-right">{{ $newCustomers }} Klien</td>
        </tr>
    </table>

    <h3>2. Rincian Pemasukan Kas</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Kategori & Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $inc)
            <tr>
                <td>{{ \Carbon\Carbon::parse($inc->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $inc->transaction_code }}</td>
                <td><strong>{{ $inc->category }}</strong> - {{ $inc->description }}</td>
                <td class="text-right">Rp {{ number_format($inc->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">Tidak ada pemasukan tercatat pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>3. Rincian Pengeluaran Operasional</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Kategori & Keterangan</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
            <tr>
                <td>{{ \Carbon\Carbon::parse($exp->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $exp->transaction_code }}</td>
                <td><strong>{{ $exp->category }}</strong> - {{ $exp->description }}</td>
                <td class="text-right">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">Tidak ada pengeluaran tercatat pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>4. Evaluasi Bulanan Tim</h3>
    <div class="summary-box">
        {{ $evaluation }}
    </div>

    <table class="footer">
        <tr>
            <td>Mengetahui,<br>CEO / Project Manager<br><br><br><br><strong>( Raihan Alvian )</strong></td>
            <td>Disusun Oleh,<br>Finance Manager<br><br><br><br><strong>( Sutan Raja )</strong></td>
        </tr>
    </table>
</body>
</html>