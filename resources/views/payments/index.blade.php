<x-app-layout x-data="{ 
    openCreate: false, 
    openDetail: false, 
    openReject: false, 
    openVoid: false,
    openOverride: false,
    selectedPayment: {}, 
    overrideRecordId: null 
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Verifikasi Pembayaran & Piutang
                </h2>
                <p class="text-xs text-slate-500 mt-1">Audit transaksi pembayaran klien: DP, termin, dan pelunasan sebelum dibukukan ke kas</p>
            </div>
            <button type="button" @click="openCreate = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Pembayaran Klien</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Segmented Workspace Navigation -->
        <x-viscode-workspace-tabs workspace="finance" />

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <div class="font-bold mb-1">Perhatian:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Payments Table Container (Pure White) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Riwayat Entri Pembayaran Masuk</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total {{ $payments->total() }} transaksi pembayaran terdata</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 rounded-l-xl">Kode Bayar & Tanggal</th>
                            <th class="py-3.5 px-4">Order Terkait & Klien</th>
                            <th class="py-3.5 px-4">Metode & Pencatat</th>
                            <th class="py-3.5 px-4 text-right">Nominal Bayar</th>
                            <th class="py-3.5 px-4">Status Verifikasi</th>
                            <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($payments as $pay)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-blue-600">{{ $pay->payment_code }}</div>
                                <div class="text-[11px] text-slate-400 font-normal">{{ $pay->payment_date ? $pay->payment_date->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $pay->order->order_code ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $pay->order->customer->name ?? '-' }} (Deal: Rp {{ number_format($pay->order->agreed_price ?? 0, 0, ',', '.') }})</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $pay->payment_method }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Dicatat: {{ $pay->recordedBy->name ?? 'Tim' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-600 text-sm">
                                Rp {{ number_format($pay->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $statusClasses = match($pay->status) {
                                    'PENDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'VERIFIED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'VOIDED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $statusClasses }}">
                                    {{ $pay->status }}
                                </span>
                                @if($pay->status === 'VERIFIED')
                                <div class="text-[10px] text-emerald-600 font-medium mt-1">Oleh: {{ $pay->verifiedBy->name ?? 'Finance' }}</div>
                                @elseif($pay->status === 'REJECTED')
                                <div class="text-[10px] text-rose-600 mt-1 italic truncate max-w-[140px]" title="{{ $pay->rejection_reason }}">{{ $pay->rejection_reason }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center justify-center gap-2">
                                    {{-- CONTEXTUAL ACTION SYSTEM: ONE PRIMARY ACTION PER PAYMENT --}}
                                    @if($pay->status === 'PENDING' && (Auth::user()->isFinance() || Auth::user()->isSuperAdmin() || Auth::user()->isCeo()))
                                        <button type="button" @click="selectedPayment = {{ Js::from($pay) }}; openDetail = true"
                                                class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                            🔍 Tinjau Pembayaran
                                        </button>
                                    @else
                                        <button type="button" @click="selectedPayment = {{ Js::from($pay) }}; openDetail = true"
                                                class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-2xs transition cursor-pointer">
                                            Lihat Detail
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">Belum ada catatan transaksi pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL: CATAT PEMBAYARAN BARU -->
    <x-viscode-modal name="openCreate" title="Catat Pembayaran Masuk dari Klien" maxWidth="md">
        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tautkan ke Pesanan Proyek <span class="text-rose-500">*</span></label>
                <select name="order_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="">-- Pilih Pesanan Aktif --</option>
                    @foreach($activeOrders as $ord)
                        <option value="{{ $ord->id }}">
                            {{ $ord->order_code }} - {{ $ord->customer->name ?? '-' }} (Deal: Rp {{ number_format($ord->agreed_price, 0, ',', '.') }} | Sisa: Rp {{ number_format($ord->remainingBalance(), 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <x-viscode-rupiah-input
                name="amount"
                label="Nominal Pembayaran"
                placeholder="Contoh: 500.000"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="payment_method" value="Transfer Bank / QRIS" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Upload Bukti Transfer / Struk</label>
                <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tambahan</label>
                <input type="text" name="notes" placeholder="Misal: Pembayaran DP 50% via BCA..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Pembayaran</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: TOLAK PEMBAYARAN -->
    <x-viscode-modal name="openReject" title="Tolak Transaksi Pembayaran" maxWidth="md">
        <form :action="`/payments/${selectedPayment.id}/reject`" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs leading-relaxed">
                Pembayaran <strong x-text="selectedPayment.payment_code"></strong> akan ditolak. Alasan penolakan wajib dicatat untuk transparansi audit.
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Penolakan (Wajib) <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Contoh: Bukti transfer tidak valid / mutasi rekening belum masuk..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openReject = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Tolak Pembayaran</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: VOID PEMBAYARAN -->
    <x-viscode-modal name="openVoid" title="Koreksi / Batalkan Pembayaran (VOID)" maxWidth="md">
        <form :action="`/payments/${selectedPayment.id}/void`" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs leading-relaxed">
                ⚠️ Pembatalan/Void pembayaran akan otomatis mengarsipkan mutasi kas masuk terkait dan menghitung ulang status pembayaran pesanan. Riwayat transaksi tetap tersimpan dalam sistem.
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Pembatalan / Void (Wajib) <span class="text-rose-500">*</span></label>
                <textarea name="void_reason" rows="3" required placeholder="Contoh: Koreksi entri ganda / transaksi dikembalikan kepada klien..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openVoid = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Konfirmasi VOID</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: DETAIL PEMBAYARAN -->
    <x-viscode-modal name="openDetail" title="Detail Bukti & Catatan Pembayaran" maxWidth="lg">
        <div class="space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="font-mono text-blue-600 font-bold" x-text="selectedPayment.payment_code"></span>
                    <h4 class="text-slate-900 font-bold text-sm mt-0.5" x-text="`Order ${selectedPayment.order?.order_code}`"></h4>
                </div>
                <div class="text-right">
                    <span class="text-slate-500">Nominal:</span>
                    <div class="text-lg font-bold text-emerald-600" x-text="`Rp ${Number(selectedPayment.amount).toLocaleString('id-ID')}`"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-500">Metode Bayar:</span>
                    <div class="font-semibold text-slate-900 mt-0.5" x-text="selectedPayment.payment_method"></div>
                </div>
                <div>
                    <span class="text-slate-500">Status Pembayaran:</span>
                    <div class="font-bold text-blue-700 mt-0.5" x-text="selectedPayment.status"></div>
                </div>
            </div>

            <div x-show="selectedPayment.proof_image">
                <span class="text-slate-700 font-semibold block mb-1">Bukti Transfer / Struk:</span>
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 flex justify-center">
                    <img :src="`/storage/${selectedPayment.proof_image}`" alt="Bukti Transfer" class="max-h-64 rounded-lg object-contain">
                </div>
            </div>

            <div x-show="selectedPayment.notes">
                <span class="text-slate-700 font-semibold block mb-0.5">Catatan:</span>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-700" x-text="selectedPayment.notes"></div>
            </div>

            <div x-show="selectedPayment.rejection_reason" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <span class="font-bold">Alasan Ditolak:</span> <span x-text="selectedPayment.rejection_reason"></span>
            </div>

            <div x-show="selectedPayment.void_reason" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                <span class="font-bold">Alasan Void:</span> <span x-text="selectedPayment.void_reason"></span>
            </div>

            <!-- Contextual Review & Secondary Actions inside Detail Dialog -->
            @if(Auth::user()->isFinance() || Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
            <template x-if="selectedPayment.status === 'PENDING'">
                <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form :action="`/payments/${selectedPayment.id}/verify`" method="POST"
                          onsubmit="return window.confirmFormSubmit(event, {
                              title: 'Verifikasi Pembayaran Ini?',
                              html: 'Verifikasi pembayaran sebesar <b>Rp ' + Number(selectedPayment.amount || 0).toLocaleString('id-ID') + '</b> untuk Order <b>' + (selectedPayment.order?.order_code || '') + '</b>?<br><br>Status akan berubah menjadi <span class=\'text-emerald-600 font-bold\'>VERIFIED</span> dan dicatat ke buku kas.',
                              icon: 'question',
                              confirmButtonText: '✓ Verifikasi Pembayaran',
                              confirmButtonColor: '#16A34A'
                          })">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                            ✓ Verifikasi Pembayaran
                        </button>
                    </form>

                    <button type="button" @click="openDetail = false; openReject = true"
                            class="w-full py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl transition cursor-pointer">
                        ✕ Tolak Pembayaran
                    </button>
                </div>
            </template>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    @if(Auth::user()->isFinance() || Auth::user()->isSuperAdmin())
                    <template x-if="selectedPayment.status === 'VERIFIED'">
                        <button type="button" @click="openDetail = false; openVoid = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg cursor-pointer">
                            Void / Koreksi
                        </button>
                    </template>
                    @endif
                    @if(Auth::user()->isSuperAdmin())
                    <button type="button" @click="openDetail = false; overrideRecordId = selectedPayment.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer">
                        ⚡ Admin Override
                    </button>
                    @endif
                </div>
                <button type="button" @click="openDetail = false" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE -->
    <x-viscode-override-modal module="payments" :statuses="['PENDING', 'VERIFIED', 'REJECTED', 'VOIDED']" />
</x-app-layout>
