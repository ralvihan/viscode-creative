<x-app-layout x-data="{
    openCreate: false,
    openApprove: false,
    openReject: false,
    openRecordPurchase: false,
    openFinalize: false,
    openCancel: false,
    openDetail: false,
    openOverride: false,
    overrideRecordId: null,
    selectedPurchase: null,
    approveUrl: '',
    rejectUrl: '',
    recordUrl: '',
    finalizeUrl: '',
    cancelUrl: '',
    setApprove(pr) {
        this.selectedPurchase = pr;
        this.approveUrl = '{{ url('/purchases') }}/' + pr.id + '/approve';
        this.openApprove = true;
    },
    setReject(pr) {
        this.selectedPurchase = pr;
        this.rejectUrl = '{{ url('/purchases') }}/' + pr.id + '/reject';
        this.openReject = true;
    },
    setRecordPurchase(pr) {
        this.selectedPurchase = pr;
        this.recordUrl = '{{ url('/purchases') }}/' + pr.id + '/record-purchase';
        this.openRecordPurchase = true;
    },
    setFinalize(pr) {
        this.selectedPurchase = pr;
        this.finalizeUrl = '{{ url('/purchases') }}/' + pr.id + '/finalize';
        this.openFinalize = true;
    },
    setCancel(pr) {
        this.selectedPurchase = pr;
        this.cancelUrl = '{{ url('/purchases') }}/' + pr.id + '/cancel';
        this.openCancel = true;
    },
    setDetail(pr) {
        this.selectedPurchase = pr;
        this.openDetail = true;
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-amber-500 rounded-full inline-block"></span>
                    Pengajuan Pembelian & Beban Operasional
                </h2>
                <p class="text-sm text-slate-500 mt-1">Kelola kebutuhan operasional, perizinan CEO, nota pengeluaran, dan integrasi kas tim</p>
            </div>
            <button type="button" @click="openCreate = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajukan Pembelian
            </button>
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

            <!-- Mini Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Diajukan</span>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 mt-2">{{ $purchases->total() }} <span class="text-xs font-normal text-slate-400">Permintaan</span></div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Menunggu CEO</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-amber-600 mt-2">
                        {{ $purchases->where('status', 'PENDING')->count() }} <span class="text-xs font-normal text-slate-400">Menunggu</span>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Siap Dibelanjakan</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-600 mt-2">
                        {{ $purchases->where('status', 'APPROVED')->count() }} <span class="text-xs font-normal text-slate-400">Disetujui</span>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Selesai Dibukukan</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600 mt-2">
                        {{ $purchases->where('status', 'COMPLETED')->count() }} <span class="text-xs font-normal text-slate-400">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Table of Purchase Requests -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-base">Daftar Pengajuan Pembelian Usaha</h3>
                    <span class="text-xs font-semibold text-slate-500">Total: {{ $purchases->total() }} Data</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 bg-slate-50/50">
                                <th class="py-3.5 px-4">Kode & Tanggal</th>
                                <th class="py-3.5 px-4">Pemohon</th>
                                <th class="py-3.5 px-4">Judul & Tujuan Pengeluaran</th>
                                <th class="py-3.5 px-4 text-right">Estimasi / Realisasi</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi Operasional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($purchases as $pr)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-blue-600 text-xs">{{ $pr->request_code }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $pr->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-900">{{ $pr->requester->name ?? 'Tim' }}</div>
                                    <div class="text-[11px] text-slate-500 uppercase">{{ $pr->requester->role ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900">{{ $pr->title }}</div>
                                    <div class="text-slate-500 truncate text-xs mt-0.5">{{ $pr->purpose }}</div>
                                    @if($pr->approval_notes)
                                        <div class="text-xs text-blue-600 mt-1">Catatan CEO: {{ $pr->approval_notes }}</div>
                                    @endif
                                    @if($pr->rejection_reason)
                                        <div class="text-xs text-rose-600 mt-1">Alasan Ditolak: {{ $pr->rejection_reason }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="text-slate-600 font-mono text-xs">Est: Rp {{ number_format($pr->estimated_amount, 0, ',', '.') }}</div>
                                    @if($pr->actual_amount)
                                        <div class="text-emerald-600 font-bold font-mono text-xs mt-0.5">Real: Rp {{ number_format($pr->actual_amount, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $badgeStyles = [
                                            'PENDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'APPROVED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'PURCHASED' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'COMPLETED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'CANCELLED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badgeStyles[$pr->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $pr->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-2">
                                        {{-- CONTEXTUAL ACTION SYSTEM: ONE PRIMARY ACTION PER PURCHASE STATUS --}}
                                        @if($pr->status === 'PENDING' && (auth()->user()->isCeo() || auth()->user()->isSuperAdmin()))
                                            <button type="button" @click="setApprove({{ json_encode($pr) }})" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                                🔍 Tinjau & Setujui
                                            </button>
                                        @elseif($pr->status === 'APPROVED')
                                            <button type="button" @click="setRecordPurchase({{ json_encode($pr) }})" class="px-3.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                                🛒 Catat Belanja & Nota
                                            </button>
                                        @elseif($pr->status === 'PURCHASED' && (auth()->user()->isFinance() || auth()->user()->isSuperAdmin() || auth()->user()->isCeo()))
                                            <button type="button" @click="setFinalize({{ json_encode($pr) }})" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                                📘 Bukukan ke Kas
                                            </button>
                                        @endif

                                        <button type="button" @click="setDetail({{ json_encode($pr) }})" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg border border-transparent hover:border-slate-200 transition-colors cursor-pointer" title="Lihat Detail & Opsi Lainnya">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    </div>
                                    <div class="font-medium text-slate-600">Belum ada pengajuan pembelian operasional</div>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "+ Ajukan Pembelian" di atas untuk mengajukan kebutuhan tim.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchases->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL: AJUKAN PEMBELIAN BARU -->
    <x-viscode-modal name="openCreate" title="Buat Pengajuan Pembelian Operasional" maxWidth="lg">
        <form action="{{ route('purchases.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul / Barang / Layanan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Langganan Server Cloud / Desain Asset Icon" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tujuan & Rincian Kebutuhan <span class="text-rose-500">*</span></label>
                <textarea name="purpose" rows="3" required placeholder="Jelaskan untuk keperluan proyek atau operasional apa..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <x-viscode-rupiah-input
                name="estimated_amount"
                label="Estimasi Biaya"
                placeholder="Contoh: 150.000"
                :required="true"
                hint="Pengajuan akan berstatus PENDING dan dikirimkan ke CEO untuk diverifikasi."
            />

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Kirim Pengajuan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: PERSETUJUAN CEO -->
    <x-viscode-modal name="openApprove" title="Persetujuan CEO atas Pengajuan Pembelian" maxWidth="md">
        <form :action="approveUrl" method="POST" class="space-y-4"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Setujui Pengajuan Pembelian?',
                  html: 'Anda menyetujui pengajuan pembelian sebesar <b>' + (selectedPurchase ? 'Rp ' + Number(selectedPurchase.estimated_amount).toLocaleString('id-ID') : '') + '</b> untuk <b>' + (selectedPurchase ? selectedPurchase.title : '') + '</b>.<br><br>Status pengajuan akan beralih ke <span class=\'text-blue-600 font-bold\'>APPROVED</span>.',
                  icon: 'question',
                  confirmButtonText: 'Setujui Pengajuan',
                  confirmButtonColor: '#16A34A'
              })">
            @csrf
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div class="text-slate-600">Pengajuan: <strong class="text-slate-900" x-text="selectedPurchase ? selectedPurchase.request_code + ' - ' + selectedPurchase.title : ''"></strong></div>
                <div class="text-slate-600 mt-1">Estimasi: <strong class="text-blue-600 font-mono" x-text="selectedPurchase ? 'Rp ' + Number(selectedPurchase.estimated_amount).toLocaleString('id-ID') : ''"></strong></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Persetujuan (Opsional)</label>
                <textarea name="approval_notes" rows="2" placeholder="Contoh: Disetujui, gunakan dana kas operasional bulan ini..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openApprove = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Setujui Pengajuan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: PENOLAKAN CEO -->
    <x-viscode-modal name="openReject" title="Penolakan Pengajuan Pembelian" maxWidth="md">
        <form :action="rejectUrl" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 leading-relaxed">
                Pengajuan yang ditolak tidak dapat diproses belanja. Harap berikan alasan yang jelas.
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Jelaskan alasan penolakan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openReject = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Konfirmasi Penolakan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: CATAT PEMBELIAN (PURCHASED) -->
    <x-viscode-modal name="openRecordPurchase" title="Catat Realisasi Pembelian & Unggah Bukti Nota" maxWidth="lg">
        <form :action="recordUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-800 leading-relaxed">
                Pengajuan telah disetujui CEO. Masukkan total biaya yang dibayarkan dan unggah foto/struk nota pembelian.
            </div>

            <x-viscode-rupiah-input
                name="actual_amount"
                label="Total Biaya Aktual / Riil"
                placeholder="Contoh: 150.000"
                :required="true"
                alpineValue="selectedPurchase ? selectedPurchase.estimated_amount : ''"
            />

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Unggah Struk / Bukti Nota (JPG, PNG, PDF max 2MB)</label>
                <input type="file" name="receipt_proof" accept=".jpeg,.png,.jpg,.pdf" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openRecordPurchase = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Realisasi Pembelian</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: BUKUKAN KE KAS KELUAR (FINANCE) -->
    <x-viscode-modal name="openFinalize" title="Bukukan Pengeluaran ke Buku Kas Usaha" maxWidth="md">
        <form :action="finalizeUrl" method="POST" class="space-y-4"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Bukukan ke Buku Kas Usaha?',
                  html: 'Anda akan membukukan pengeluaran riil sebesar <b>' + (selectedPurchase ? 'Rp ' + Number(selectedPurchase.actual_amount).toLocaleString('id-ID') : '') + '</b> ke kas operasional.<br><br>Status pengajuan akan beralih ke <span class=\'text-emerald-600 font-bold\'>COMPLETED</span>.',
                  icon: 'question',
                  confirmButtonText: 'Bukukan Sekarang',
                  confirmButtonColor: '#2563EB'
              })">
            @csrf
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 leading-relaxed">
                Tindakan ini akan mencatat transaksi <strong>Expense (Pengeluaran)</strong> secara otomatis di Buku Kas dan menandai pengajuan sebagai <strong>COMPLETED</strong>.
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <div class="text-slate-600">Pengajuan: <strong class="text-slate-900" x-text="selectedPurchase ? selectedPurchase.request_code : ''"></strong></div>
                <div class="text-slate-600 mt-1">Pengeluaran Riil: <strong class="text-emerald-600 font-mono text-sm" x-text="selectedPurchase ? 'Rp ' + Number(selectedPurchase.actual_amount).toLocaleString('id-ID') : ''"></strong></div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Buku Kas</label>
                <input type="text" name="category" value="Beban Operasional Tim" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi Kas</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran</label>
                <select name="payment_method" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="Transfer Bank Operasional">Transfer Bank Operasional</option>
                    <option value="QRIS / E-Wallet Usaha">QRIS / E-Wallet Usaha</option>
                    <option value="Tunai / Kas Kecil">Tunai / Kas Kecil</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openFinalize = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Bukukan Sekarang</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: BATALKAN PENGAJUAN -->
    <x-viscode-modal name="openCancel" title="Batalkan Pengajuan Pembelian" maxWidth="md">
        <form :action="cancelUrl" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alasan Pembatalan <span class="text-rose-500">*</span></label>
                <textarea name="cancellation_reason" rows="3" required placeholder="Tuliskan alasan pembatalan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCancel = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Kembali</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Batalkan Pengajuan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: LIHAT DETAIL LENGKAP -->
    <x-viscode-modal name="openDetail" title="Detail Pengajuan Pembelian" maxWidth="lg">
        <div class="space-y-4 text-xs" x-show="selectedPurchase">
            <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-500 font-medium">Kode Pengajuan:</span>
                    <div class="font-mono font-bold text-blue-600 text-sm mt-0.5" x-text="selectedPurchase ? selectedPurchase.request_code : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Status:</span>
                    <div class="mt-0.5">
                        <span class="px-2.5 py-0.5 rounded-full font-bold border border-slate-200 bg-white text-slate-700 text-[11px]" x-text="selectedPurchase ? selectedPurchase.status : ''"></span>
                    </div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Estimasi Biaya:</span>
                    <div class="font-bold text-slate-900 mt-0.5" x-text="selectedPurchase ? 'Rp ' + Number(selectedPurchase.estimated_amount).toLocaleString('id-ID') : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Biaya Riil / Aktual:</span>
                    <div class="font-bold text-emerald-600 mt-0.5" x-text="selectedPurchase && selectedPurchase.actual_amount ? 'Rp ' + Number(selectedPurchase.actual_amount).toLocaleString('id-ID') : '-'"></div>
                </div>
            </div>

            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider">Judul Pengeluaran:</span>
                <div class="text-sm font-bold text-slate-900 mt-1" x-text="selectedPurchase ? selectedPurchase.title : ''"></div>
            </div>

            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider">Tujuan / Keperluan:</span>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 mt-1 whitespace-pre-line leading-relaxed" x-text="selectedPurchase ? selectedPurchase.purpose : ''"></div>
            </div>

            <template x-if="selectedPurchase && selectedPurchase.approval_notes">
                <div>
                    <span class="text-blue-600 font-semibold uppercase tracking-wider">Catatan Persetujuan CEO:</span>
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 mt-1" x-text="selectedPurchase.approval_notes"></div>
                </div>
            </template>

            <template x-if="selectedPurchase && selectedPurchase.rejection_reason">
                <div>
                    <span class="text-rose-600 font-semibold uppercase tracking-wider">Alasan Penolakan:</span>
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 mt-1" x-text="selectedPurchase.rejection_reason"></div>
                </div>
            </template>

            <template x-if="selectedPurchase && selectedPurchase.receipt_proof">
                <div>
                    <span class="text-slate-500 font-semibold uppercase tracking-wider">Bukti Struk / Nota Pembelian:</span>
                    <div class="mt-2">
                        <a :href="'{{ asset('storage') }}/' + selectedPurchase.receipt_proof" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-700 rounded-xl transition-colors font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Buka / Unduh Bukti Nota Pembelian
                        </a>
                    </div>
                </div>
            </template>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2 flex-wrap">
                    @if(auth()->user()->isCeo() || auth()->user()->isSuperAdmin())
                    <template x-if="selectedPurchase && selectedPurchase.status === 'PENDING'">
                        <button type="button" @click="openDetail = false; setReject(selectedPurchase)" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg cursor-pointer">
                            ✕ Tolak Pengajuan
                        </button>
                    </template>
                    @endif
                    <template x-if="selectedPurchase && ['PENDING', 'APPROVED'].includes(selectedPurchase.status)">
                        <button type="button" @click="openDetail = false; setCancel(selectedPurchase)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg cursor-pointer">
                            Batalkan Pengajuan
                        </button>
                    </template>
                    @if(auth()->user()->isSuperAdmin())
                    <button type="button" @click="openDetail = false; overrideRecordId = selectedPurchase.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer">
                        ⚡ Admin Override
                    </button>
                    @endif
                </div>
                <button type="button" @click="openDetail = false" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE MODAL COMPONENT -->
    <x-viscode-override-modal module="purchases" :statuses="['PENDING', 'APPROVED', 'REJECTED', 'PURCHASED', 'COMPLETED', 'CANCELLED']" />
</x-app-layout>
