<x-app-layout x-data="{ 
    stageFilter: 'all',
    openCreate: false, 
    openReview: false,
    openComm: false, 
    openLost: false, 
    openConvertQuo: false, 
    openDetail: false,
    openOverride: false,
    selectedLead: {}, 
    quoPrice: 0,
    quoDiscount: 0,
    quoSubmitAction: 'send',
    overrideRecordId: null,
    openQuotationModal(lead) {
        this.selectedLead = lead;
        this.quoSubmitAction = 'send';
        this.openConvertQuo = true;
        this.$nextTick(() => window.dispatchEvent(new CustomEvent('open-quo-reset')));
    },
    get quoTotal() {
        return Math.max(0, (Number(this.quoPrice) || 0) - (Number(this.quoDiscount) || 0));
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Sales Pipeline — Manajemen Prospek & Leads
                </h2>
                <p class="text-xs text-slate-500 mt-1">Alur terstruktur: Lead Masuk (NEW) &rarr; Review Lead (REVIEWED) &rarr; Catat Follow-up (FOLLOW_UP) &rarr; Buat &amp; Kirim Penawaran (QUOTATION_SENT)</p>
            </div>
            <button type="button" @click="openCreate = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Lead Manual</span>
            </button>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Segmented Workspace Navigation -->
        <x-viscode-workspace-tabs workspace="business" />

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

        <!-- Leads Pipeline Table (Pure White Container) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @php
                $col = $leads->getCollection();
                $countNew = $col->where('status', 'NEW')->count();
                $countReviewed = $col->where('status', 'REVIEWED')->count();
                $countFollowUp = $col->whereIn('status', ['FOLLOW_UP', 'CONTACTED'])->count();
                $countQuoSent = $col->whereIn('status', ['QUOTATION_SENT', 'OFFERED', 'WON'])->count();
            @endphp
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                    <button type="button" @click="stageFilter = 'all'"
                            :class="stageFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Semua Prospek</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="stageFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600'">{{ $leads->total() }}</span>
                    </button>

                    <button type="button" @click="stageFilter = 'new'"
                            :class="stageFilter === 'new' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>1. Baru Masuk (NEW)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="stageFilter === 'new' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'">{{ $countNew }}</span>
                    </button>

                    <button type="button" @click="stageFilter = 'reviewed'"
                            :class="stageFilter === 'reviewed' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>2. Direview (REVIEWED)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="stageFilter === 'reviewed' ? 'bg-indigo-500 text-white' : 'bg-indigo-50 text-indigo-700'">{{ $countReviewed }}</span>
                    </button>

                    <button type="button" @click="stageFilter = 'follow_up'"
                            :class="stageFilter === 'follow_up' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>3. Siap Penawaran (FOLLOW_UP)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="stageFilter === 'follow_up' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700'">{{ $countFollowUp }}</span>
                    </button>

                    <button type="button" @click="stageFilter = 'quotation_sent'"
                            :class="stageFilter === 'quotation_sent' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>4. Penawaran Terkirim</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="stageFilter === 'quotation_sent' ? 'bg-purple-500 text-white' : 'bg-purple-50 text-purple-700'">{{ $countQuoSent }}</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Kode Lead & Tanggal</th>
                            <th class="py-3.5 px-4">Nama & Kontak Klien</th>
                            <th class="py-3.5 px-4">Minat Layanan & Kebutuhan</th>
                            <th class="py-3.5 px-4">Sumber</th>
                            <th class="py-3.5 px-4">Status Pipeline</th>
                            <th class="py-3.5 px-4">PIC Sales</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Utama (Next Action)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($leads as $lead)
                        @php
                            $draftQuotation = $lead->quotations->where('status', 'DRAFT')->first();
                            $activeQuotation = $lead->quotations->whereIn('status', ['SENT', 'ACCEPTED', 'CONVERTED'])->first();
                        @endphp
                        <tr x-show="stageFilter === 'all'
                                || (stageFilter === 'new' && '{{ $lead->status }}' === 'NEW')
                                || (stageFilter === 'reviewed' && '{{ $lead->status }}' === 'REVIEWED')
                                || (stageFilter === 'follow_up' && ['FOLLOW_UP', 'CONTACTED'].includes('{{ $lead->status }}'))
                                || (stageFilter === 'quotation_sent' && ['QUOTATION_SENT', 'OFFERED', 'WON'].includes('{{ $lead->status }}'))"
                            class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                {{ $lead->lead_code }}
                                <div class="text-[10px] text-slate-400 font-normal">{{ $lead->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $lead->customer_name }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $lead->customer_phone }}</div>
                                @if($lead->customer_email)
                                    <div class="text-[10px] text-slate-400">{{ $lead->customer_email }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-bold text-slate-800">{{ $lead->service->name ?? 'Konsultasi Umum' }}</div>
                                @if($lead->estimated_value > 0)
                                    <div class="text-[11px] text-emerald-600 font-semibold font-mono mt-0.5">Est: Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}</div>
                                @endif
                                @if($lead->communication_notes)
                                    <div class="text-[11px] text-slate-500 truncate mt-0.5" title="{{ $lead->communication_notes }}">
                                        {{ Str::limit($lead->communication_notes, 55) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $lead->source }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $statusClasses = match($lead->status) {
                                    'NEW' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'REVIEWED' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'FOLLOW_UP', 'CONTACTED' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'QUOTATION_SENT', 'OFFERED' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'WON' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'LOST' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', $lead->status) }}
                                </span>
                                @if($activeQuotation)
                                    <div class="text-[10px] font-mono text-purple-700 font-semibold mt-1">
                                        📄 {{ $activeQuotation->quotation_code }}
                                    </div>
                                @elseif($draftQuotation)
                                    <div class="text-[10px] font-mono text-slate-500 font-semibold mt-1">
                                        📝 Draft: {{ $draftQuotation->quotation_code }}
                                    </div>
                                @endif
                                @if($lead->next_follow_up_date && in_array($lead->status, ['REVIEWED', 'FOLLOW_UP', 'CONTACTED']))
                                    <div class="text-[10px] text-amber-600 mt-0.5">
                                        📅 Next: {{ $lead->next_follow_up_date->format('d M Y') }}
                                    </div>
                                @endif
                                @if($lead->status === 'LOST' && $lead->lost_reason)
                                    <div class="text-[10px] text-rose-600 mt-1 italic truncate max-w-[150px]" title="{{ $lead->lost_reason }}">{{ $lead->lost_reason }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 font-medium">
                                {{ $lead->assignedUser->name ?? ($lead->reviewer->name ?? '-') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    {{-- CONTEXTUAL ACTION SYSTEM: ONE PRIMARY NEXT ACTION PER LEAD STATUS --}}
                                    @if($lead->status === 'NEW')
                                        <button type="button" @click="selectedLead = {{ Js::from($lead) }}; openReview = true"
                                                class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                            🔍 Review Lead
                                        </button>

                                    @elseif($lead->status === 'REVIEWED')
                                        <button type="button" @click="selectedLead = {{ Js::from($lead) }}; openComm = true"
                                                class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                            💬 Catat Follow-up
                                        </button>

                                    @elseif(in_array($lead->status, ['FOLLOW_UP', 'CONTACTED']))
                                        <button type="button" @click="openQuotationModal({{ Js::from($lead) }})"
                                                class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                            {{ $draftQuotation ? '📤 Lanjutkan Draft Penawaran' : '📄 Buat Penawaran' }}
                                        </button>

                                    @elseif(in_array($lead->status, ['QUOTATION_SENT', 'OFFERED']))
                                        <a href="{{ route('orders.index', ['filter' => 'waiting_response']) }}"
                                           class="px-3.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold rounded-xl transition">
                                            Lihat Penawaran &rarr;
                                        </a>
                                    @endif

                                    <!-- Ikon Lihat Detail & Riwayat Lead (Selalu Tersedia) -->
                                    <button type="button" @click="selectedLead = {{ Js::from($lead) }}; openDetail = true"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg border border-transparent hover:border-slate-200 transition cursor-pointer" title="Lihat Detail & Riwayat Lead">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">Belum ada data prospek / leads.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $leads->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- ================================================================= -->
    <!-- MODAL 1: REVIEW LEAD (NEW -> REVIEWED)                            -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openReview" title="Review Prospek Masuk (Lead Intake Review)" maxWidth="lg">
        <form :action="`/leads/${selectedLead.id}/review`" method="POST"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Selesaikan Review Prospek ' + (selectedLead.customer_name || '') + '?',
                  html: 'Data kebutuhan prospek <b>' + (selectedLead.lead_code || '') + '</b> telah ditinjau.<br><br>Status akan berubah dari <b>NEW</b> menjadi <span class=\'text-indigo-600 font-bold\'>REVIEWED</span> dan siap untuk tahap Follow-up.',
                  icon: 'question',
                  confirmButtonText: '✓ Selesaikan Review',
                  confirmButtonColor: '#4F46E5'
              })"
              class="space-y-4 text-xs">
            @csrf

            <!-- Ringkasan Data Masuk dari Formulir Website -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                    <div>
                        <span class="font-mono font-bold text-blue-600 text-sm" x-text="selectedLead.lead_code"></span>
                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-800" x-text="selectedLead.source"></span>
                    </div>
                    <div class="text-slate-500 text-[11px]">
                        Tanggal Masuk: <strong class="text-slate-800" x-text="selectedLead.created_at ? new Date(selectedLead.created_at).toLocaleString('id-ID') : '-'"></strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Calon Pelanggan</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="selectedLead.customer_name"></span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Kontak WhatsApp &amp; Email</span>
                        <div class="font-mono font-semibold text-slate-800" x-text="selectedLead.customer_phone"></div>
                        <div class="text-slate-500 text-[11px]" x-text="selectedLead.customer_email || 'Email tidak dicantumkan'"></div>
                    </div>
                </div>

                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold mb-1">Deskripsi Kebutuhan &amp; Referensi Awal (Dari Form Website)</span>
                    <pre class="p-3 rounded-lg bg-white border border-slate-200 text-slate-800 font-sans whitespace-pre-wrap leading-relaxed text-xs" x-text="selectedLead.communication_notes || 'Tidak ada catatan tambahan.'"></pre>
                </div>
            </div>

            <!-- Verifikasi Layanan, Estimasi & Penugasan Sales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Konfirmasi Layanan Diminati</label>
                    <select name="service_id" :value="selectedLead.service_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="">-- Konsultasi Umum / Custom --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name }} (Rp {{ number_format($svc->base_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <x-viscode-rupiah-input name="estimated_value" label="Estimasi Nilai Proyek" placeholder="Contoh: 1.000.000" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">PIC Sales Penanggung Jawab</label>
                    <select name="assigned_to" :value="selectedLead.assigned_to || '{{ Auth::id() }}'" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        @foreach($team as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Hasil Review (Opsional)</label>
                    <input type="text" name="review_notes" placeholder="Kebutuhan jelas, siap dihubungi via WA..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" @click="openReview = false; openLost = true" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold rounded-xl cursor-pointer">
                    ✕ Tidak Valid / Tandai Lost
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="openReview = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                        ✓ Selesaikan Review (REVIEWED)
                    </button>
                </div>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 2: CATAT FOLLOW-UP (REVIEWED -> FOLLOW_UP)                  -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openComm" title="Catat Follow-up & Komunikasi Prospek" maxWidth="md">
        <form :action="`/leads/${selectedLead.id}/communication`" method="POST"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Catatan Follow-up?',
                  html: 'Simpan hasil komunikasi dengan <b>' + (selectedLead.customer_name || '') + '</b>?<br><br>Status lead akan diperbarui menjadi <span class=\'text-amber-600 font-bold\'>FOLLOW_UP</span> dan siap dibuatkan Penawaran.',
                  icon: 'info',
                  confirmButtonText: 'Simpan Follow-up',
                  confirmButtonColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                <div>
                    <span class="text-slate-500">Prospek:</span> <strong class="text-slate-900" x-text="selectedLead.customer_name"></strong>
                    <div class="font-mono text-slate-500 text-[11px]" x-text="selectedLead.customer_phone"></div>
                </div>
                <a :href="'https://wa.me/' + (selectedLead.customer_phone ? selectedLead.customer_phone.replace(/[^0-9]/g, '') : '')" target="_blank"
                   class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg font-bold text-[11px]">
                    💬 Chat WA ↗
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Komunikasi <span class="text-rose-500">*</span></label>
                    <input type="date" name="contact_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Hasil Komunikasi <span class="text-rose-500">*</span></label>
                    <select name="follow_up_result" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-xs focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="Tertarik & Siap Diberikan Penawaran">Tertarik &amp; Siap Diberikan Penawaran</option>
                        <option value="Diskusi Spesifikasi / Penyesuaian Budget">Diskusi Spesifikasi / Penyesuaian Budget</option>
                        <option value="Menunggu Konfirmasi Lanjutan Klien">Menunggu Konfirmasi Lanjutan Klien</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Detail Komunikasi / Kebutuhan <span class="text-rose-500">*</span></label>
                <textarea name="notes" rows="3" required placeholder="Contoh: Klien menyetujui konsep paket UMKM Digital dan meminta surat penawaran resmi..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jadwal Follow-up Berikutnya (Opsional)</label>
                <input type="date" name="next_follow_up_date" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openComm = false; openLost = true" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg cursor-pointer">
                    ✕ Tandai Lost
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="openComm = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">Simpan Follow-up</button>
                </div>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 3: BUAT PENAWARAN / QUOTATION (FOLLOW_UP -> QUOTATION_SENT) -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openConvertQuo" title="Buat Surat Penawaran (Quotation) dari Prospek" maxWidth="2xl">
        <form :action="`/leads/${selectedLead.id}/convert-quotation`" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: this.quoSubmitAction === 'send' ? 'Simpan & Kirim Penawaran ke ' + (selectedLead.customer_name || '') + '?' : 'Simpan Draft Penawaran?',
                  html: this.quoSubmitAction === 'send'
                      ? 'Penawaran akan diterbitkan dan langsung berstatus <b>SENT</b>, serta status prospek berubah menjadi <span class=\'text-purple-600 font-bold\'>QUOTATION_SENT</span>.'
                      : 'Penawaran akan disimpan sebagai <b>DRAFT</b> dan terhubung ke prospek ini.',
                  icon: 'info',
                  confirmButtonText: this.quoSubmitAction === 'send' ? '📤 Simpan & Kirim Penawaran' : '📝 Simpan Draft',
                  confirmButtonColor: '#2563EB'
              })"
              class="space-y-5">
            @csrf
            <input type="hidden" name="submit_action" :value="quoSubmitAction">

            <!-- Data Klien Otomatis Terhubung -->
            <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 text-blue-900 text-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <div>Klien: <strong class="text-sm text-slate-900" x-text="selectedLead.customer_name"></strong> (<span class="font-mono font-semibold" x-text="selectedLead.customer_phone"></span>)</div>
                    <div class="text-[11px] text-blue-700 mt-0.5">Lead: <span class="font-mono font-bold" x-text="selectedLead.lead_code"></span> &mdash; Data pelanggan &amp; paket layanan otomatis terhubung.</div>
                </div>
                <template x-if="Number(selectedLead.estimated_value) > 0">
                    <div class="text-left sm:text-right shrink-0">
                        <span class="text-[10px] uppercase font-bold text-blue-700 block">Referensi Budget Klien</span>
                        <span class="font-mono font-bold text-slate-900 text-xs" x-text="`Rp ${Number(selectedLead.estimated_value).toLocaleString('id-ID')}`"></span>
                    </div>
                </template>
            </div>

            <x-viscode-quotation-form-fields
                :services="$services"
                defaultTitleExpr="`Penawaran Layanan untuk ${selectedLead.customer_name || ''}`"
                defaultServiceExpr="selectedLead.service_id" />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 flex-wrap">
                <button type="button" @click="openConvertQuo = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" @click="quoSubmitAction = 'draft'" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                    📝 Simpan Draft
                </button>
                <button type="submit" @click="quoSubmitAction = 'send'" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                    📤 Simpan &amp; Kirim Penawaran
                </button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 4: DETAIL & RIWAYAT PROSPEK                                 -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openDetail" title="Detail Informasi & Riwayat Prospek (Lead)" maxWidth="lg">
        <div class="space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-blue-600 text-sm" x-text="selectedLead.lead_code"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200" x-text="selectedLead.status"></span>
                    </div>
                    <h4 class="text-slate-900 font-bold text-base mt-1" x-text="selectedLead.customer_name"></h4>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 text-[11px]">Estimasi Nilai:</span>
                    <div class="font-mono font-bold text-emerald-600 text-base" x-text="`Rp ${Number(selectedLead.estimated_value || 0).toLocaleString('id-ID')}`"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Kontak &amp; Sumber</span>
                    <div class="font-mono font-semibold text-slate-800 mt-0.5" x-text="selectedLead.customer_phone"></div>
                    <div class="text-slate-500" x-text="selectedLead.customer_email || '-'"></div>
                    <div class="mt-1"><span class="px-2 py-0.5 rounded bg-white border border-slate-200 text-[10px] font-bold uppercase" x-text="selectedLead.source"></span></div>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Status Review &amp; Follow-up</span>
                    <div>Layanan: <strong x-text="selectedLead.service?.name || 'Konsultasi Umum'"></strong></div>
                    <div x-show="selectedLead.reviewed_at">Direview: <strong x-text="selectedLead.reviewed_at ? selectedLead.reviewed_at.substring(0, 10) : '-'"></strong></div>
                    <div x-show="selectedLead.follow_up_result">Hasil Follow-up: <strong x-text="selectedLead.follow_up_result"></strong></div>
                </div>
            </div>

            <div x-show="selectedLead.review_notes" class="p-3 rounded-xl bg-indigo-50/70 border border-indigo-200 text-indigo-900">
                <span class="font-bold block text-[10px] uppercase">Catatan Review Awal:</span>
                <span x-text="selectedLead.review_notes"></span>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Deskripsi Kebutuhan &amp; Riwayat Follow-up:</label>
                <pre class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-sans whitespace-pre-wrap max-h-48 overflow-y-auto leading-relaxed" x-text="selectedLead.communication_notes || 'Belum ada catatan.'"></pre>
            </div>

            <!-- Secondary Contextual Actions inside Detail Modal -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100 flex-wrap gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <template x-if="!['WON', 'LOST'].includes(selectedLead.status)">
                        <button type="button" @click="openDetail = false; openComm = true" class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold rounded-lg cursor-pointer">
                            💬 Tambah Log Follow-up
                        </button>
                    </template>
                    <template x-if="!['WON', 'LOST'].includes(selectedLead.status)">
                        <button type="button" @click="openDetail = false; openLost = true" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold rounded-lg cursor-pointer">
                            ✕ Tandai Lost
                        </button>
                    </template>
                    @if(Auth::user()->isSuperAdmin())
                    <button type="button" @click="openDetail = false; overrideRecordId = selectedLead.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer">
                        ⚡ Admin Override
                    </button>
                    @endif
                </div>
                <button type="button" @click="openDetail = false" class="px-4 py-2 bg-slate-900 text-white font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 5: ADD LEAD MANUAL                                          -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreate" title="Catat Prospek / Lead Baru" maxWidth="lg">
        <form action="{{ route('leads.store') }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Prospek / Lead Baru?',
                  text: 'Lead akan masuk ke pipeline berstatus NEW untuk direview oleh tim Sales.',
                  icon: 'question',
                  confirmText: 'Simpan Lead',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Calon Pelanggan <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_phone" placeholder="08xxxxxxxxxx" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email (Opsional)</label>
                    <input type="email" name="customer_email" placeholder="budi@example.com" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Sumber Prospek <span class="text-rose-500">*</span></label>
                    <select name="source" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="website">Website Viscode</option>
                        <option value="whatsapp">Chat WhatsApp Langsung</option>
                        <option value="instagram">Instagram / Media Sosial</option>
                        <option value="referral">Rekomendasi / Referral</option>
                        <option value="direct">Keluarga / Teman Kampus</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Minat Layanan</label>
                    <select name="service_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                        <option value="">-- Belum spesifik / Konsultasi --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->name }} (Rp {{ number_format($svc->base_price, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>
                <x-viscode-rupiah-input name="estimated_value" label="Estimasi Nilai Proyek" placeholder="Contoh: 1.000.000" />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tugaskan ke PIC Sales</label>
                <select name="assigned_to" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="">-- Pilih PIC Sales --</option>
                    @foreach($team as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role_label }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Kebutuhan Awal</label>
                <textarea name="communication_notes" rows="2" placeholder="Klien membutuhkan pembuatan website company profile untuk UMKM..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Lead</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 6: MARK LOST                                                -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openLost" title="Tandai Prospek Tidak Dilanjutkan (LOST)" maxWidth="md">
        <form :action="`/leads/${selectedLead.id}/lost`" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Tandai Prospek ' + (selectedLead.customer_name || '') + ' sebagai LOST?',
                  text: 'Alasan penolakan akan dicatat permanen dalam riwayat evaluasi lead.',
                  icon: 'warning',
                  confirmText: 'Ya, Tandai Lost',
                  confirmColor: '#DC2626'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs leading-relaxed">
                Status LOST memerlukan pencatatan alasan yang jelas untuk evaluasi strategi pemasaran dan performa lead.
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Tidak Dilanjutkan (Wajib) <span class="text-rose-500">*</span></label>
                <textarea name="lost_reason" rows="3" required placeholder="Contoh: Budget klien belum mencukupi / memilih vendor lain / kontak tidak responsif..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openLost = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Tandai LOST</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE -->
    <x-viscode-override-modal module="leads" :statuses="['NEW', 'REVIEWED', 'FOLLOW_UP', 'QUOTATION_SENT', 'WON', 'LOST']" />
</x-app-layout>
