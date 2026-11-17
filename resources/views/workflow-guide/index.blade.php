<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Panduan &amp; Tracking Alur Sistem (Menu, Action &amp; Role)
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Peta navigasi operasional langkah demi langkah: pahami menu mana yang harus dibuka, tombol apa yang harus diklik, dan ke role mana pekerjaan berpindah.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold rounded-xl transition shadow-2xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Quick Role Guide Banner for Currently Logged-In User -->
        <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-2xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Panduan Khusus Akun Anda:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ Auth::user()->name }} &bull; {{ Auth::user()->role_label }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Setiap tahapan di bawah yang memiliki label <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white">✨ Tugas Role Anda</span> adalah tahapan yang dapat Anda eksekusi. Klik tombol <strong>"Buka ..."</strong> berwarna biru pada kolom <em>1. Pergi ke Menu Mana?</em> untuk langsung menuju halaman tersebut.
                    </p>
                </div>
            </div>
        </div>

        <!-- Full Interactive System Flow Tracker -->
        <x-viscode-system-flow-tracker :compactHeader="false" />
    </div>
</x-app-layout>
