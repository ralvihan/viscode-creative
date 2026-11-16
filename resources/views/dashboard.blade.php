<x-app-layout x-data="{ 
    openCreateLead: false, 
    openCreateTrx: false,
    activeWorkflowTab: 'sales'
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Command Center & Action Dashboard
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Selamat datang, <span class="font-semibold text-slate-800">{{ Auth::user()->name }}</span> &bull; Hak Akses: <span class="uppercase font-bold text-blue-600">{{ str_replace('_', ' ', Auth::user()->role) }}</span> &bull; Tim Kewirausahaan Kelompok 8
                </p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                @if(Auth::user()->canAccessModule('leads'))
                <button type="button" @click="openCreateLead = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl transition shadow-sm hover:shadow flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>+ Intake Klien / Lead</span>
                </button>
                @endif
                @if(Auth::user()->canAccessModule('financials'))
                <button type="button" @click="openCreateTrx = true" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Catat Transaksi Kas</span>
                </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">

        <!-- ========================================================= -->
        <!-- SECTION A: BUSINESS OVERVIEW (Compact 6-Metric Summary)   -->
        <!-- ========================================================= -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    A. Business Overview — Ringkasan Kinerja Bulan Ini
                </h3>
                <span class="text-[11px] text-slate-400 font-mono">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Monthly Income</div>
                    <div class="text-base font-bold text-emerald-600 font-mono mt-1.5">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Pemasukan terverifikasi</div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Monthly Expenses</div>
                    <div class="text-base font-bold text-rose-600 font-mono mt-1.5">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Pengeluaran operasional</div>
                </div>

                <div class="bg-white border {{ $netProfit >= 0 ? 'border-blue-200' : 'border-rose-200' }} rounded-2xl p-4 shadow-2xs">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estimated Profit</div>
                    <div class="text-base font-bold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-rose-600' }} font-mono mt-1.5">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400 mt-1">Estimasi laba bersih</div>
                </div>

                <a href="{{ route('orders.index', ['filter' => 'active']) }}" class="bg-white border border-slate-200 hover:border-blue-300 rounded-2xl p-4 shadow-2xs transition">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Active Orders</div>
                    <div class="text-xl font-bold text-slate-900 font-mono mt-1">{{ $activeOrdersCount }}</div>
                    <div class="text-[10px] text-blue-600 font-medium mt-1">Pesanan aktif &rarr;</div>
                </a>

                <a href="{{ route('projects.index') }}" class="bg-white border border-slate-200 hover:border-blue-300 rounded-2xl p-4 shadow-2xs transition">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Active Projects</div>
                    <div class="text-xl font-bold text-slate-900 font-mono mt-1">{{ $activeProjectsCount }}</div>
                    <div class="text-[10px] text-blue-600 font-medium mt-1">Proyek berjalan &rarr;</div>
                </a>

                <a href="{{ route('orders.index', ['filter' => 'waiting_payment']) }}" class="bg-white border border-slate-200 hover:border-amber-300 rounded-2xl p-4 shadow-2xs transition">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Receivables</div>
                    <div class="text-base font-bold text-amber-600 font-mono mt-1.5">Rp {{ number_format($outstandingReceivables, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-amber-600 font-medium mt-1">Piutang tertunda &rarr;</div>
                </a>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- SECTION B: WHAT NEEDS MY ATTENTION?                       -->
        <!-- ========================================================= -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                        </span>
                        B. What Needs My Attention? — Tindakan Prioritas Anda
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Hanya menampilkan rekaman aktif yang memerlukan tindakan langsung dari hak akses Anda ({{ Auth::user()->role_label }}).
                    </p>
                </div>
                <div class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-200 self-start sm:self-center">
                    {{ count($actionCards) }} Menunggu Tindakan
                </div>
            </div>

            @if(count($actionCards) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-5">
                    @foreach($actionCards as $action)
                        <div class="bg-slate-50/60 border border-slate-200/90 hover:border-slate-300 hover:bg-slate-50 rounded-xl p-4 transition-all flex flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $action['status_color'] }} border">
                                        {{ $action['badge'] }}
                                    </span>
                                    <span class="font-mono text-[11px] font-semibold text-slate-500">{{ $action['record_code'] }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mt-2 leading-snug">{{ $action['title'] }}</h4>
                                <div class="text-xs text-slate-700 font-medium mt-1">{{ $action['customer_name'] }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $action['meta'] }}</div>

                                @if($action['amount'])
                                    <div class="mt-2 text-xs font-mono font-bold text-blue-600">
                                        Rp {{ number_format($action['amount'], 0, ',', '.') }}
                                    </div>
                                @endif

                                @if($action['deadline'])
                                    <div class="mt-1 text-[11px] text-amber-600 font-medium">
                                        Deadline: {{ \Carbon\Carbon::parse($action['deadline'])->format('d M Y') }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-[10px] uppercase font-bold text-slate-600">{{ $action['category'] }}</span>
                                <a href="{{ $action['action_url'] }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                                    <span>{{ $action['action_label'] }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center text-slate-500">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="text-sm font-semibold text-slate-800">Semua tugas operasional Anda dalam kendali!</div>
                    <p class="text-xs text-slate-400 mt-1">Tidak ada tindakan mendesak atau verifikasi yang tertunda untuk hak akses Anda saat ini.</p>
                </div>
            @endif
        </div>

        <!-- ========================================================= -->
        <!-- SECTION C: BUSINESS WORKFLOW TRACKER (5 Stages Pipeline)  -->
        <!-- ========================================================= -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        Pelacak Alur Kerja Bisnis (End-to-End Pipeline)
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Transisi status otomatis: Input Sekali &rarr; Pembayaran DP &rarr; Proyek & Tasks &rarr; QC Review &rarr; Pelunasan & Deliverable.
                    </p>
                </div>
                <span class="text-xs font-semibold text-slate-400">Klik tahapan untuk melihat pesanan</span>
            </div>

            <!-- Pipeline Tabs -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-5">
                @foreach($workflowTracker as $key => $stage)
                    <button type="button" 
                            @click="activeWorkflowTab = '{{ $key }}'"
                            :class="activeWorkflowTab === '{{ $key }}' ? 'bg-blue-50/80 border-blue-500 ring-2 ring-blue-500/20' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300 hover:bg-slate-100'"
                            class="p-3.5 rounded-xl border text-left transition-all cursor-pointer">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900">{{ $stage['name'] }}</span>
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center"
                                  :class="activeWorkflowTab === '{{ $key }}' ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700'">
                                {{ $stage['count'] }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1 line-clamp-1 leading-snug">{{ $stage['description'] }}</div>
                    </button>
                @endforeach
            </div>

            <!-- Tab Content (Active Stage Records) -->
            <div class="mt-5 border border-slate-100 rounded-xl overflow-hidden bg-white">
                @foreach($workflowTracker as $key => $stage)
                    <div x-show="activeWorkflowTab === '{{ $key }}'" class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar {{ $stage['name'] }} ({{ $stage['count'] }} Data)</span>
                            <span class="text-xs text-slate-400">{{ $stage['description'] }}</span>
                        </div>

                        @if($stage['orders']->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-slate-500 uppercase font-semibold bg-slate-50/60">
                                            <th class="py-2.5 px-3">Kode Order</th>
                                            <th class="py-2.5 px-3">Klien & Layanan</th>
                                            <th class="py-2.5 px-3 text-right">Nilai Deal</th>
                                            <th class="py-2.5 px-3 text-right">Sisa Tagihan</th>
                                            <th class="py-2.5 px-3 text-center">Status Pembayaran</th>
                                            <th class="py-2.5 px-3 text-center">Tindakan Workflow</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($stage['orders'] as $ord)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="py-2.5 px-3 font-mono font-bold text-blue-600">{{ $ord->order_code }}</td>
                                                <td class="py-2.5 px-3">
                                                    <div class="font-semibold text-slate-900">{{ $ord->customer->name }}</div>
                                                    <div class="text-[11px] text-slate-500">{{ $ord->service->name }}</div>
                                                </td>
                                                <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800">
                                                    Rp {{ number_format($ord->agreed_price, 0, ',', '.') }}
                                                </td>
                                                <td class="py-2.5 px-3 text-right font-mono font-bold {{ $ord->remainingBalance() > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                                                    Rp {{ number_format($ord->remainingBalance(), 0, ',', '.') }}
                                                </td>
                                                <td class="py-2.5 px-3 text-center">
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border 
                                                        {{ $ord->payment_status === 'PAID' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($ord->payment_status === 'PARTIALLY_PAID' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200') }}">
                                                        {{ $ord->payment_status }}
                                                    </span>
                                                </td>
                                                <td class="py-2.5 px-3 text-center">
                                                    <a href="{{ route('orders.index') }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold inline-flex items-center gap-1">
                                                        Buka Order &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada pesanan pada tahapan ini saat ini.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- SECTION D: RECENT ORDERS & ACTIVITY                        -->
        <!-- ========================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Recent Orders -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        D. Pesanan Proyek Terbaru (Recent Orders)
                    </h3>
                    <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Sales & Orders &rarr;</a>
                </div>

                <div class="divide-y divide-slate-100 mt-3 text-xs">
                    @forelse($recentOrders as $ro)
                        <div class="py-2.5 flex items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-blue-600">{{ $ro->order_code }}</span>
                                    <span class="font-semibold text-slate-900">{{ $ro->customer->name ?? '-' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $ro->service->name ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-bold text-slate-800">Rp {{ number_format($ro->agreed_price, 0, ',', '.') }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-100 text-slate-700">{{ str_replace('_', ' ', $ro->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">Belum ada pesanan proyek terbaru.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Audit Trail Activity Log -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Aktivitas Operasional Terbaru (Recent Activity)
                    </h3>
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
                        <a href="{{ route('activity-logs.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Audit Log &rarr;</a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 mt-3 text-xs">
                    @forelse($recentActivities as $act)
                        <div class="py-2.5 flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $act->reason ?? $act->activity }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <span class="font-medium text-slate-700">{{ $act->user->name ?? 'System' }}</span> &bull; <span class="font-mono text-blue-600">{{ $act->module }}</span> ({{ $act->record_id }})
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">Belum ada riwayat aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MODAL: ONE-TIME INTAKE KLIEN & LEAD                       -->
    <!-- ========================================================= -->
    <x-viscode-modal name="openCreateLead" title="Intake Klien & Prospek Baru (Satu Kali Input)" maxWidth="lg">
        <form action="{{ route('leads.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-800 leading-relaxed">
                💡 <strong>Prinsip Input Sekali:</strong> Data klien yang dimasukkan di sini akan otomatis disimpan dan dapat langsung dikonversi menjadi Surat Penawaran (Quotation) dan Pesanan (Order) tanpa perlu mengisi ulang.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Klien / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required placeholder="Contoh: PT Surya Kencana / Budi Santoso" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor WhatsApp / Kontak <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_phone" required placeholder="Contoh: 081234567890" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Klien (Opsional)</label>
                    <input type="email" name="customer_email" placeholder="klien@gmail.com" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Layanan yang Diminati</label>
                    <select name="service_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} (Mulai Rp {{ number_format($s->base_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Sumber Prospek (Lead Source)</label>
                    <select name="source" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="whatsapp">WhatsApp Bisnis</option>
                        <option value="instagram">Instagram</option>
                        <option value="website">Website Viscode</option>
                        <option value="referral">Rekomendasi / Teman</option>
                        <option value="direct">Pertemuan Langsung</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <x-viscode-rupiah-input
                    name="estimated_value"
                    label="Estimasi Nilai Prospek"
                    placeholder="Contoh: 450.000"
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kebutuhan & Catatan Komunikasi Awal</label>
                <textarea name="communication_notes" rows="3" placeholder="Jelaskan kebutuhan website/desain klien dan kesepakatan awal..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateLead = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Klien & Lead</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ========================================================= -->
    <!-- MODAL: CATAT TRANSAKSI KAS                                -->
    <!-- ========================================================= -->
    <x-viscode-modal name="openCreateTrx" title="Catat Transaksi Keuangan Buku Kas">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Arus Kas</label>
                    <select name="type" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="income">Pemasukan (Income)</option>
                        <option value="expense">Pengeluaran (Expense)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kategori</label>
                    <input type="text" name="category" placeholder="DP / Pelunasan / Domain / Internet" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautkan ke Pesanan (Khusus Pemasukan)</label>
                <select name="order_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Tidak terkait pesanan / Pengeluaran Umum --</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}">{{ $order->order_code }} - {{ $order->customer->name }} (Deal: Rp {{ number_format($order->agreed_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <x-viscode-rupiah-input
                name="amount"
                label="Nominal Transaksi"
                placeholder="Contoh: 500.000"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="payment_method" value="Transfer / QRIS" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan Detail</label>
                <input type="text" name="description" placeholder="Misal: DP 50% Pembuatan Landing Page" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Upload Bukti Transfer / Struk (Opsional)</label>
                <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateTrx = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Transaksi</button>
            </div>
        </form>
    </x-viscode-modal>

</x-app-layout>
