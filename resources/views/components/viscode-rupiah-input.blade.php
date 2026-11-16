@props([
    'name',
    'label' => null,
    'required' => false,
    'placeholder' => 'Contoh: 1.000.000',
    'initialValue' => '',
    'hint' => null,
])

<div x-data="{
    raw: window.sanitizeRupiahRaw(@js($initialValue)),
    display: window.formatRupiahDots(@js($initialValue)),
    onInput(e) {
        this.raw = window.formatRupiahInputWithCursor(e.target);
        this.display = e.target.value;
    },
    setAmount(val) {
        this.raw = window.sanitizeRupiahRaw(val);
        this.display = window.formatRupiahDots(this.raw);
        if (this.$refs.visInput) {
            this.$refs.visInput.value = this.display;
        }
    }
}" {{ $attributes->only('class') }}>
    @if($label)
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center">
        <span class="absolute left-0 inset-y-0 flex items-center pl-3.5 pr-2.5 text-xs font-bold text-slate-500 select-none pointer-events-none border-r border-slate-200 my-2">
            Rp
        </span>
        <input type="text"
               x-ref="visInput"
               inputmode="numeric"
               autocomplete="off"
               :value="display"
               @input="onInput($event)"
               placeholder="{{ $placeholder }}"
               @if($required) required @endif
               class="w-full rounded-xl bg-white border-slate-300 text-slate-900 font-mono font-semibold text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 shadow-2xs"
               style="padding-left: 3.1rem; min-height: 44px;">
        <input type="hidden" name="{{ $name }}" :value="raw">
    </div>

    @if($hint)
        <span class="text-[11px] text-slate-500 mt-1.5 block leading-snug">{{ $hint }}</span>
    @endif
</div>
