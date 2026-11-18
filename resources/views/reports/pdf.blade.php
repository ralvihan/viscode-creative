<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan Viscode Creative — {{ $periodName }}</title>
    <style>
        @page {
            margin: 18mm 16mm 20mm 16mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1E293B;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Running Footer */
        .page-footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 0.75pt solid #CBD5E1;
            padding-top: 4pt;
            font-size: 7.5pt;
            color: #64748B;
        }

        .page-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .page-footer td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        /* Corporate Letterhead */
        .letterhead {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2pt solid #17243B;
            padding-bottom: 10pt;
            margin-bottom: 14pt;
        }

        .letterhead td {
            border: none;
            padding: 0 0 10pt 0;
            vertical-align: bottom;
        }

        .brand-logo {
            height: 36pt;
            width: auto;
            display: block;
            margin-bottom: 4pt;
        }

        .brand-subtitle {
            font-size: 7.5pt;
            color: #475569;
            letter-spacing: 0.3px;
            margin: 0;
        }

        .doc-title-wrap {
            text-align: right;
        }

        .doc-kicker {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin: 0 0 2pt 0;
        }

        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            color: #17243B;
            margin: 0 0 3pt 0;
            letter-spacing: 0.3px;
        }

        .doc-period {
            font-size: 9pt;
            font-weight: bold;
            color: #334155;
            margin: 0;
        }

        /* Document Metadata Strip */
        .meta-strip {
            width: 100%;
            border-collapse: collapse;
            background-color: #F8FAFC;
            border: 0.75pt solid #CBD5E1;
            margin-bottom: 16pt;
        }

        .meta-strip td {
            border-right: 0.75pt solid #E2E8F0;
            padding: 6pt 9pt;
            vertical-align: top;
            width: 25%;
        }

        .meta-strip td:last-child {
            border-right: none;
        }

        .meta-label {
            display: block;
            font-size: 6.8pt;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748B;
            font-weight: bold;
            margin-bottom: 2pt;
        }

        .meta-value {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0F172A;
        }

        .status-tag {
            display: inline-block;
            padding: 1.5pt 6pt;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 0.75pt solid #17243B;
            color: #17243B;
            background-color: #FFFFFF;
        }

        .status-tag-draft {
            border-style: dashed;
            border-color: #64748B;
            color: #475569;
        }

        /* Section Headings */
        .section-heading {
            margin: 16pt 0 7pt 0;
            padding-bottom: 4pt;
            border-bottom: 1pt solid #CBD5E1;
            page-break-after: avoid;
        }

        .section-num {
            display: inline-block;
            background-color: #17243B;
            color: #FFFFFF;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 1.5pt 5pt;
            margin-right: 6pt;
            vertical-align: middle;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #17243B;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            vertical-align: middle;
        }

        /* Executive Summary Box */
        .exec-summary {
            background-color: #F8FAFC;
            border-left: 3pt solid #17243B;
            border-top: 0.5pt solid #E2E8F0;
            border-right: 0.5pt solid #E2E8F0;
            border-bottom: 0.5pt solid #E2E8F0;
            padding: 9pt 12pt;
            margin-bottom: 12pt;
            font-size: 9pt;
            color: #334155;
            text-align: justify;
        }

        /* KPI Grid (Table-based for DomPDF precision) */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6pt 0;
            margin: 0 -6pt 12pt -6pt;
            page-break-inside: avoid;
        }

        .kpi-cell {
            width: 25%;
            background-color: #FFFFFF;
            border: 0.75pt solid #CBD5E1;
            border-top: 2.5pt solid #17243B;
            padding: 8pt 9pt;
            vertical-align: top;
        }

        .kpi-cell-muted {
            border-top-color: #64748B;
        }

        .kpi-label {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748B;
            font-weight: bold;
            margin-bottom: 4pt;
        }

        .kpi-value {
            font-size: 11.5pt;
            font-weight: bold;
            color: #0F172A;
            margin-bottom: 2pt;
        }

        .kpi-value-positive {
            color: #14532D;
        }

        .kpi-value-negative {
            color: #991B1B;
        }

        .kpi-note {
            font-size: 7pt;
            color: #64748B;
        }

        /* Corporate Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
            margin-bottom: 10pt;
            font-size: 8.5pt;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background-color: #F1F5F9;
            color: #0F172A;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-top: 1pt solid #17243B;
            border-bottom: 1pt solid #94A3B8;
            border-left: 0.5pt solid #E2E8F0;
            border-right: 0.5pt solid #E2E8F0;
            padding: 6.5pt 7pt;
            text-align: left;
        }

        .data-table td {
            border-bottom: 0.5pt solid #E2E8F0;
            border-left: 0.5pt solid #F1F5F9;
            border-right: 0.5pt solid #F1F5F9;
            padding: 6pt 7pt;
            vertical-align: top;
            color: #1E293B;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #FAFCFF;
        }

        .data-table tfoot td {
            background-color: #F8FAFC;
            border-top: 1pt solid #17243B;
            border-bottom: 1pt solid #17243B;
            font-weight: bold;
            color: #0F172A;
            padding: 7pt 7pt;
        }

        /* Summary Accounting Statement */
        .statement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
            page-break-inside: avoid;
        }

        .statement-table td {
            padding: 6.5pt 9pt;
            border-bottom: 0.5pt solid #CBD5E1;
            font-size: 9pt;
        }

        .statement-total td {
            background-color: #17243B;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 9.5pt;
            border: none;
            padding: 8pt 9pt;
        }

        /* Operational Overview 2-Column Matrix */
        .ops-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
            page-break-inside: avoid;
        }

        .ops-table th {
            background-color: #F8FAFC;
            color: #334155;
            font-size: 8pt;
            font-weight: bold;
            padding: 6pt 8pt;
            border: 0.5pt solid #CBD5E1;
            text-align: left;
            width: 30%;
        }

        .ops-table td {
            padding: 6pt 8pt;
            border: 0.5pt solid #CBD5E1;
            font-size: 8.5pt;
            color: #0F172A;
            width: 20%;
        }

        /* Evaluation Box */
        .evaluation-box {
            background-color: #FFFFFF;
            border: 0.75pt solid #CBD5E1;
            padding: 10pt 12pt;
            font-size: 8.8pt;
            color: #334155;
            line-height: 1.6;
            text-align: justify;
            page-break-inside: avoid;
        }

        /* Signature Block */
        .signature-block {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24pt;
            page-break-inside: avoid;
        }

        .signature-block td {
            width: 50%;
            border: none;
            text-align: center;
            vertical-align: top;
            padding: 4pt 16pt;
            font-size: 8.5pt;
            color: #334155;
        }

        .sig-role {
            font-size: 8pt;
            color: #64748B;
            margin-top: 2pt;
        }

        .sig-space {
            height: 46pt;
        }

        .sig-name {
            font-weight: bold;
            color: #0F172A;
            border-bottom: 0.75pt solid #0F172A;
            display: inline-block;
            padding: 0 18pt 2pt 18pt;
            margin-bottom: 2pt;
        }

        .sig-meta {
            font-size: 7.5pt;
            color: #64748B;
        }

        /* Helpers */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; font-size: 8pt; }
        .muted { color: #64748B; font-size: 7.8pt; }
        .bold { font-weight: bold; }
        .empty-row { text-align: center !important; color: #64748B !important; font-style: italic; padding: 12pt !important; }
    </style>
</head>
<body>
    @php
        $reportStatus = isset($report) ? $report->status : 'DRAFT';
        $statusLabelMap = [
            'DRAFT' => 'DRAFT (PENYUSUNAN)',
            'UNDER_REVIEW' => 'MENUNGGU REVIEW CEO',
            'APPROVED' => 'DISETUJUI CEO',
            'FINALIZED' => 'FINAL / TERKUNCI',
        ];
        $statusReadable = $statusLabelMap[$reportStatus] ?? $reportStatus;
        $reportCode = isset($report) && $report->report_code ? $report->report_code : 'RPT-'.str_pad((string) ($month ?? date('m')), 2, '0', STR_PAD_LEFT).'-'.($year ?? date('Y'));
        $isSurplus = $netProfit >= 0;
        $printedTimestamp = $printedAt ?? \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i').' WIB';
        $leadCount = $leadsIn ?? 0;
        $quoCount = $quotationsCreated ?? 0;
        $activePrjCount = $activeProjects ?? 0;
        $receivablesAmount = $outstandingReceivables ?? 0;
        $marginPercent = $profitMargin ?? ($totalIncome > 0 ? round(($netProfit / $totalIncome) * 100, 1) : 0);
        $completionPercent = $completionRate ?? ($ordersIn > 0 ? round(($ordersCompleted / $ordersIn) * 100, 1) : 0);
    @endphp

    <!-- RUNNING FOOTER -->
    <div class="page-footer">
        <table>
            <tr>
                <td><strong>Viscode Creative</strong> — Business Management System (VC-BMS) &bull; Laporan Resmi Bulanan</td>
                <td class="text-right">Periode {{ $periodName }} &bull; Dicetak {{ $printedTimestamp }}</td>
            </tr>
        </table>
    </div>

    <!-- ============================================================= -->
    <!-- KOP SURAT / CORPORATE LETTERHEAD                              -->
    <!-- ============================================================= -->
    <table class="letterhead">
        <tr>
            <td style="width: 52%;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Viscode Creative" class="brand-logo">
                @else
                    <div style="font-size: 16pt; font-weight: bold; color: #17243B; margin-bottom: 3pt;">VISCODE CREATIVE</div>
                @endif
                <p class="brand-subtitle">
                    Digital Creative Agency &bull; Web Development, Graphic Design &amp; Event Solutions<br>
                    Unit Usaha Mahasiswa — Mata Kuliah Kewirausahaan (Kelompok 8)
                </p>
            </td>
            <td style="width: 48%;" class="doc-title-wrap">
                <p class="doc-kicker">Dokumen Laporan Internal Manajemen</p>
                <h1 class="doc-title">LAPORAN BISNIS &amp; KEUANGAN</h1>
                <p class="doc-period">Periode: {{ $periodName }}</p>
            </td>
        </tr>
    </table>

    <!-- METADATA DOKUMEN -->
    <table class="meta-strip">
        <tr>
            <td>
                <span class="meta-label">Nomor Laporan</span>
                <span class="meta-value mono">{{ $reportCode }}</span>
            </td>
            <td>
                <span class="meta-label">Periode Pelaporan</span>
                <span class="meta-value">{{ $periodName }}</span>
            </td>
            <td>
                <span class="meta-label">Status Dokumen</span>
                <span class="status-tag {{ $reportStatus === 'DRAFT' ? 'status-tag-draft' : '' }}">{{ $statusReadable }}</span>
            </td>
            <td>
                <span class="meta-label">Tanggal Cetak</span>
                <span class="meta-value">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d M Y') }}</span>
            </td>
        </tr>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 1: RINGKASAN EKSEKUTIF & INDIKATOR UTAMA               -->
    <!-- ============================================================= -->
    <div class="section-heading" style="margin-top: 4pt;">
        <span class="section-num">01</span>
        <span class="section-title">Ringkasan Eksekutif (Executive Summary)</span>
    </div>

    <div class="exec-summary">
        Pada periode <strong>{{ $periodName }}</strong>, <strong>Viscode Creative</strong> membukukan total pemasukan kas sebesar
        <strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong> dan total pengeluaran operasional sebesar
        <strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong>, sehingga menghasilkan
        <strong>{{ $isSurplus ? 'laba bersih (surplus)' : 'defisit operasional' }} sebesar Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</strong>
        @if($totalIncome > 0)
            dengan rasio margin laba bersih <strong>{{ number_format($marginPercent, 1, ',', '.') }}%</strong>.
        @else
            pada periode pencatatan berjalan.
        @endif
        Dari sisi operasional, tim menerima <strong>{{ $leadCount }} prospek baru</strong>, menerbitkan <strong>{{ $quoCount }} penawaran harga</strong>,
        mencatat <strong>{{ $ordersIn }} pesanan proyek masuk</strong> dengan <strong>{{ $ordersCompleted }} proyek terselesaikan</strong>, serta menambah
        <strong>{{ $newCustomers }} pelanggan baru</strong>.
        @if($receivablesAmount > 0)
            Selain kas masuk tersebut, terdapat potensi sisa piutang usaha aktif sebesar <strong>Rp {{ number_format($receivablesAmount, 0, ',', '.') }}</strong> dari pesanan yang sedang berjalan.
        @endif
    </div>

    <!-- 4 KARTU KPI UTAMA -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell">
                <div class="kpi-label">Total Pemasukan Kas</div>
                <div class="kpi-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                <div class="kpi-note">{{ $incomes->count() }} transaksi kas masuk</div>
            </td>
            <td class="kpi-cell kpi-cell-muted">
                <div class="kpi-label">Total Pengeluaran</div>
                <div class="kpi-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                <div class="kpi-note">{{ $expenses->count() }} transaksi biaya operasional</div>
            </td>
            <td class="kpi-cell">
                <div class="kpi-label">Laba / Rugi Bersih</div>
                <div class="kpi-value {{ $isSurplus ? 'kpi-value-positive' : 'kpi-value-negative' }}">
                    {{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                </div>
                <div class="kpi-note">Margin bersih: {{ number_format($marginPercent, 1, ',', '.') }}%</div>
            </td>
            <td class="kpi-cell kpi-cell-muted">
                <div class="kpi-label">Piutang Usaha Aktif</div>
                <div class="kpi-value">Rp {{ number_format($receivablesAmount, 0, ',', '.') }}</div>
                <div class="kpi-note">Sisa tagihan klien belum lunas</div>
            </td>
        </tr>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 2: IKHTISAR KINERJA OPERASIONAL & PENJUALAN            -->
    <!-- ============================================================= -->
    <div class="section-heading">
        <span class="section-num">02</span>
        <span class="section-title">Ikhtisar Kinerja Operasional &amp; Penjualan</span>
    </div>

    <table class="ops-table">
        <tr>
            <th>Prospek Masuk (Leads)</th>
            <td class="text-right bold">{{ $leadCount }} Prospek</td>
            <th>Pesanan Proyek Masuk</th>
            <td class="text-right bold">{{ $ordersIn }} Pesanan</td>
        </tr>
        <tr>
            <th>Penawaran Diterbitkan (Quotation)</th>
            <td class="text-right bold">{{ $quoCount }} Dokumen</td>
            <th>Proyek Selesai (Completed)</th>
            <td class="text-right bold">{{ $ordersCompleted }} Proyek</td>
        </tr>
        <tr>
            <th>Pelanggan Baru Terdaftar</th>
            <td class="text-right bold">{{ $newCustomers }} Klien</td>
            <th>Proyek Aktif Sedang Berjalan</th>
            <td class="text-right bold">{{ $activePrjCount }} Proyek</td>
        </tr>
        <tr>
            <th>Tingkat Penyelesaian Periode</th>
            <td class="text-right bold">{{ number_format($completionPercent, 1, ',', '.') }}%</td>
            <th>Status Verifikasi Laporan</th>
            <td class="text-right bold">{{ $statusReadable }}</td>
        </tr>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 3: RINCIAN PEMASUKAN KAS (CASH INFLOWS)                -->
    <!-- ============================================================= -->
    <div class="section-heading">
        <span class="section-num">03</span>
        <span class="section-title">Rincian Pemasukan Kas (Cash Inflows)</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 16%;">Kode Transaksi</th>
                <th style="width: 35%;">Kategori &amp; Keterangan</th>
                <th style="width: 14%;">Metode</th>
                <th style="width: 17%;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $index => $inc)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="mono">{{ \Carbon\Carbon::parse($inc->transaction_date)->format('d/m/Y') }}</td>
                <td class="mono bold">{{ $inc->transaction_code }}</td>
                <td>
                    <span class="bold">{{ $inc->category }}</span>
                    @if($inc->description)
                        <div class="muted">{{ $inc->description }}</div>
                    @endif
                    @if($inc->order && $inc->order->customer)
                        <div class="muted">Klien: {{ $inc->order->customer->name }} ({{ $inc->order->order_code }})</div>
                    @endif
                </td>
                <td>{{ $inc->payment_method ?? '-' }}</td>
                <td class="text-right mono bold">{{ number_format($inc->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-row">Belum ada pemasukan kas yang tercatat pada periode {{ $periodName }}.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL PEMASUKAN KAS PERIODE {{ strtoupper($periodName) }}</td>
                <td class="text-right mono">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 4: RINCIAN PENGELUARAN OPERASIONAL (CASH OUTFLOWS)     -->
    <!-- ============================================================= -->
    <div class="section-heading">
        <span class="section-num">04</span>
        <span class="section-title">Rincian Pengeluaran Operasional (Cash Outflows)</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 13%;">Tanggal</th>
                <th style="width: 16%;">Kode Transaksi</th>
                <th style="width: 35%;">Kategori &amp; Keterangan</th>
                <th style="width: 14%;">Metode</th>
                <th style="width: 17%;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $index => $exp)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="mono">{{ \Carbon\Carbon::parse($exp->transaction_date)->format('d/m/Y') }}</td>
                <td class="mono bold">{{ $exp->transaction_code }}</td>
                <td>
                    <span class="bold">{{ $exp->category }}</span>
                    @if($exp->description)
                        <div class="muted">{{ $exp->description }}</div>
                    @endif
                </td>
                <td>{{ $exp->payment_method ?? '-' }}</td>
                <td class="text-right mono bold">{{ number_format($exp->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-row">Belum ada pengeluaran operasional yang tercatat pada periode {{ $periodName }}.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL PENGELUARAN OPERASIONAL PERIODE {{ strtoupper($periodName) }}</td>
                <td class="text-right mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 5: IKHTISAR LABA RUGI SINGKAT (INCOME STATEMENT)       -->
    <!-- ============================================================= -->
    <div class="section-heading">
        <span class="section-num">05</span>
        <span class="section-title">Rekapitulasi Laba Rugi Singkat</span>
    </div>

    <table class="statement-table">
        <tr>
            <td style="width: 72%;"><strong>Pendapatan / Pemasukan Kas Terverifikasi</strong> <span class="muted">({{ $incomes->count() }} transaksi)</span></td>
            <td style="width: 28%;" class="text-right mono bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>(-) Beban / Pengeluaran Operasional</strong> <span class="muted">({{ $expenses->count() }} transaksi)</span></td>
            <td class="text-right mono">(Rp {{ number_format($totalExpense, 0, ',', '.') }})</td>
        </tr>
        <tr class="statement-total">
            <td>(=) {{ $isSurplus ? 'LABA BERSIH USAHA (NET PROFIT)' : 'RUGI / DEFISIT OPERASIONAL' }} — MARGIN {{ number_format($marginPercent, 1, ',', '.') }}%</td>
            <td class="text-right mono">{{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- ============================================================= -->
    <!-- BAGIAN 6: CATATAN EVALUASI & RENCANA TINDAK LANJUT TIM        -->
    <!-- ============================================================= -->
    <div class="section-heading">
        <span class="section-num">06</span>
        <span class="section-title">Catatan Evaluasi &amp; Tindak Lanjut Manajemen</span>
    </div>

    <div class="evaluation-box">
        {{ $evaluation }}
    </div>

    <!-- ============================================================= -->
    <!-- BAGIAN 7: LEMBAR PENGESAHAN & TANDA TANGAN RESMI              -->
    <!-- ============================================================= -->
    <table class="signature-block">
        <tr>
            <td>
                <div>Disusun &amp; Diverifikasi Oleh,</div>
                <div class="sig-role">Finance Manager — Viscode Creative</div>
                <div class="sig-space"></div>
                <div class="sig-name">Sutan Raja</div>
                <div class="sig-meta">Kelompok 8 — Kewirausahaan</div>
            </td>
            <td>
                <div>Mengetahui &amp; Menyetujui,</div>
                <div class="sig-role">CEO / Project Manager — Viscode Creative</div>
                <div class="sig-space"></div>
                <div class="sig-name">Raihan Alvian</div>
                <div class="sig-meta">
                    @if(isset($report) && $report->finalized_at)
                        Disahkan pada {{ \Carbon\Carbon::parse($report->finalized_at)->locale('id')->translatedFormat('d F Y') }}
                    @else
                        Periode {{ $periodName }}
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
