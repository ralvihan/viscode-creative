<x-app-layout x-data="{ 
    openCreate: false, 
    openDetail: false, 
    openReject: false, 
    openOverride: false,
    selectedQuo: {}, 
    overrideRecordId: null 
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Manajemen Penawaran (Quotation)
                </h2>
                <p class="text-xs text-slate-500 mt-1">Penerbitan proposal harga resmi, pelacakan persetujuan, dan konversi ke pesanan</p>
            </div>
            <button type="button" @click="openCreate = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Quotation Baru</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6">

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

        <!-- Quotation Table Container (Pure White) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Daftar Surat Penawaran Resmi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total {{ $quotations->total() }} surat penawaran harga diterbitkan</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 rounded-l-xl">Kode Quotation</th>
                            <th class="py-3.5 px-4">Pelanggan</th>
                            <th class="py-3.5 px-4">Judul & Layanan</th>
                            <th class="py-3.5 px-4 text-right">Total Nilai</th>
                            <th class="py-3.5 px-4">Masa Berlaku</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($quotations as $quo)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                {{ $quo->quotation_code }}
                                <div class="text-[10px] text-slate-400 font-normal">{{ $quo->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $quo->customer->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $quo->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $quo->title }}</div>
                                <div class="text-[11px] text-slate-500">{{ $quo->service->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($quo->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-xs {{ $quo->valid_until < now()->toDateString() && $quo->status === 'SENT' ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                {{ $quo->valid_until ? $quo->valid_until->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $statusClasses = match($quo->status) {
                                    'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'SENT' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'ACCEPTED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'EXPIRED' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'CONVERTED' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $statusClasses }}">
                                    {{ $quo->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <!-- Detail Modal -->
                                    <button type="button" @click="selectedQuo = {{ Js::from($quo) }}; openDetail = true" class="px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">
                                        Detail
                                    </button>

                                    @if($quo->status === 'DRAFT')
                                    <!-- Send to Customer -->
                                    <form action="{{ route('quotations.send', $quo) }}" method="POST" class="inline"
                                          onsubmit="return window.confirmFormSubmit(event, {
                                              title: 'Kirimkan Penawaran Resmi?',
                                              html: 'Anda akan mengirim penawaran <b>{{ $quo->quotation_code }}</b> senilai <b>Rp {{ number_format($quo->total_amount, 0, ',', '.') }}</b> kepada klien <b>{{ addslashes($quo->customer->name) }}</b>.<br><br>Status penawaran akan beralih ke <span class=\'text-blue-600 font-bold\'>SENT</span>.',
                                              icon: 'question',
                                              confirmButtonText: 'Ya, Kirim Penawaran',
                                              confirmButtonColor: '#2563EB'
                                          })">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">
                                            Kirim ↗
                                        </button>
                                    </form>
                                    @endif

                                    @if(in_array($quo->status, ['DRAFT', 'SENT']))
                                    <!-- Customer Accept -->
                                    <form action="{{ route('quotations.accept', $quo) }}" method="POST" class="inline"
                                          onsubmit="return window.confirmFormSubmit(event, {
                                              title: 'Catat Persetujuan Klien?',
                                              html: 'Klien <b>{{ addslashes($quo->customer->name) }}</b> telah menyetujui penawaran <b>{{ $quo->quotation_code }}</b>.<br><br>Status akan beralih ke <span class=\'text-emerald-600 font-bold\'>ACCEPTED</span> dan siap dikonversi ke Pesanan Proyek.',
                                              icon: 'success',
                                              confirmButtonText: 'Catat Disetujui',
                                              confirmButtonColor: '#16A34A'
                                          })">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">
                                            ✓ Setuju
                                        </button>
                                    </form>

                                    <!-- Customer Reject -->
                                    <button type="button" @click="selectedQuo = {{ Js::from($quo) }}; openReject = true" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">
                                        ✕ Tolak
                                    </button>
                                    @endif

                                    @if($quo->status === 'ACCEPTED')
                                    <!-- Convert to Order -->
                                    <form action="{{ route('quotations.convert-order', $quo) }}" method="POST" class="inline"
                                          onsubmit="return window.confirmFormSubmit(event, {
                                              title: 'Konversi Menjadi Pesanan (Order)?',
                                              html: 'Penawaran <b>{{ $quo->quotation_code }}</b> akan otomatis dikonversi menjadi Pesanan Proyek baru.<br><br>Data klien, layanan, harga kesepakatan (<b>Rp {{ number_format($quo->total_amount, 0, ',', '.') }}</b>), dan ketentuan DP 50% akan langsung terhubung tanpa perlu input ulang.',
                                              icon: 'success',
                                              confirmButtonText: '⚡ Buat Pesanan Sekarang',
                                              confirmButtonColor: '#7C3AED'
                                          })">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-sm transition cursor-pointer">
                                            ⚡ Buat Order
                                        </button>
                                    </form>
                                    @endif

                                    @if(Auth::user()->isSuperAdmin())
                                    <!-- Super Admin Override -->
                                    <button type="button" @click="overrideRecordId = {{ $quo->id }}; openOverride = true" class="px-2 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg transition cursor-pointer" title="Super Admin Override Status">
                                        ⚡ Override
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">Belum ada penawaran quotation tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($quotations->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $quotations->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL: CREATE QUOTATION -->
    <x-viscode-modal name="openCreate" title="Buat Penawaran Quotation Baru" maxWidth="lg">
        <form action="{{ route('quotations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Klien / Pelanggan <span class="text-rose-500">*</span></label>
                <select name="customer_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Judul Penawaran Proposal <span class="text-rose-500">*</span></label>
                <input type="text" name="title" placeholder="Pembuatan Landing Page & Desain Feed Instagram" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Layanan Utama <span class="text-rose-500">*</span></label>
                    <select name="service_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name }} (Rp {{ number_format($svc->base_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Total Nilai Penawaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="total_amount" required placeholder="350000" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Masa Berlaku Penawaran <span class="text-rose-500">*</span></label>
                <input type="date" name="valid_until" required value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Rincian Cakupan Pekerjaan (Scope of Work) <span class="text-rose-500">*</span></label>
                <textarea name="scope_description" rows="3" required placeholder="Sertakan daftar fitur, deliverable, dan garansi revisi..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Terbitkan Draft</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: REJECT QUOTATION -->
    <x-viscode-modal name="openReject" title="Catat Penolakan Penawaran (Quotation)" maxWidth="md">
        <form :action="`/quotations/${selectedQuo.id}/reject`" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs leading-relaxed">
                Quotation <strong x-text="selectedQuo.quotation_code"></strong> akan ditandai REJECTED. Alasan wajib dicatat untuk audit dan follow-up.
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Penolakan dari Pelanggan <span class="text-rose-500">*</span></label>
                <textarea name="rejection_reason" rows="3" required placeholder="Contoh: Klien memilih vendor alternatif dengan harga lebih murah / proyek ditunda..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openReject = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Penolakan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: DETAIL QUOTATION -->
    <x-viscode-modal name="openDetail" title="Detail Penawaran (Quotation)" maxWidth="lg">
        <div class="space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="font-mono text-blue-600 font-bold" x-text="selectedQuo.quotation_code"></span>
                    <h4 class="text-slate-900 font-bold text-sm mt-0.5" x-text="selectedQuo.title"></h4>
                </div>
                <div class="text-right">
                    <span class="text-slate-500">Total Penawaran:</span>
                    <div class="text-lg font-bold text-emerald-600" x-text="`Rp ${Number(selectedQuo.total_amount).toLocaleString('id-ID')}`"></div>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-600 uppercase text-[10px]">Cakupan Pekerjaan (Scope of Work):</label>
                <pre class="p-3 mt-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-wrap font-sans text-xs" x-text="selectedQuo.scope_description"></pre>
            </div>

            <div x-show="selectedQuo.rejection_reason" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <span class="font-bold">Alasan Penolakan:</span> <span x-text="selectedQuo.rejection_reason"></span>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" @click="openDetail = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE -->
    <x-viscode-override-modal module="quotations" :statuses="['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'EXPIRED', 'CONVERTED']" />
</x-app-layout>
