@php
    $counts = $navCounts ?? [];
    $dashCount = $counts['dashboard_attention'] ?? 0;
    $salesCount = $counts['sidebar_sales_badge'] ?? 0;
    $salesNewCount = ($counts['leads_new'] ?? 0) + ($counts['orders_waiting_payment'] ?? 0);
    $opsCount = $counts['sidebar_operations_badge'] ?? 0;
    $opsNewCount = $counts['projects_new'] ?? 0;
    $finCount = $counts['sidebar_finance_badge'] ?? 0;
@endphp

<!-- SIDEBAR NAVIGATION (Deep Navy #17243B — 4 Primary Groups + Live Progress Badges + System Flow Guide) -->
<aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-[#17243B] border-r border-[#1E2E4A] transition-all duration-300 select-none shadow-xl"
       :class="{
           'translate-x-0 w-64': !sidebarCollapsed,
           'translate-x-0 w-20': sidebarCollapsed,
           '-translate-x-full lg:translate-x-0': !sidebarOpen
       }"
       @click.outside="sidebarOpen = false">

    <!-- Header Logo (Using Official Landing Page Logo) -->
    <div class="h-16 flex items-center justify-between px-3.5 border-b border-[#1E2E4A]">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center flex-1 overflow-hidden group">
            <!-- Expanded Full Logo -->
            <div x-show="!sidebarCollapsed" class="w-full bg-white rounded-xl px-3 py-1.5 flex items-center justify-between shadow-sm group-hover:shadow transition-all">
                <img src="{{ asset('assets/logo.png') }}" alt="Viscode Creative" class="h-8 w-auto object-contain">
                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded bg-[#17243B] text-[#22D3EE] shrink-0">BMS</span>
            </div>
            <!-- Collapsed Icon Logo -->
            <div x-show="sidebarCollapsed" style="display: none;" class="w-10 h-10 bg-white rounded-xl p-1.5 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <img src="{{ asset('assets/favicon.png') }}" alt="VC" class="w-full h-full object-contain">
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Primary Business Navigation (4 Core Groups + Bottom System Flow Guide) -->
    <div class="flex-1 overflow-y-auto py-5 px-3 space-y-5">

        <!-- 1. DASHBOARD -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>1. Dashboard</span>
                @if($dashCount > 0)
                    <span class="text-[9px] font-mono text-[#22D3EE] lowercase">{{ $dashCount }} aktif</span>
                @endif
            </div>
            <a href="{{ route('dashboard') }}"
               class="relative flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-[#2563EB] text-white shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A] hover:translate-x-0.5' }}"
               title="Business Overview, Action Center & Workflow ({{ $dashCount }} item aktif)">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        @if($dashCount > 0)
                            <span x-show="sidebarCollapsed" class="absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center shadow">
                                {{ $dashCount }}
                            </span>
                        @endif
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <div>Dashboard Utama</div>
                        <div class="text-[10px] font-normal {{ request()->routeIs('dashboard') ? 'text-blue-100' : 'text-slate-400' }}">Overview &amp; Action Center</div>
                    </div>
                </div>

                @if($dashCount > 0)
                    <span x-show="!sidebarCollapsed"
                          class="shrink-0 min-w-[22px] h-5 px-1.5 rounded-full text-[10px] font-mono font-bold flex items-center justify-center transition-all {{ request()->routeIs('dashboard') ? 'bg-white text-[#2563EB] shadow-2xs' : 'bg-blue-500/20 text-[#22D3EE] border border-cyan-400/30 group-hover:bg-blue-500 group-hover:text-white' }}">
                        {{ $dashCount }}
                    </span>
                @endif
            </a>
        </div>

        <!-- 2. BUSINESS MANAGEMENT (Sales & Orders, Pipeline, Customers, Services) -->
        @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('quotations') || Auth::user()->canAccessModule('leads') || Auth::user()->canAccessModule('customers'))
        @php
            $isBusinessMgmt = request()->routeIs('orders.*', 'quotations.*', 'leads.*', 'customers.*');
            $businessRoute = Auth::user()->canAccessModule('orders') ? route('orders.index') : route('leads.index');
        @endphp
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>2. Business Management</span>
                @if(($counts['leads_new'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-400/30 text-[9px] font-mono animate-pulse">
                        {{ $counts['leads_new'] }} NEW
                    </span>
                @endif
            </div>
            <a href="{{ $businessRoute }}"
               class="relative flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isBusinessMgmt ? 'bg-[#2563EB] text-white shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A] hover:translate-x-0.5' }}"
               title="Sales & Orders ({{ $counts['orders_active'] ?? 0 }} Pesanan Berjalan, {{ $counts['quotations_active'] ?? 0 }} Penawaran, {{ $counts['leads_total_active'] ?? 0 }} Prospek)">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <svg class="w-4 h-4 {{ $isBusinessMgmt ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @if($salesCount > 0)
                            <span x-show="sidebarCollapsed" class="absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-amber-500 text-white text-[9px] font-bold flex items-center justify-center shadow">
                                {{ $salesCount }}
                            </span>
                        @endif
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <div>Sales &amp; Orders</div>
                        <div class="text-[10px] font-normal {{ $isBusinessMgmt ? 'text-blue-100' : 'text-slate-400' }}">
                            {{ $counts['orders_active'] ?? 0 }} Order &bull; {{ $counts['leads_total_active'] ?? 0 }} Prospek
                        </div>
                    </div>
                </div>

                @if($salesCount > 0)
                    <div x-show="!sidebarCollapsed" class="shrink-0 flex items-center gap-1">
                        <span class="min-w-[22px] h-5 px-1.5 rounded-full text-[10px] font-mono font-bold flex items-center justify-center transition-all {{ $isBusinessMgmt ? 'bg-white text-[#2563EB] shadow-2xs' : ($salesNewCount > 0 ? 'bg-amber-500 text-white shadow-xs' : 'bg-blue-500/20 text-[#22D3EE] border border-cyan-400/30') }}"
                              title="{{ $counts['orders_active'] ?? 0 }} Pesanan berjalan/belum dikerjakan, {{ $counts['quotations_active'] ?? 0 }} Penawaran aktif, {{ $counts['leads_total_active'] ?? 0 }} Prospek">
                            {{ $salesCount }}
                        </span>
                    </div>
                @endif
            </a>
        </div>
        @endif

        <!-- 3. OPERATIONS (Projects, My Tasks, Review & QC) -->
        @if(Auth::user()->canAccessModule('projects') || Auth::user()->canAccessModule('documentation'))
        @php
            $isOperations = request()->routeIs('projects.*', 'documentations.*');
            $operationsRoute = Auth::user()->canAccessModule('projects') ? route('projects.index') : route('documentations.index');
        @endphp
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>3. Operations</span>
                @if($opsNewCount > 0)
                    <span class="px-1.5 py-0.2 rounded bg-cyan-500/20 text-[#22D3EE] border border-cyan-400/30 text-[9px] font-mono">
                        {{ $opsNewCount }} QUEUED
                    </span>
                @endif
            </div>
            <a href="{{ $operationsRoute }}"
               class="relative flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isOperations ? 'bg-[#2563EB] text-white shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A] hover:translate-x-0.5' }}"
               title="Work Management ({{ $opsCount }} Proyek Berjalan, {{ $counts['my_tasks_pending'] ?? 0 }} Tugas Aktif)">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <svg class="w-4 h-4 {{ $isOperations ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        @if($opsCount > 0)
                            <span x-show="sidebarCollapsed" class="absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-blue-500 text-white text-[9px] font-bold flex items-center justify-center shadow">
                                {{ $opsCount }}
                            </span>
                        @endif
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <div>Work Management</div>
                        <div class="text-[10px] font-normal {{ $isOperations ? 'text-blue-100' : 'text-slate-400' }}">
                            {{ $opsCount }} Proyek &bull; {{ $counts['my_tasks_pending'] ?? 0 }} Tugas Aktif
                        </div>
                    </div>
                </div>

                @if($opsCount > 0)
                    <span x-show="!sidebarCollapsed"
                          class="shrink-0 min-w-[22px] h-5 px-1.5 rounded-full text-[10px] font-mono font-bold flex items-center justify-center transition-all {{ $isOperations ? 'bg-white text-[#2563EB] shadow-2xs' : 'bg-blue-500/20 text-[#22D3EE] border border-cyan-400/30 group-hover:bg-blue-500 group-hover:text-white' }}"
                          title="{{ $opsCount }} Proyek sedang berjalan / belum selesai">
                        {{ $opsCount }}
                    </span>
                @endif
            </a>
        </div>
        @endif

        <!-- 4. FINANCE & REPORTS (Payments, Purchases, Cashbook, Monthly Reports) -->
        @if(Auth::user()->canAccessModule('payments') || Auth::user()->canAccessModule('purchases') || Auth::user()->canAccessModule('financials') || Auth::user()->canAccessModule('reports'))
        @php
            $isFinance = request()->routeIs('payments.*', 'purchases.*', 'transactions.*', 'reports.*');
            $financeRoute = Auth::user()->canAccessModule('payments') ? route('payments.index') : route('purchases.index');
        @endphp
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                <span>4. Finance &amp; Reports</span>
                @if(($counts['payments_pending'] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[9px] font-mono animate-pulse">
                        {{ $counts['payments_pending'] }} VERIFIKASI
                    </span>
                @endif
            </div>
            <a href="{{ $financeRoute }}"
               class="relative flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ $isFinance ? 'bg-[#2563EB] text-white shadow-md shadow-blue-900/30' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A] hover:translate-x-0.5' }}"
               title="Finance & Reports ({{ $counts['payments_pending'] ?? 0 }} Pembayaran Pending, {{ $counts['purchases_active'] ?? 0 }} Pembelian Aktif)">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="relative shrink-0">
                        <svg class="w-4 h-4 {{ $isFinance ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @if($finCount > 0)
                            <span x-show="sidebarCollapsed" class="absolute -top-2 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-emerald-500 text-white text-[9px] font-bold flex items-center justify-center shadow">
                                {{ $finCount }}
                            </span>
                        @endif
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <div>Finance &amp; Reports</div>
                        <div class="text-[10px] font-normal {{ $isFinance ? 'text-blue-100' : 'text-slate-400' }}">Pembayaran, Kas &amp; Laporan</div>
                    </div>
                </div>

                @if($finCount > 0)
                    <span x-show="!sidebarCollapsed"
                          class="shrink-0 min-w-[22px] h-5 px-1.5 rounded-full text-[10px] font-mono font-bold flex items-center justify-center transition-all {{ $isFinance ? 'bg-white text-[#2563EB] shadow-2xs' : (($counts['payments_pending'] ?? 0) > 0 ? 'bg-emerald-500 text-white shadow-xs' : 'bg-blue-500/20 text-[#22D3EE] border border-cyan-400/30') }}"
                          title="{{ $counts['payments_pending'] ?? 0 }} Pembayaran menunggu verifikasi, {{ $counts['purchases_active'] ?? 0 }} Pembelian berjalan">
                        {{ $finCount }}
                    </span>
                @endif
            </a>
        </div>
        @endif

        <!-- 5. SYSTEM FLOW & NAVIGATION GUIDE (Bottom Tracking Menu for All Users) -->
        <div class="pt-3 border-t border-[#1E2E4A]">
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-[#22D3EE]">
                Panduan Penggunaan Sistem
            </div>
            <a href="{{ route('workflow-guide.index') }}"
               class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('workflow-guide.*') ? 'bg-gradient-to-r from-[#2563EB] to-[#0891B2] text-white shadow-md' : 'bg-[#1E2E4A]/50 border border-[#22D3EE]/25 text-slate-200 hover:text-white hover:bg-[#1E2E4A] hover:border-[#22D3EE]/50 hover:translate-x-0.5' }}"
               title="Tracking Alur Sistem: Klik Menu Apa, Klik Action Apa & Pindah ke Role Mana">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('workflow-guide.*') ? 'text-white' : 'text-[#22D3EE]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <div>Alur &amp; Panduan Sistem</div>
                        <div class="text-[10px] font-normal {{ request()->routeIs('workflow-guide.*') ? 'text-cyan-100' : 'text-slate-400' }}">Klik Menu, Action &amp; Pindah Role</div>
                    </div>
                </div>
                <span x-show="!sidebarCollapsed" class="shrink-0 px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-cyan-500/20 text-[#22D3EE] border border-cyan-400/30">
                    TRACK
                </span>
            </a>
        </div>

    </div>

    <!-- COMPACT ADMINISTRATIVE & PROFILE AREA (Restricted / Subtle) -->
    <div class="px-3 py-2.5 border-t border-[#1E2E4A] bg-[#131F33] space-y-1">
        @if(Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
            <div x-show="!sidebarCollapsed" class="px-2 pb-1 text-[9px] font-bold uppercase tracking-widest text-slate-500">
                Administration
            </div>
            <a href="{{ route('activity-logs.index') }}"
               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-[11px] font-medium transition {{ request()->routeIs('activity-logs.*') ? 'bg-[#1E2E4A] text-[#22D3EE]' : 'text-slate-400 hover:text-white hover:bg-[#1E2E4A]/60' }}"
               title="System Audit Log & Super Admin Access">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Audit Log &amp; System</span>
            </a>
        @endif

        <a href="{{ route('profile.edit') }}"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg hover:bg-[#1E2E4A]/60 transition group"
           title="Pengaturan Profil & Akun">
            <div class="w-7 h-7 rounded-lg bg-blue-600/30 text-[#22D3EE] font-bold flex items-center justify-center shrink-0 border border-blue-500/30 text-[11px]">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="overflow-hidden flex-1">
                <div class="text-xs font-semibold text-white truncate group-hover:text-[#22D3EE] transition">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-slate-400 font-mono truncate">{{ Auth::user()->role_label }}</div>
            </div>
        </a>
    </div>
</aside>
