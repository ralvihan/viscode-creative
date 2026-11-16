@props([
    'services' => [],
    'customers' => null,
    'showCustomerSelect' => false,
    'defaultTitleExpr' => "''",
    'defaultServiceExpr' => "''",
])

<div x-data="{
    priceRaw: '',
    priceDisplay: '',
    discountRaw: '',
    discountDisplay: '',
    discountError: false,
    resetFields() {
        this.priceRaw = '';
        this.priceDisplay = '';
        this.discountRaw = '';
        this.discountDisplay = '';
        this.discountError = false;
        if (this.$refs.priceInput) this.$refs.priceInput.value = '';
        if (this.$refs.discountInput) this.$refs.discountInput.value = '';
    },
    onPriceInput(e) {
        this.priceRaw = window.formatRupiahInputWithCursor(e.target);
        this.priceDisplay = e.target.value;
        this.validateDiscount();
    },
    onDiscountInput(e) {
        this.discountRaw = window.formatRupiahInputWithCursor(e.target);
        this.discountDisplay = e.target.value;
        this.validateDiscount();
    },
    validateDiscount() {
        const p = parseInt(this.priceRaw || '0', 10);
        const d = parseInt(this.discountRaw || '0', 10);
        if (d > p && p > 0) {
            this.discountError = true;
        } else {
            this.discountError = false;
        }
    },
    get totalAmount() {
        const p = parseInt(this.priceRaw || '0', 10);
        const d = parseInt(this.discountRaw || '0', 10);
        if (d > p) return 0;
        return Math.max(0, p - d);
    },
    get formattedTotal() {
        return window.formatRupiahDots(this.totalAmount) || '0';
    }
}"
@open-quo-reset.window="resetFields()"
class="space-y-5">

    @if($showCustomerSelect && $customers)
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Pilih Klien / Pelanggan <span class="text-rose-500">*</span>
        </label>
        <select name="customer_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
            <option value="">-- Pilih Pelanggan Terdaftar --</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- 1. Judul Penawaran (Full-Width) -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Judul Penawaran <span class="text-rose-500">*</span>
        </label>
        <input type="text"
               name="title"
               required
               :value="{{ $defaultTitleExpr }}"
               placeholder="Contoh: Penawaran Pembuatan Website UMKM & Desain Konten"
               class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
    </div>

    <!-- 2. Paket / Layanan (Full-Width) -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Paket / Layanan <span class="text-rose-500">*</span>
        </label>
        <select name="service_id"
                required
                :value="{{ $defaultServiceExpr }}"
                class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
            @foreach($services as $svc)
                <option value="{{ $svc->id }}">{{ $svc->name }} — Harga Dasar Rp {{ number_format($svc->base_price, 0, ',', '.') }}</option>
            @endforeach
        </select>
    </div>

    <!-- 3. Harga Pekerjaan & Diskon (2 Columns on Desktop, 1 Column on Mobile) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Harga Pekerjaan <span class="text-rose-500">*</span>
            </label>
            <div class="relative flex items-center">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3.5 pr-2.5 text-xs font-bold text-slate-500 select-none pointer-events-none border-r border-slate-200 my-2">
                    Rp
                </span>
                <input type="text"
                       x-ref="priceInput"
                       inputmode="numeric"
                       autocomplete="off"
                       required
                       @input="onPriceInput($event)"
                       placeholder="Contoh: 1.000.000"
                       class="w-full rounded-xl bg-white border-slate-300 text-slate-900 font-mono font-semibold text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs"
                       style="padding-left: 3.1rem; min-height: 44px;">
                <input type="hidden" name="price" :value="priceRaw">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Diskon (Opsional)
            </label>
            <div class="relative flex items-center">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3.5 pr-2.5 text-xs font-bold text-slate-500 select-none pointer-events-none border-r border-slate-200 my-2">
                    Rp
                </span>
                <input type="text"
                       x-ref="discountInput"
                       inputmode="numeric"
                       autocomplete="off"
                       @input="onDiscountInput($event)"
                       placeholder="Contoh: 100.000"
                       :class="discountError ? 'border-rose-400 focus:border-rose-600 focus:ring-rose-500/20' : 'border-slate-300 focus:border-blue-600 focus:ring-blue-500/20'"
                       class="w-full rounded-xl bg-white text-slate-900 font-mono font-semibold text-sm focus:ring-2 shadow-2xs"
                       style="padding-left: 3.1rem; min-height: 44px;">
                <input type="hidden" name="discount_amount" :value="discountRaw || 0">
            </div>
            <p x-show="discountError" x-cloak class="text-xs text-rose-600 font-semibold mt-1.5">
                ⚠ Nominal diskon tidak boleh melebihi Harga Pekerjaan.
            </p>
        </div>
    </div>

    <!-- 4. Total Penawaran (Prominent Readonly Automatic Calculation) -->
    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-emerald-900">
                Total Penawaran Akhir (Otomatis)
            </label>
            <span class="text-xs text-emerald-700 mt-0.5 block">
                Rumus: Harga Pekerjaan dikurangi Diskon
            </span>
        </div>
        <div class="sm:w-64">
            <div class="relative flex items-center">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3.5 pr-2.5 text-xs font-bold text-emerald-700 select-none pointer-events-none border-r border-emerald-200 my-2">
                    Rp
                </span>
                <input type="text"
                       readonly
                       tabindex="-1"
                       :value="formattedTotal"
                       class="w-full rounded-xl bg-white border-emerald-300 text-emerald-700 font-mono font-extrabold text-base text-right pr-4 cursor-default select-all shadow-2xs"
                       style="padding-left: 3.1rem; min-height: 46px;">
                <input type="hidden" name="total_amount" :value="totalAmount">
            </div>
        </div>
    </div>

    <!-- 5. Estimasi Deadline & Masa Berlaku Penawaran (2 Columns on Desktop) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Estimasi Deadline Pengerjaan
            </label>
            <input type="date"
                   name="estimated_deadline"
                   value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                   class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Masa Berlaku Penawaran <span class="text-rose-500">*</span>
            </label>
            <input type="date"
                   name="valid_until"
                   required
                   value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                   class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
        </div>
    </div>

    <!-- 6. Deskripsi Pekerjaan (Scope of Work) -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Deskripsi Pekerjaan (Scope of Work) <span class="text-rose-500">*</span>
        </label>
        <textarea name="scope_description"
                  rows="3"
                  required
                  placeholder="Tuliskan rincian fitur, tahapan deliverables, dan ketentuan revisi..."
                  class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs"></textarea>
    </div>

    <!-- 7. Catatan Opsional -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Catatan Tambahan Penawaran (Opsional)
        </label>
        <input type="text"
               name="notes"
               placeholder="Contoh: Pembayaran DP 50% di awal sebelum pengerjaan dimulai..."
               class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
    </div>
</div>
