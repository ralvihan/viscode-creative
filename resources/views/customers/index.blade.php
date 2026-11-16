<x-app-layout x-data="{ 
    openCreate: false, 
    openEdit: false, 
    openQuotation: false,
    openDirectOrder: false,
    selectedCustomer: {} 
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Database Pelanggan & Klien
                </h2>
                <p class="text-xs text-slate-500 mt-1">Direktori terpusat: kelola kontak klien sekali, gunakan kembali untuk penawaran dan pesanan proyek</p>
            </div>
            <button type="button" @click="openCreate = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pelanggan</span>
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

        <!-- Customer Data Table Container (Pure White) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Direktori Klien Terdaftar</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total {{ $customers->total() }} kontak klien terdata di sistem</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4 rounded-l-xl">Nama Pelanggan</th>
                            <th class="py-3.5 px-4">Kontak & WhatsApp</th>
                            <th class="py-3.5 px-4">Segmentasi</th>
                            <th class="py-3.5 px-4 text-center">Prospek</th>
                            <th class="py-3.5 px-4 text-center">Quotation</th>
                            <th class="py-3.5 px-4 text-center">Pesanan Selesai</th>
                            <th class="py-3.5 px-4 text-center rounded-r-xl">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $customer->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $customer->email ?? 'Tidak ada email' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-slate-800">
                                @if(!empty($customer->phone))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}" target="_blank" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-800 text-[11px] inline-flex items-center gap-1 transition">
                                        <span>💬</span>
                                        <span>{{ $customer->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ str_replace('_', ' ', $customer->type) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 font-mono">{{ $customer->leads_count }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800 font-mono">{{ $customer->quotations_count }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600 font-mono">{{ $customer->orders_count }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    <button type="button" @click="selectedCustomer = {{ Js::from($customer) }}; openEdit = true" class="px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer" title="Edit Data Klien">
                                        Edit
                                    </button>

                                    @if(auth()->user()->canAccessModule('quotations'))
                                    <button type="button" @click="selectedCustomer = {{ Js::from($customer) }}; openQuotation = true" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer" title="Buat Penawaran Quotation Langsung">
                                        📄 Penawaran
                                    </button>
                                    @endif

                                    @if(auth()->user()->canAccessModule('orders'))
                                    <button type="button" @click="selectedCustomer = {{ Js::from($customer) }}; openDirectOrder = true" class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer" title="Buat Pesanan Proyek Langsung">
                                        🛒 Pesanan
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                Belum ada data pelanggan terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL: TAMBAH PELANGGAN BARU -->
    <x-viscode-modal name="openCreate" title="Tambah Data Pelanggan Baru" maxWidth="md">
        <form action="{{ route('customers.store') }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Simpan Data Pelanggan Baru?',
                  text: 'Data kontak akan tersimpan di direktori dan dapat digunakan untuk penawaran atau pesanan.',
                  icon: 'question',
                  confirmText: 'Simpan Pelanggan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Pelanggan / Instansi <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Budi Hartono / CV Mandiri" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">No. Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" placeholder="08xxxxxxxxxx" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Klien</label>
                    <input type="email" name="email" placeholder="klien@example.com" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Segmentasi Klien <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="umkm">UMKM / Bisnis Lokal</option>
                    <option value="organisasi">Organisasi / Lembaga</option>
                    <option value="mahasiswa">Mahasiswa / Komunitas</option>
                    <option value="event_organizer">Event Organizer</option>
                    <option value="individu">Individu</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tambahan</label>
                <textarea name="notes" rows="3" placeholder="Informasi preferensi komunikasi, asal relasi, dsb..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Simpan Data Klien</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: EDIT DATA PELANGGAN -->
    <x-viscode-modal name="openEdit" title="Edit Data Pelanggan" maxWidth="md">
        <form :action="'{{ url('/customers') }}/' + selectedCustomer.id" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Perbarui Data ' + (selectedCustomer.name || 'Pelanggan') + '?',
                  text: 'Informasi kontak dan segmentasi akan diperbarui.',
                  icon: 'question',
                  confirmText: 'Simpan Perubahan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Pelanggan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" x-model="selectedCustomer.name" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor Telepon / WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" x-model="selectedCustomer.phone" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" x-model="selectedCustomer.email" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Segmentasi Klien</label>
                <select name="type" x-model="selectedCustomer.type" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    <option value="umkm">UMKM / Bisnis Lokal</option>
                    <option value="organisasi">Organisasi / Lembaga</option>
                    <option value="mahasiswa">Mahasiswa / Komunitas</option>
                    <option value="event_organizer">Event Organizer</option>
                    <option value="individu">Individu</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tambahan</label>
                <textarea name="notes" x-model="selectedCustomer.notes" rows="3" class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openEdit = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Perbarui Data Klien</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: BUAT PENAWARAN (QUOTATION) DARI PELANGGAN INI -->
    @if(auth()->user()->canAccessModule('quotations'))
    <x-viscode-modal name="openQuotation" title="Terbitkan Penawaran Resmi (Quotation)" maxWidth="2xl">
        <form action="{{ route('quotations.store') }}" method="POST" 
              x-data="{ submitAct: 'send' }"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Terbitkan Penawaran untuk ' + (selectedCustomer.name || '') + '?',
                  text: 'Dokumen penawaran akan disimpan sesuai opsi yang dipilih.',
                  icon: 'info',
                  confirmText: 'Simpan Penawaran',
                  confirmColor: '#2563EB'
              })"
              class="space-y-5">
            @csrf
            <input type="hidden" name="customer_id" :value="selectedCustomer.id">
            <input type="hidden" name="submit_action" :value="submitAct">

            <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs text-blue-900 leading-relaxed">
                <div>Klien: <strong class="text-blue-800 text-sm" x-text="selectedCustomer.name"></strong> (<span class="font-mono font-semibold" x-text="selectedCustomer.phone"></span>)</div>
                <div class="text-[11px] text-blue-700 mt-0.5">Penawaran akan ditautkan secara langsung dengan profil pelanggan ini.</div>
            </div>

            <x-viscode-quotation-form-fields
                :services="$services"
                defaultTitleExpr="`Penawaran Layanan untuk ${selectedCustomer.name || ''}`" />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 flex-wrap">
                <button type="button" @click="openQuotation = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" @click="submitAct = 'draft'" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 text-xs font-bold rounded-xl transition cursor-pointer">
                    📝 Simpan Draft
                </button>
                <button type="submit" @click="submitAct = 'send'" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">
                    📤 Simpan &amp; Kirim Penawaran
                </button>
            </div>
        </form>
    </x-viscode-modal>
    @endif

    <!-- MODAL: BUAT PESANAN LANGSUNG (ORDER) DARI PELANGGAN INI -->
    @if(auth()->user()->canAccessModule('orders'))
    <x-viscode-modal name="openDirectOrder" title="Catat Pesanan Proyek Klien Langsung" maxWidth="2xl">
        <form action="{{ route('orders.store') }}" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Buat Pesanan Baru untuk ' + (selectedCustomer.name || '') + '?',
                  text: 'Pesanan akan dicatat dengan status PENDING_PAYMENT (Menunggu DP).',
                  icon: 'question',
                  confirmText: 'Simpan Pesanan',
                  confirmColor: '#2563EB'
              })"
              class="space-y-5">
            @csrf
            <input type="hidden" name="customer_id" :value="selectedCustomer.id">
            <input type="hidden" name="customer_name" :value="selectedCustomer.name">
            <input type="hidden" name="customer_whatsapp" :value="selectedCustomer.phone">
            <input type="hidden" name="customer_type" :value="selectedCustomer.type">

            <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                <div>Klien Terpilih: <strong class="text-emerald-800 text-sm" x-text="selectedCustomer.name"></strong> (<span class="font-mono font-semibold" x-text="selectedCustomer.phone"></span>)</div>
                <div class="text-[11px] text-emerald-700 mt-0.5">Data kontak klien otomatis digunakan tanpa perlu diinput ulang.</div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pilih Layanan / Paket <span class="text-rose-500">*</span></label>
                <select name="service_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }} (Rp {{ number_format($service->base_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
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
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Deskripsi Brief Kebutuhan Proyek <span class="text-rose-500">*</span></label>
                <textarea name="brief_description" rows="3" required placeholder="Jelaskan kebutuhan spesifik dari klien..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openDirectOrder = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">Simpan Pesanan</button>
            </div>
        </form>
    </x-viscode-modal>
    @endif

</x-app-layout>