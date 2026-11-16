<x-app-layout x-data="{ 
    activeFilter: '{{ request()->routeIs('quotations.*') ? 'waiting_response' : request('filter', 'all') }}',
    mainTab: '{{ request('tab', 'sales_orders') }}',
    openCreateQuo: false,
    openCreateOrder: {{ request()->has('create') ? 'true' : 'false' }}, 
    openQuoResponse: false,
    openQuoDetail: false,
    openEdit: false, 
    openPay: false,
    openReviewPayment: false,
    openCreateProject: false,
    openDelivery: false,
    openCancel: false,
    openDetail: false,
    openOverride: false,
    overrideModule: 'orders',
    createMode: 'existing',
    editData: {}, 
    selectedQuo: {},
    selectedOrder: {},
    selectedPayment: {},
    overrideRecordId: null,
    customersList: {{ Js::from($customers) }},
    onSelectCustomer(e) {
        const custId = e.target.value;
        if (!custId) return;
        const cust = this.customersList.find(c => c.id == custId);
        if (cust) {
            this.$refs.custName.value = cust.name;
            this.$refs.custPhone.value = cust.phone;
            this.$refs.custType.value = cust.type;
        }
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Sales & Orders — Manajemen Penawaran & Pesanan Terpadu
                </h2>
                <p class="text-xs text-slate-500 mt-1">Alur penjualan satu pintu: Penawaran (Quotation) &rarr; Persetujuan Klien &rarr; Konversi Pesanan Otomatis &rarr; Pembayaran &rarr; Eksekusi Proyek</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="openCreateQuo = true" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>+ Buat Penawaran</span>
                </button>
                <button type="button" @click="openCreateOrder = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Pesanan Baru</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Segmented Workspace Navigation -->
        <x-viscode-workspace-tabs workspace="business" :active="request('tab', 'sales_orders')" />

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">&times;</button>
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

        <!-- ========================================================= -->
        <!-- TAB: SERVICES & PACKAGES                                  -->
        <!-- ========================================================= -->
        <div x-show="mainTab === 'services'" x-cloak class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Katalog Layanan & Paket Standar Viscode Creative</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar paket layanan aktif yang otomatis direferensikan saat membuat Penawaran maupun Pesanan</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">{{ $services->count() }} Paket Aktif</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($services as $svc)
                <div class="p-5 rounded-2xl border border-slate-200 hover:border-blue-300 bg-slate-50/40 transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                {{ str_replace('_', ' ', $svc->category) }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">Est. {{ $svc->estimated_days ?? 7 }} Hari</span>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm mt-3">{{ $svc->name }}</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $svc->description }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-200/70 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Harga Dasar</div>
                            <div class="text-sm font-bold text-slate-900 font-mono">Rp {{ number_format($svc->base_price, 0, ',', '.') }}</div>
                        </div>
                        <button type="button" @click="openCreateQuo = true" class="px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-600 border border-slate-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                            Buat Penawaran &rarr;
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB: UNIFIED SALES & ORDERS (Quotations + Orders)         -->
        <!-- ========================================================= -->
        <div x-show="mainTab !== 'services'" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <!-- 5 Internal Workflow Filter Tabs -->
            @php
                $waitingResponseCount = $quotations->whereIn('status', ['DRAFT', 'SENT', 'ACCEPTED'])->count();
                $waitingPaymentCount = $orders->getCollection()->filter(fn($o) => $o->status !== 'CANCELLED' && ($o->status === 'PENDING_PAYMENT' || $o->payment_status !== 'PAID'))->count();
                $activeOrdersTabCount = $orders->getCollection()->whereIn('status', ['CONFIRMED', 'IN_PROGRESS'])->count();
                $completedOrdersTabCount = $orders->getCollection()->where('status', 'COMPLETED')->count();
                $allRecordsCount = $quotations->count() + $orders->total();
            @endphp
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                    <button type="button" @click="activeFilter = 'all'"
                            :class="activeFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>All Records</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600'">{{ $allRecordsCount }}</span>
                    </button>

                    <button type="button" @click="activeFilter = 'waiting_response'"
                            :class="activeFilter === 'waiting_response' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Waiting for Response</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeFilter === 'waiting_response' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'">{{ $waitingResponseCount }}</span>
                    </button>

                    <button type="button" @click="activeFilter = 'waiting_payment'"
                            :class="activeFilter === 'waiting_payment' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Waiting for Payment</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeFilter === 'waiting_payment' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700'">{{ $waitingPaymentCount }}</span>
                    </button>

                    <button type="button" @click="activeFilter = 'active'"
                            :class="activeFilter === 'active' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Confirmed / Active</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeFilter === 'active' ? 'bg-indigo-500 text-white' : 'bg-indigo-50 text-indigo-700'">{{ $activeOrdersTabCount }}</span>
                    </button>

                    <button type="button" @click="activeFilter = 'completed'"
                            :class="activeFilter === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Completed</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeFilter === 'completed' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-700'">{{ $completedOrdersTabCount }}</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate-500">
                    Menampilkan tindakan kontekstual berikutnya sesuai tahapan aktif
                </div>
            </div>

            <!-- UNIFIED TABLE OF QUOTATIONS & ORDERS -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Kode & Tahap</th>
                            <th class="py-3.5 px-4">Pelanggan</th>
                            <th class="py-3.5 px-4">Layanan & Rincian</th>
                            <th class="py-3.5 px-4">Status Workflow</th>
                            <th class="py-3.5 px-4 text-right">Nilai / Tagihan</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Utama (Next Action)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">

                        <!-- ================================================= -->
                        <!-- PART 1: ACTIVE QUOTATIONS (Not yet converted)     -->
                        <!-- ================================================= -->
                        @foreach($quotations as $quo)
                        <tr x-show="activeFilter === 'all' || activeFilter === 'waiting_response'"
                            class="hover:bg-blue-50/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">QUOTATION</span>
                                    <span class="font-mono font-bold text-slate-900">{{ $quo->quotation_code }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Berlaku s/d {{ $quo->valid_until ? $quo->valid_until->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $quo->customer->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $quo->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $quo->title }}</div>
                                <div class="text-[11px] text-slate-500">{{ $quo->service->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $quoBadge = match($quo->status) {
                                    'DRAFT' => ['bg-slate-100 text-slate-700 border-slate-200', 'Draft Penawaran'],
                                    'SENT' => ['bg-blue-50 text-blue-700 border-blue-200', 'Menunggu Respon Klien'],
                                    'ACCEPTED' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Disetujui Klien (Siap Order)'],
                                    'REJECTED' => ['bg-rose-50 text-rose-700 border-rose-200', 'Ditolak Klien'],
                                    'EXPIRED' => ['bg-amber-50 text-amber-700 border-amber-200', 'Kedaluwarsa'],
                                    default => ['bg-slate-100 text-slate-700 border-slate-200', $quo->status],
                                };
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $quoBadge[0] }}">
                                    {{ $quo->status }}
                                </span>
                                <div class="text-[10px] text-slate-500 mt-1">{{ $quoBadge[1] }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-bold text-slate-900 text-sm font-mono">Rp {{ number_format($quo->total_amount, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Est. DP 50%: Rp {{ number_format($quo->total_amount * 0.5, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    {{-- SINGLE PRIMARY ACTION FOR QUOTATION --}}
                                    @if($quo->status === 'DRAFT')
                                        <form action="{{ route('quotations.send', $quo) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Kirim Penawaran Resmi?',
                                                  html: 'Kirimkan surat penawaran <b>{{ $quo->quotation_code }}</b> senilai <b>Rp {{ number_format($quo->total_amount, 0, ',', '.') }}</b> kepada <b>{{ addslashes($quo->customer->name ?? '') }}</b>?<br><br>Status akan beralih ke <span class=\'text-blue-600 font-bold\'>SENT</span>.',
                                                  icon: 'question',
                                                  confirmButtonText: 'Ya, Kirim Penawaran',
                                                  confirmButtonColor: '#2563EB'
                                              })">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                Kirim Penawaran &rarr;
                                            </button>
                                        </form>
                                    @elseif($quo->status === 'SENT')
                                        <button type="button" @click="selectedQuo = {{ Js::from($quo) }}; openQuoResponse = true"
                                                class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                            Catat Respon Klien
                                        </button>
                                    @elseif($quo->status === 'ACCEPTED')
                                        <form action="{{ route('quotations.convert-order', $quo) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Konversi Menjadi Pesanan (Order)?',
                                                  html: 'Konversi penawaran yang disetujui ini menjadi Pesanan Proyek untuk <b>{{ addslashes($quo->customer->name ?? '') }}</b> dengan nilai total <b>Rp {{ number_format($quo->total_amount, 0, ',', '.') }}</b>?<br><br>Data pelanggan, layanan, dan harga akan otomatis digunakan kembali.',
                                                  icon: 'success',
                                                  confirmButtonText: '⚡ Konversi ke Order',
                                                  confirmButtonColor: '#16A34A'
                                              })">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                ⚡ Konversi ke Order
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Secondary Detail Trigger -->
                                    <button type="button" @click="selectedQuo = {{ Js::from($quo) }}; openQuoDetail = true"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg border border-transparent hover:border-slate-200 transition cursor-pointer" title="Detail & Riwayat Quotation">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        <!-- ================================================= -->
                        <!-- PART 2: ORDERS (Connected Workflow Records)       -->
                        <!-- ================================================= -->
                        @forelse($orders as $order)
                        @php
                            $isWaitingPayment = $order->status !== 'CANCELLED' && ($order->status === 'PENDING_PAYMENT' || $order->payment_status !== 'PAID');
                            $isActiveOrder = in_array($order->status, ['CONFIRMED', 'IN_PROGRESS']);
                            $isCompletedOrder = $order->status === 'COMPLETED';
                            $pendingPaymentRecord = $order->payments->where('status', 'PENDING')->first();
                        @endphp
                        <tr x-show="activeFilter === 'all' 
                                || (activeFilter === 'waiting_payment' && {{ $isWaitingPayment ? 'true' : 'false' }})
                                || (activeFilter === 'active' && {{ $isActiveOrder ? 'true' : 'false' }})
                                || (activeFilter === 'completed' && {{ $isCompletedOrder ? 'true' : 'false' }})"
                            class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">ORDER</span>
                                    <span class="font-mono font-bold text-blue-600">{{ $order->order_code }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $order->created_at->format('d M Y') }}
                                    @if($order->quotation)
                                        &bull; Dari <span class="font-mono text-slate-500">{{ $order->quotation->quotation_code }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $order->customer->name ?? '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $order->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $order->service->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">
                                    PIC: {{ $order->pic->name ?? 'Belum Ditugaskan' }}
                                    @if($order->project)
                                        &bull; <span class="font-mono text-blue-600">{{ $order->project->project_code }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $statusClasses = match($order->status) {
                                    'PENDING_PAYMENT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'CONFIRMED' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'IN_PROGRESS' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'COMPLETED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full border {{ $statusClasses }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                    @if($order->payment_status === 'PAID')
                                        <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">LUNAS</span>
                                    @elseif($order->payment_status === 'PARTIALLY_PAID')
                                        <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-full bg-amber-50 text-amber-700 border border-amber-200">DP MASUK</span>
                                    @endif
                                </div>
                                @if($pendingPaymentRecord)
                                    <div class="text-[10px] text-amber-600 font-semibold mt-1">⏳ Pembayaran menunggu verifikasi</div>
                                @elseif($order->delivery_date)
                                    <div class="text-[10px] text-blue-600 font-semibold mt-1">📦 Hasil diserahkan {{ $order->delivery_date->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-bold text-slate-900 text-sm font-mono">Rp {{ number_format($order->agreed_price, 0, ',', '.') }}</div>
                                @if($order->remainingBalance() > 0 && $order->status !== 'CANCELLED')
                                    <div class="text-[10px] text-rose-600 font-mono mt-0.5">Sisa: Rp {{ number_format($order->remainingBalance(), 0, ',', '.') }}</div>
                                @else
                                    <div class="text-[10px] text-emerald-600 font-mono mt-0.5">Terbayar: Rp {{ number_format($order->totalVerifiedPayments(), 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    {{-- CONTEXTUAL ACTION SYSTEM: ONE PRIMARY NEXT ACTION BASED ON STATUS --}}
                                    @if($order->status === 'PENDING_PAYMENT')
                                        @if($pendingPaymentRecord && (Auth::user()->isFinance() || Auth::user()->isCeo() || Auth::user()->isSuperAdmin()))
                                            <button type="button" @click="selectedOrder = {{ Js::from($order) }}; selectedPayment = {{ Js::from($pendingPaymentRecord) }}; openReviewPayment = true"
                                                    class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                🔍 Tinjau Pembayaran
                                            </button>
                                        @elseif($pendingPaymentRecord)
                                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold">
                                                Menunggu Verifikasi Finance
                                            </span>
                                        @else
                                            <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openPay = true"
                                                    class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                💳 Catat Pembayaran DP
                                            </button>
                                        @endif

                                    @elseif($order->status === 'CONFIRMED')
                                        @if(!$order->project)
                                            <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openCreateProject = true"
                                                    class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                🚀 Buat Proyek
                                            </button>
                                        @else
                                            <a href="{{ route('projects.index') }}"
                                               class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold rounded-xl transition">
                                                📋 Buka Proyek &rarr;
                                            </a>
                                        @endif

                                    @elseif($order->status === 'IN_PROGRESS')
                                        @if($order->project && $order->project->status === 'COMPLETED' && !$order->delivery_date)
                                            <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openDelivery = true"
                                                    class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                📦 Serahkan Hasil
                                            </button>
                                        @elseif(($order->delivery_date || ($order->project && $order->project->status === 'COMPLETED')) && $order->payment_status !== 'PAID')
                                            @if($pendingPaymentRecord && (Auth::user()->isFinance() || Auth::user()->isCeo() || Auth::user()->isSuperAdmin()))
                                                <button type="button" @click="selectedOrder = {{ Js::from($order) }}; selectedPayment = {{ Js::from($pendingPaymentRecord) }}; openReviewPayment = true"
                                                        class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                    🔍 Verifikasi Pelunasan
                                                </button>
                                            @else
                                                <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openPay = true"
                                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                    💳 Catat Pelunasan
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ route('projects.index') }}"
                                               class="px-3.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl transition">
                                                📋 Progress Proyek &rarr;
                                            </a>
                                        @endif

                                    @elseif($order->status === 'COMPLETED')
                                        <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openDetail = true"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                                            Riwayat Selesai
                                        </button>
                                    @endif

                                    <!-- Secondary Detail & Contextual Menu Trigger -->
                                    @if($order->status !== 'COMPLETED')
                                    <button type="button" @click="selectedOrder = {{ Js::from($order) }}; openDetail = true"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg border border-transparent hover:border-slate-200 transition cursor-pointer" title="Detail Pesanan & Opsi Lainnya">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            @if($quotations->isEmpty())
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 text-xs">Belum ada data penawaran maupun pesanan proyek.</td>
                            </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- ================================================================= -->
    <!-- MODAL 1: CREATE QUOTATION                                         -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateQuo" title="Buat Surat Penawaran (Quotation) Baru" maxWidth="2xl">
        <form action="{{ route('quotations.store') }}" method="POST"
              x-data="{ submitAct: 'send' }"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Terbitkan Surat Penawaran?',
                  text: 'Penawaran akan disimpan sesuai opsi yang Anda pilih.',
                  icon: 'question',
                  confirmButtonText: 'Ya, Simpan Penawaran',
                  confirmButtonColor: '#2563EB'
              })"
              class="space-y-5">
            @csrf
            <input type="hidden" name="submit_action" :value="submitAct">

            <x-viscode-quotation-form-fields
                :services="$services"
                :customers="$customers"
                :showCustomerSelect="true" />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 flex-wrap">
                <button type="button" @click="openCreateQuo = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" @click="submitAct = 'draft'" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                    📝 Simpan Draft
                </button>
                <button type="submit" @click="submitAct = 'send'" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                    📤 Simpan &amp; Kirim Penawaran
                </button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 2: CONTEXTUAL QUOTATION CUSTOMER RESPONSE DIALOG (SENT)     -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openQuoResponse" title="Catat Respon Pelanggan atas Penawaran" maxWidth="md">
        <div class="space-y-4 text-xs" x-data="{ showRejectReason: false }">
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-blue-700" x-text="selectedQuo.quotation_code"></span>
                    <span class="font-bold font-mono text-slate-900" x-text="`Rp ${Number(selectedQuo.total_amount || 0).toLocaleString('id-ID')}`"></span>
                </div>
                <div class="font-bold text-slate-900" x-text="selectedQuo.title"></div>
                <div class="text-slate-600">Pelanggan: <strong x-text="selectedQuo.customer?.name"></strong></div>
            </div>

            <div x-show="!showRejectReason" class="space-y-3 pt-1">
                <p class="text-slate-600">Pilih keputusan pelanggan terhadap surat penawaran di atas:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <form :action="`/quotations/${selectedQuo.id}/accept`" method="POST"
                          onsubmit="return window.confirmFormSubmit(event, {
                              title: 'Catat Persetujuan Klien?',
                              html: 'Pelanggan <b>' + (selectedQuo.customer?.name || '') + '</b> menyetujui penawaran <b>' + (selectedQuo.quotation_code || '') + '</b> senilai <b>Rp ' + Number(selectedQuo.total_amount || 0).toLocaleString('id-ID') + '</b>?<br><br>Status akan menjadi <span class=\'text-emerald-600 font-bold\'>ACCEPTED</span> dan siap dikonversi menjadi Order.',
                              icon: 'success',
                              confirmButtonText: 'Ya, Klien Setuju',
                              confirmButtonColor: '#16A34A'
                          })">
                        @csrf
                        <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer flex items-center justify-center gap-2">
                            <span>✓ Klien Menyetujui</span>
                        </button>
                    </form>

                    <button type="button" @click="showRejectReason = true"
                            class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl transition cursor-pointer">
                        ✕ Klien Menolak
                    </button>
                </div>
            </div>

            <form x-show="showRejectReason" :action="`/quotations/${selectedQuo.id}/reject`" method="POST"
                  onsubmit="return window.confirmFormSubmit(event, {
                      title: 'Catat Penolakan Penawaran?',
                      text: 'Status quotation akan menjadi REJECTED dan alasan penolakan disimpan.',
                      icon: 'warning',
                      confirmButtonText: 'Simpan Penolakan',
                      confirmButtonColor: '#DC2626'
                  })"
                  class="space-y-3 pt-2 border-t border-slate-100">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alasan Penolakan Klien <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Contoh: Penyesuaian anggaran / proyek ditunda..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-xs"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showRejectReason = false" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 font-semibold cursor-pointer">Kembali</button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg cursor-pointer">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 3: QUOTATION DETAIL                                         -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openQuoDetail" title="Detail Surat Penawaran (Quotation)" maxWidth="lg">
        <div class="space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="font-mono text-blue-600 font-bold" x-text="selectedQuo.quotation_code"></span>
                    <h4 class="text-slate-900 font-bold text-sm mt-0.5" x-text="selectedQuo.title"></h4>
                </div>
                <div class="text-right">
                    <span class="text-slate-500">Total Penawaran:</span>
                    <div class="text-lg font-bold text-emerald-600 font-mono" x-text="`Rp ${Number(selectedQuo.total_amount || 0).toLocaleString('id-ID')}`"></div>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-600 uppercase text-[10px]">Cakupan Pekerjaan (Scope of Work):</label>
                <pre class="p-3 mt-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-wrap font-sans text-xs" x-text="selectedQuo.scope_description"></pre>
            </div>

            <div x-show="selectedQuo.rejection_reason" class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                <span class="font-bold">Alasan Penolakan:</span> <span x-text="selectedQuo.rejection_reason"></span>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div>
                    @if(Auth::user()->isSuperAdmin())
                    <button type="button" @click="openQuoDetail = false; overrideModule = 'quotations'; overrideRecordId = selectedQuo.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer">
                        ⚡ Admin Override Status
                    </button>
                    @endif
                </div>
                <button type="button" @click="openQuoDetail = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 4: TAMBAH PESANAN (CREATE ORDER)                            -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateOrder" title="Catat Pesanan Proyek Baru" maxWidth="lg">
        <form action="{{ route('orders.store') }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Pesanan Proyek Baru?',
                  text: 'Pesanan akan dicatat dengan status PENDING_PAYMENT (Menunggu DP).',
                  icon: 'question',
                  confirmText: 'Ya, Simpan Pesanan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf

            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Sumber Data Pelanggan</span>
                    <div class="flex items-center gap-3 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
                            <input type="radio" name="source_mode" value="existing" x-model="createMode" class="text-blue-600 focus:ring-blue-500">
                            Pelanggan Terdaftar
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-700">
                            <input type="radio" name="source_mode" value="new" x-model="createMode" class="text-blue-600 focus:ring-blue-500">
                            Klien Baru
                        </label>
                    </div>
                </div>

                <div x-show="createMode === 'existing'">
                    <select name="customer_id" @change="onSelectCustomer($event)" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="">-- Pilih dari database pelanggan (Otomatis isi kontak) --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }}) - {{ strtoupper($c->type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Pelanggan / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" x-ref="custName" name="customer_name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" x-ref="custPhone" name="customer_whatsapp" placeholder="08xxxxxxxxxx" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Segmen Pelanggan</label>
                    <select x-ref="custType" name="customer_type" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="umkm">UMKM / Usaha Lokal</option>
                        <option value="organisasi">Organisasi Kampus / Sekolah</option>
                        <option value="mahasiswa">Mahasiswa / Komunitas</option>
                        <option value="event_organizer">Event Organizer</option>
                        <option value="individu">Individu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Layanan / Paket <span class="text-rose-500">*</span></label>
                    <select name="service_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->base_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-viscode-rupiah-input name="agreed_price" label="Harga Deal Pekerjaan" :required="true" placeholder="Contoh: 1.000.000" />
                <x-viscode-rupiah-input name="down_payment_required" label="Ketentuan DP Minimal (Opsional)" placeholder="Contoh: 500.000 (Kosong = 50%)" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">PIC Pengerjaan</label>
                    <select name="pic_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="">-- Belum Ditentukan --</option>
                        @foreach($team as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Deadline Selesai</label>
                    <input type="date" name="deadline" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deskripsi Brief Kebutuhan Proyek <span class="text-rose-500">*</span></label>
                <textarea name="brief_description" rows="3" required placeholder="Jelaskan kebutuhan spesifik dari klien..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateOrder = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Pesanan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 5: CATAT PEMBAYARAN UNTUK PESANAN (MINIMAL INPUT)           -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openPay" title="Catat Pembayaran DP / Pelunasan" maxWidth="md">
        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Pembayaran untuk ' + (selectedOrder.order_code || 'Pesanan') + '?',
                  text: 'Pembayaran akan dicatat berstatus PENDING hingga diverifikasi oleh Finance.',
                  icon: 'info',
                  confirmText: 'Simpan Pembayaran',
                  confirmColor: '#16A34A'
              })"
              class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" :value="selectedOrder.id">

            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 leading-relaxed">
                <div>Pesanan: <strong class="text-blue-700 font-mono" x-text="selectedOrder.order_code"></strong> (<span x-text="selectedOrder.customer?.name"></span>)</div>
                <div>Total Deal: <strong class="text-slate-900" x-text="`Rp ${Number(selectedOrder.agreed_price || 0).toLocaleString('id-ID')}`"></strong> &bull; Sisa Tagihan: <strong class="text-rose-600" x-text="`Rp ${Number(selectedOrder.remaining_balance || 0).toLocaleString('id-ID')}`"></strong></div>
            </div>

            <x-viscode-rupiah-input name="amount" label="Nominal Pembayaran Diterima" :required="true" placeholder="Contoh: 500.000" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Bayar <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Metode Bayar <span class="text-rose-500">*</span></label>
                    <input type="text" name="payment_method" value="Transfer Bank / QRIS" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Upload Bukti Transfer (Opsional)</label>
                <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Singkat (Opsional)</label>
                <input type="text" name="notes" placeholder="DP 50% / Pelunasan Akhir..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openPay = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Pembayaran</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 6: CONTEXTUAL PAYMENT REVIEW DIALOG (VERIFY / REJECT)       -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openReviewPayment" title="Tinjau & Verifikasi Pembayaran Masuk" maxWidth="md">
        <div class="space-y-4 text-xs" x-data="{ showRejectPay: false }">
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-amber-800" x-text="selectedPayment.payment_code"></span>
                    <span class="text-base font-bold font-mono text-emerald-700" x-text="`Rp ${Number(selectedPayment.amount || 0).toLocaleString('id-ID')}`"></span>
                </div>
                <div>Order: <strong class="font-mono" x-text="selectedOrder.order_code"></strong> &bull; Klien: <strong x-text="selectedOrder.customer?.name"></strong></div>
                <div>Metode: <span x-text="selectedPayment.payment_method"></span></div>
            </div>

            <div x-show="selectedPayment.proof_image">
                <span class="text-slate-600 font-bold block mb-1">Bukti Pembayaran:</span>
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 flex justify-center">
                    <img :src="`/storage/${selectedPayment.proof_image}`" alt="Bukti Transfer" class="max-h-56 rounded-lg object-contain">
                </div>
            </div>

            <div x-show="!showRejectPay" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <form :action="`/payments/${selectedPayment.id}/verify`" method="POST"
                      onsubmit="return window.confirmFormSubmit(event, {
                          title: 'Verifikasi Pembayaran Ini?',
                          html: 'Verifikasi pembayaran sebesar <b>Rp ' + Number(selectedPayment.amount || 0).toLocaleString('id-ID') + '</b> untuk Order <b>' + (selectedOrder.order_code || '') + '</b>?<br><br>Sistem akan otomatis memperbarui status pembayaran order dan mencatat kas masuk.',
                          icon: 'question',
                          confirmButtonText: '✓ Verifikasi Pembayaran',
                          confirmButtonColor: '#16A34A'
                      })">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                        ✓ Verifikasi Pembayaran
                    </button>
                </form>

                <button type="button" @click="showRejectPay = true"
                        class="w-full py-3 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl transition cursor-pointer">
                    ✕ Tolak Pembayaran
                </button>
            </div>

            <form x-show="showRejectPay" :action="`/payments/${selectedPayment.id}/reject`" method="POST"
                  onsubmit="return window.confirmFormSubmit(event, {
                      title: 'Tolak Pembayaran?',
                      text: 'Pembayaran akan ditandai REJECTED dan alasan dicatat.',
                      icon: 'warning',
                      confirmButtonText: 'Tolak Pembayaran',
                      confirmButtonColor: '#DC2626'
                  })"
                  class="space-y-3 pt-2 border-t border-slate-100">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="rejection_reason" rows="2" required placeholder="Mutasi belum masuk / bukti tidak sesuai..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-xs"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showRejectPay = false" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 font-semibold cursor-pointer">Kembali</button>
                    <button type="submit" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg cursor-pointer">Simpan Penolakan</button>
                </div>
            </form>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 7: BUAT PROYEK DARI PESANAN                                 -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateProject" title="Buat Proyek Operasional dari Pesanan" maxWidth="md">
        <form action="{{ route('projects.store') }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Inisialisasi Proyek ' + (selectedOrder.order_code || '') + '?',
                  text: 'Proyek operasional akan dibuat dan tim teknis dapat langsung mulai mengerjakan tugas.',
                  icon: 'info',
                  confirmText: 'Mulai Proyek',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" :value="selectedOrder.id">

            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs leading-relaxed">
                Pesanan <strong class="text-blue-700 font-mono" x-text="selectedOrder.order_code"></strong> (<span x-text="selectedOrder.customer?.name"></span>) sudah terkonfirmasi.
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama / Judul Proyek <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required :value="`Proyek ${selectedOrder.order_code || ''}`" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Template Tugas Standar</label>
                <select name="apply_template" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="web_development">Web Development Standard (5 Tugas Berurutan)</option>
                    <option value="graphic_design">Graphic Design & Branding (4 Tugas)</option>
                    <option value="bundling">Paket Bundling Komprehensif (4 Tugas)</option>
                    <option value="">-- Tanpa Template (Buat Manual) --</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Project Manager <span class="text-rose-500">*</span></label>
                <select name="project_manager_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    @foreach($team as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role_label }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Target Selesai</label>
                    <input type="date" name="target_completion_date" :value="selectedOrder.deadline ? selectedOrder.deadline.substring(0,10) : ''" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateProject = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Inisialisasi Proyek</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 8: SERAHKAN DELIVERABLES / HASIL PEKERJAAN                  -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openDelivery" title="Serahkan Hasil Pekerjaan (Deliverables)" maxWidth="md">
        <form :action="`/orders/${selectedOrder.id}/delivery`" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Serahkan Deliverables ' + (selectedOrder.order_code || '') + '?',
                  text: 'Sistem akan mencatat penyerahan deliverables. Jika pembayaran sudah lunas 100%, status pesanan otomatis COMPLETED.',
                  icon: 'info',
                  confirmText: 'Konfirmasi Penyerahan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf

            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 leading-relaxed">
                <div>Pesanan: <strong class="text-indigo-800 font-mono" x-text="selectedOrder.order_code"></strong> &bull; Klien: <strong class="text-slate-900" x-text="selectedOrder.customer?.name"></strong></div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Penyerahan Hasil <span class="text-rose-500">*</span></label>
                <input type="date" name="delivery_date" required :value="selectedOrder.delivery_date ? selectedOrder.delivery_date.substring(0,10) : '{{ date('Y-m-d') }}'" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Link File Hasil / Production URL <span class="text-rose-500">*</span></label>
                <input type="url" name="result_file_url" required :value="selectedOrder.result_file_url" placeholder="https://drive.google.com/... atau https://viscode.id" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Penyerahan / Panduan Klien</label>
                <textarea name="delivery_notes" rows="3" :value="selectedOrder.delivery_notes" placeholder="Seluruh aset desain dan akses website telah dikirimkan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openDelivery = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan & Serahkan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 9: BATALKAN PESANAN                                         -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCancel" title="Batalkan Pesanan Proyek" maxWidth="md">
        <form :action="`/orders/${selectedOrder.id}/cancel`" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Batalkan Pesanan ' + (selectedOrder.order_code || '') + '?',
                  text: 'PERINGATAN: Tindakan ini permanen. Status pesanan akan menjadi CANCELLED.',
                  icon: 'warning',
                  confirmText: 'Ya, Batalkan Pesanan',
                  confirmColor: '#DC2626'
              })"
              class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Pembatalan (Wajib) <span class="text-rose-500">*</span></label>
                <textarea name="cancellation_reason" rows="3" required placeholder="Jelaskan alasan resmi pembatalan pesanan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCancel = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Konfirmasi Pembatalan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 10: EDIT DETAIL PESANAN                                     -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openEdit" title="Edit Detail Informasi Pesanan" maxWidth="lg">
        <form :action="`/orders/${editData.id}`" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Perubahan Pesanan ' + (editData.order_code || '') + '?',
                  text: 'Perubahan harga dan detail operasional akan disimpan.',
                  icon: 'question',
                  confirmText: 'Simpan Perubahan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">PIC Pengerjaan</label>
                    <select name="pic_id" x-model="editData.pic_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="">-- Belum Diatur --</option>
                        @foreach($team as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-viscode-rupiah-input
                    name="agreed_price"
                    label="Harga Deal"
                    placeholder="Contoh: 1.500.000"
                    :required="true"
                    alpineValue="editData.agreed_price"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-viscode-rupiah-input
                    name="down_payment_required"
                    label="Ketentuan DP Minimal"
                    placeholder="Contoh: 750.000"
                    alpineValue="editData.down_payment_required"
                />
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Deadline Selesai</label>
                    <input type="date" name="deadline" x-model="editData.deadline" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Link File Hasil (GDrive / Figma / GitHub)</label>
                <input type="url" name="result_file_url" x-model="editData.result_file_url" placeholder="https://..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Brief Kebutuhan <span class="text-rose-500">*</span></label>
                <textarea name="brief_description" x-model="editData.brief_description" rows="2" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openEdit = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 11: DETAIL PESANAN & SECONDARY CONTEXTUAL ACTIONS           -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openDetail" title="Detail Pesanan & Riwayat Terhubung" maxWidth="lg">
        <div class="space-y-4 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-slate-100 gap-2">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-blue-600 font-bold text-sm" x-text="selectedOrder.order_code"></span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-blue-50 text-blue-700 border-blue-200" x-text="selectedOrder.status"></span>
                    </div>
                    <h4 class="text-slate-900 font-bold text-base mt-1" x-text="selectedOrder.service?.name"></h4>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-slate-500 text-[11px]">Nilai Kesepakatan:</span>
                    <div class="text-lg font-bold text-slate-900 font-mono" x-text="`Rp ${Number(selectedOrder.agreed_price || 0).toLocaleString('id-ID')}`"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-800">Pelanggan & PIC</div>
                    <div>Klien: <strong x-text="selectedOrder.customer?.name || '-'"></strong></div>
                    <div>WhatsApp: <span class="font-mono" x-text="selectedOrder.customer?.phone || '-'"></span></div>
                    <div>PIC: <span x-text="selectedOrder.pic?.name || 'Belum Ditugaskan'"></span></div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <div class="font-bold text-slate-800">Status Pembayaran</div>
                    <div>Status: <strong x-text="selectedOrder.payment_status"></strong></div>
                    <div>Terbayar: <strong class="text-emerald-600 font-mono" x-text="`Rp ${Number(selectedOrder.total_paid || 0).toLocaleString('id-ID')}`"></strong></div>
                    <div>Sisa Piutang: <strong class="text-rose-600 font-mono" x-text="`Rp ${Number(selectedOrder.remaining_balance || 0).toLocaleString('id-ID')}`"></strong></div>
                </div>
            </div>

            <template x-if="selectedOrder.result_file_url || selectedOrder.delivery_date">
                <div class="p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-200 space-y-1">
                    <div class="font-bold text-indigo-900">Deliverables & Hasil Pekerjaan</div>
                    <div>Tanggal Serah Terima: <strong x-text="selectedOrder.delivery_date ? selectedOrder.delivery_date.substring(0,10) : '-'"></strong></div>
                    <template x-if="selectedOrder.result_file_url">
                        <div>Link Hasil: <a :href="selectedOrder.result_file_url" target="_blank" class="text-blue-600 hover:underline font-mono" x-text="selectedOrder.result_file_url"></a></div>
                    </template>
                </div>
            </template>

            <div>
                <label class="font-bold text-slate-600 uppercase text-[10px]">Brief Kebutuhan Proyek:</label>
                <pre class="p-3 mt-1 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 whitespace-pre-wrap font-sans text-xs" x-text="selectedOrder.brief_description"></pre>
            </div>

            <!-- Secondary Contextual Actions (Tucked neatly inside Detail) -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <template x-if="selectedOrder.status !== 'CANCELLED' && selectedOrder.status !== 'COMPLETED'">
                        <button type="button" @click="openDetail = false; editData = selectedOrder; openEdit = true" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold rounded-lg cursor-pointer">
                            Edit Informasi
                        </button>
                    </template>
                    <template x-if="selectedOrder.status !== 'CANCELLED' && selectedOrder.status !== 'COMPLETED'">
                        <button type="button" @click="openDetail = false; openDelivery = true" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold rounded-lg cursor-pointer">
                            📦 Serahkan Deliverables
                        </button>
                    </template>
                    <template x-if="!['COMPLETED', 'CANCELLED'].includes(selectedOrder.status)">
                        <button type="button" @click="openDetail = false; openCancel = true" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold rounded-lg cursor-pointer">
                            Batalkan Order
                        </button>
                    </template>
                    @if(Auth::user()->isSuperAdmin())
                    <button type="button" @click="openDetail = false; overrideModule = 'orders'; overrideRecordId = selectedOrder.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer">
                        ⚡ Admin Override
                    </button>
                    @endif
                </div>
                <button type="button" @click="openDetail = false" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE -->
    <x-viscode-override-modal module="orders" :statuses="['PENDING_PAYMENT', 'CONFIRMED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED']" />
</x-app-layout>
