<x-app-layout x-data="{
    openExportPdf: false,
    openSubmitReview: false,
    openApprove: false,
    openFinalize: false,
    openOverride: false,
    overrideRecordId: null
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-emerald-500 rounded-full inline-block"></span>
                    Laporan Bulanan Bisnis & Keuangan Usaha
                </h2>
                <p class="text-sm text-slate-500 mt-1">Laporan kinerja bulanan Viscode Creative — Mata Kuliah Kewirausahaan (Kelompok 8)</p>
            </div>

            <!-- Actions Header -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="openExportPdf = true" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 transition-colors flex items-center gap-2 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh PDF Laporan
                </button>

                @if(!$report->isLocked())
                    <form action="{{ route('reports.generate') }}" method="POST"
                          onsubmit="return window.confirmFormSubmit(event, {
                              title: 'Sinkronisasi Buku Kas {{ $periodName }}?',
                              text: 'Sistem akan menghitung ulang seluruh pemasukan dan pengeluaran riil dari transaksi periode ini.',
                              icon: 'info',
                              confirmText: 'Sinkronkan Sekarang',
                              confirmColor: '#2563EB'
                          })">
                        @csrf
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2 cursor-pointer" title="Perbarui angka dari transaksi riil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Sinkronisasi Kas
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Segmented Workspace Navigation -->
            <x-viscode-workspace-tabs workspace="finance" />

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="font-bold mb-1">Perhatian:</div>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filter Periode & Status Banner -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <!-- Selector Bulan & Tahun -->
                <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Bulan:</label>
                        <select name="month" class="rounded-xl bg-white border-slate-300 text-slate-900 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Tahun:</label>
                        <select name="year" class="rounded-xl bg-white border-slate-300 text-slate-900 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                            @for($y = 2025; $y <= 2027; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-colors cursor-pointer">
                        Tampilkan
                    </button>
                </form>

                <!-- Workflow Trigger Buttons for Report Status -->
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="text-right">
                        <div class="text-[10px] text-slate-500 uppercase font-semibold tracking-wider">Status Laporan Periode</div>
                        @php
                            $statusBadge = [
                                'DRAFT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'UNDER_REVIEW' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'APPROVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'FINALIZED' => 'bg-purple-50 text-purple-700 border-purple-200',
                            ];
                        @endphp
                        <div class="mt-0.5 flex items-center gap-2 justify-end">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadge[$report->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $report->status }}
                            </span>
                            @if($report->isLocked())
                                <span class="text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200 text-xs flex items-center gap-1" title="Laporan terkunci permanen">
                                    <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Workflow Transitions -->
                    @if($report->status === 'DRAFT' && (auth()->user()->isFinance() || auth()->user()->isSuperAdmin()))
                        <button type="button" @click="openSubmitReview = true" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer">
                            Ajukan ke CEO
                        </button>
                    @endif

                    @if($report->status === 'UNDER_REVIEW' && (auth()->user()->isCeo() || auth()->user()->isSuperAdmin()))
                        <button type="button" @click="openApprove = true" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer">
                            Setujui Laporan (CEO)
                        </button>
                    @endif

                    @if($report->status === 'APPROVED' && (auth()->user()->isFinance() || auth()->user()->isCeo() || auth()->user()->isSuperAdmin()))
                        <button type="button" @click="openFinalize = true" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold rounded-xl shadow-xs transition-colors cursor-pointer">
                            Finalisasi & Kunci Laporan
                        </button>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                        <button type="button" @click="overrideRecordId = {{ $report->id }}; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-[11px] font-bold cursor-pointer">
                            ⚡ Override
                        </button>
                    @endif
                </div>
            </div>

            <!-- Ringkasan Finansial & Operasional Periode -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Total Pemasukan</span>
                    <div class="text-lg font-bold text-emerald-600 mt-1">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Total Beban/Biaya</span>
                    <div class="text-lg font-bold text-rose-600 mt-1">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white border {{ $netProfit >= 0 ? 'border-emerald-200' : 'border-rose-200' }} rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Laba Bersih</span>
                    <div class="text-lg font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Pesanan Masuk</span>
                    <div class="text-lg font-bold text-slate-900 mt-1">{{ $ordersIn }} <span class="text-xs font-normal text-slate-400">Proyek</span></div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Proyek Selesai</span>
                    <div class="text-lg font-bold text-blue-600 mt-1">{{ $ordersCompleted }} <span class="text-xs font-normal text-slate-400">Proyek</span></div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
                    <span class="text-[11px] text-slate-500 uppercase font-semibold tracking-wider">Pelanggan Baru</span>
                    <div class="text-lg font-bold text-amber-600 mt-1">{{ $newCustomers }} <span class="text-xs font-normal text-slate-400">Klien</span></div>
                </div>
            </div>

            <!-- Tabel Rincian Pemasukan Kas -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Rincian Pemasukan Kas (Incomes)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh uang masuk dari DP, pelunasan proyek, dan pendapatan usaha</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-600">Total: Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 bg-slate-50/50">
                                <th class="py-3.5 px-4">Tanggal & Kode</th>
                                <th class="py-3.5 px-4">Kategori & Keterangan</th>
                                <th class="py-3.5 px-4">Tautan Pesanan / Klien</th>
                                <th class="py-3.5 px-4 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($incomes as $inc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="text-slate-900 font-medium text-xs">{{ \Carbon\Carbon::parse($inc->transaction_date)->format('d M Y') }}</div>
                                    <div class="font-mono text-[11px] text-slate-400 mt-0.5">{{ $inc->transaction_code }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-blue-600 text-xs">{{ $inc->category }}</span>
                                    <div class="text-slate-600 text-xs mt-0.5">{{ $inc->description }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($inc->order)
                                        <div class="text-slate-900 font-medium text-xs">{{ $inc->order->order_code }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $inc->order->customer->name ?? '-' }}</div>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold font-mono text-emerald-600 text-sm">
                                    + Rp {{ number_format($inc->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-500">Tidak ada pemasukan tercatat pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Rincian Pengeluaran Kas -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Rincian Beban & Pengeluaran Operasional (Expenses)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Biaya server, domain, aset desain, konsumsi rapat, dan kebutuhan tim</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-rose-600">Total: Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 bg-slate-50/50">
                                <th class="py-3.5 px-4">Tanggal & Kode</th>
                                <th class="py-3.5 px-4">Kategori & Keterangan</th>
                                <th class="py-3.5 px-4">Metode Pembayaran</th>
                                <th class="py-3.5 px-4 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($expenses as $exp)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="text-slate-900 font-medium text-xs">{{ \Carbon\Carbon::parse($exp->transaction_date)->format('d M Y') }}</div>
                                    <div class="font-mono text-[11px] text-slate-400 mt-0.5">{{ $exp->transaction_code }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-amber-600 text-xs">{{ $exp->category }}</span>
                                    <div class="text-slate-600 text-xs mt-0.5">{{ $exp->description }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-xs">
                                    {{ $exp->payment_method ?? 'Kas Operasional' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold font-mono text-rose-600 text-sm">
                                    - Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-500">Tidak ada pengeluaran operasional tercatat pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Arsip Riwayat Semua Laporan Bulanan -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h4 class="font-bold text-slate-900 text-sm mb-4">Arsip Riwayat Laporan Bulanan Viscode Creative</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($allReports as $r)
                        <a href="{{ route('reports.index', ['month' => $r->month, 'year' => $r->year]) }}" class="p-3.5 rounded-xl border {{ $r->id === $report->id ? 'bg-blue-50/60 border-blue-300 ring-2 ring-blue-500/20' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300 hover:bg-slate-100' }} transition-all">
                            <div class="font-bold text-xs text-slate-900">{{ \Carbon\Carbon::create($r->year, $r->month, 1)->translatedFormat('F Y') }}</div>
                            <div class="text-[10px] text-slate-500 mt-1 font-mono">{{ $r->report_code }}</div>
                            <div class="mt-2 text-[11px] font-bold text-emerald-600 font-mono">Laba: Rp {{ number_format($r->net_profit, 0, ',', '.') }}</div>
                            <span class="inline-block mt-1.5 px-2 py-0.5 rounded text-[10px] font-bold border {{ $statusBadge[$r->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $r->status }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL: UNDUH PDF DENGAN EVALUASI -->
    <x-viscode-modal name="openExportPdf" title="Cetak / Unduh Laporan PDF Resmi" maxWidth="md">
        <form action="{{ route('reports.pdf') }}" method="GET" class="space-y-4">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div>Periode Laporan: <strong class="text-slate-900">{{ $periodName }}</strong></div>
                <div class="text-slate-600 mt-1">Status Dokumen: <strong class="text-emerald-600 font-bold">{{ $report->status }}</strong></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Evaluasi Usaha Bulanan</label>
                <textarea name="evaluation" rows="4" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-xs focus:border-blue-500 focus:ring-blue-500">Operasional usaha kelompok berjalan sesuai target bulanan mata kuliah kewirausahaan. Arus kas masuk dan pengeluaran terkendali dengan baik.</textarea>
                <span class="text-xs text-slate-500 mt-1 block">Catatan ini akan dicetak di bagian "Evaluasi Bulanan Tim" pada PDF.</span>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openExportPdf = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" @click="openExportPdf = false" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Unduh File PDF</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: AJUKAN KE CEO -->
    <x-viscode-modal name="openSubmitReview" title="Ajukan Laporan ke CEO" maxWidth="sm">
        <form action="{{ route('reports.submit', $report) }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Ajukan Laporan {{ $periodName }} ke CEO?',
                  text: 'Status laporan akan diubah menjadi UNDER_REVIEW untuk verifikasi pimpinan.',
                  icon: 'question',
                  confirmText: 'Ya, Ajukan ke CEO',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 leading-relaxed">
                Laporan periode <strong>{{ $periodName }}</strong> akan diajukan ke CEO untuk diverifikasi dan disetujui. Status akan berubah menjadi <strong>UNDER_REVIEW</strong>.
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="openSubmitReview = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Ajukan Sekarang</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: PERSETUJUAN CEO -->
    <x-viscode-modal name="openApprove" title="Persetujuan Laporan oleh CEO" maxWidth="sm">
        <form action="{{ route('reports.approve', $report) }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Setujui Laporan {{ $periodName }}?',
                  text: 'CEO menyatakan angka laba bersih Rp {{ number_format($report->net_profit, 0, ',', '.') }} telah terverifikasi. Status akan menjadi APPROVED.',
                  icon: 'success',
                  confirmText: 'Ya, Setujui Laporan',
                  confirmColor: '#16A34A'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 leading-relaxed">
                Dengan menyetujui, CEO menyatakan bahwa angka pemasukan, pengeluaran, dan laba bersih periode <strong>{{ $periodName }}</strong> telah sesuai. Status akan berubah menjadi <strong>APPROVED</strong>.
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="openApprove = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Setujui Laporan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: FINALISASI & KUNCI -->
    <x-viscode-modal name="openFinalize" title="Finalisasi & Kunci Laporan Bulanan" maxWidth="sm">
        <form action="{{ route('reports.finalize', $report) }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Kunci & Finalisasi Laporan {{ $periodName }}?',
                  text: 'PERINGATAN: Laporan yang berstatus FINALIZED akan dikunci secara permanen dan tidak dapat diperbarui lagi.',
                  icon: 'warning',
                  confirmText: 'Kunci & Finalisasi',
                  confirmColor: '#9333EA'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-800 leading-relaxed">
                <strong>Perhatian:</strong> Laporan yang berstatus <strong>FINALIZED</strong> akan dikunci secara permanen dan tidak dapat diperbarui lagi dari transaksi biasa demi integritas audit.
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="openFinalize = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Finalisasi & Kunci</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE MODAL COMPONENT -->
    <x-viscode-override-modal module="reports" :statuses="['DRAFT', 'UNDER_REVIEW', 'APPROVED', 'FINALIZED']" />
</x-app-layout>
