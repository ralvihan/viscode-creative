@props(['name' => 'openOverride', 'module' => '', 'statuses' => []])

@if(Auth::check() && Auth::user()->isSuperAdmin())
<x-viscode-modal :name="$name" title="Super Admin Status Override" maxWidth="md">
    <form action="{{ route('status-override') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="module" value="{{ $module }}">
        <input type="hidden" name="record_id" x-bind:value="overrideRecordId">

        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs leading-relaxed flex items-start gap-2.5">
            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <strong class="font-bold">Perhatian Super Admin:</strong> Override status manual hanya diperuntukkan untuk koreksi administratif. Seluruh perubahan status beserta alasan akan dicatat permanen dalam Audit Log.
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status Tujuan Baru <span class="text-rose-500">*</span></label>
            <select name="new_status" required class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm">
                <option value="">-- Pilih Status Tujuan --</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}">{{ strtoupper(str_replace('_', ' ', $st)) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Perubahan Status (Wajib) <span class="text-rose-500">*</span></label>
            <textarea name="reason" rows="3" required placeholder="Tuliskan justifikasi administratif atau bukti validasi di balik override ini..." class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20 shadow-sm placeholder:text-slate-400"></textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button type="button" @click="{{ $name }} = false" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Batal</button>
            <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm transition cursor-pointer">Terapkan Override</button>
        </div>
    </form>
</x-viscode-modal>
@endif
