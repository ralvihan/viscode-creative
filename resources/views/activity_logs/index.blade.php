<x-app-layout x-data="{
    openDetail: false,
    selectedLog: null,
    setDetail(log) {
        this.selectedLog = log;
        this.openDetail = true;
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-blue-600 rounded-full inline-block"></span>
                    Audit Trail & Log Aktivitas Sistem
                </h2>
                <p class="text-sm text-slate-500 mt-1">Rekam jejak seluruh perubahan data, transisi status trigger-based, dan status override Super Admin</p>
            </div>
            <div class="px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Audit Compliance & Anti-Tamper
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
                        <div class="flex items-center gap-2">
                            <label class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Modul:</label>
                            <select name="module" class="rounded-xl bg-white border-slate-300 text-slate-900 text-xs font-medium focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Semua Modul --</option>
                                @foreach($modules as $mod)
                                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative w-full md:w-64">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode ID / Alasan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-xs pl-8 pr-4 focus:border-blue-500 focus:ring-blue-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors cursor-pointer">
                            Terapkan Filter
                        </button>

                        @if(request()->hasAny(['module', 'search']))
                            <a href="{{ route('activity-logs.index') }}" class="text-xs text-slate-500 hover:text-slate-800 transition-colors">Reset</a>
                        @endif
                    </div>

                    <span class="text-xs font-medium text-slate-500 self-end md:self-center">Total {{ $logs->total() }} Log Aktivitas Tercatat</span>
                </form>
            </div>

            <!-- Activity Logs Table -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 bg-slate-50/50">
                                <th class="py-3.5 px-4">Waktu (WIB)</th>
                                <th class="py-3.5 px-4">Pelaku (User)</th>
                                <th class="py-3.5 px-4">Modul & Record ID</th>
                                <th class="py-3.5 px-4 text-center">Tipe Aktivitas</th>
                                <th class="py-3.5 px-4">Alasan / Catatan Workflow</th>
                                <th class="py-3.5 px-4 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-900">{{ $log->user->name ?? 'System' }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase font-mono">{{ $log->user->role ?? 'CRON' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">{{ $log->module }}</span>
                                    <div class="font-mono text-xs text-blue-600 mt-1 font-semibold">{{ $log->record_id }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $actStyles = [
                                            'CREATE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'UPDATE' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'STATUS_CHANGE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'OVERRIDE' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                            'DELETE' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $actStyles[$log->activity] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 max-w-sm">
                                    <div class="text-slate-700 line-clamp-2 leading-relaxed text-xs">{{ $log->reason ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5 font-mono">IP: {{ $log->ip_address ?? '127.0.0.1' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" @click="setDetail({{ json_encode($log) }})" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-blue-600 rounded-lg transition-colors cursor-pointer" title="Lihat Diff JSON & Snapshot">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">Belum ada riwayat aktivitas tercatat</div>
                                    <p class="text-xs text-slate-400 mt-1">Seluruh aktivitas operasional tim akan tercatat secara otomatis di sini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL: DETAIL AUDIT TRAIL JSON SNAPSHOT -->
    <x-viscode-modal name="openDetail" title="Snapshot Audit Trail & Data State" maxWidth="xl">
        <div class="space-y-4 text-xs" x-show="selectedLog">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-500 font-medium">Modul:</span>
                    <div class="font-bold text-slate-900 mt-0.5" x-text="selectedLog ? selectedLog.module : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Record Code/ID:</span>
                    <div class="font-bold text-blue-600 font-mono mt-0.5" x-text="selectedLog ? selectedLog.record_id : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Aktivitas:</span>
                    <div class="font-bold text-amber-600 mt-0.5" x-text="selectedLog ? selectedLog.activity : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Eksekutor:</span>
                    <div class="font-semibold text-slate-900 mt-0.5" x-text="selectedLog && selectedLog.user ? selectedLog.user.name : 'System'"></div>
                </div>
            </div>

            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider">Catatan / Alasan Perubahan:</span>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 mt-1 leading-relaxed" x-text="selectedLog ? selectedLog.reason : '-'"></div>
            </div>

            <!-- Side by side JSON Diff -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="text-rose-600 font-semibold uppercase flex items-center gap-1.5 mb-1.5 text-[11px] tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        State Sebelumnya (Old Values):
                    </span>
                    <pre class="p-3.5 rounded-xl bg-slate-900 text-[11px] text-rose-200 font-mono overflow-x-auto max-h-64 leading-tight" x-text="selectedLog && selectedLog.old_values ? JSON.stringify(selectedLog.old_values, null, 2) : 'Tidak ada data sebelumnya (CREATE)'"></pre>
                </div>

                <div>
                    <span class="text-emerald-600 font-semibold uppercase flex items-center gap-1.5 mb-1.5 text-[11px] tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        State Baru (New Values):
                    </span>
                    <pre class="p-3.5 rounded-xl bg-slate-900 text-[11px] text-emerald-200 font-mono overflow-x-auto max-h-64 leading-tight" x-text="selectedLog && selectedLog.new_values ? JSON.stringify(selectedLog.new_values, null, 2) : 'Data dihapus (DELETE)'"></pre>
                </div>
            </div>

            <div class="pt-2 text-[11px] text-slate-400 font-mono space-y-0.5">
                <div>User Agent: <span x-text="selectedLog ? selectedLog.user_agent : ''"></span></div>
                <div>IP Address: <span x-text="selectedLog ? selectedLog.ip_address : ''"></span></div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" @click="openDetail = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>
</x-app-layout>
