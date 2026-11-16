<!-- SIDEBAR NAVIGATION (Deep Navy #17243B — 4 Primary Groups + Compact Admin) -->
<aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-[#17243B] border-r border-[#1E2E4A] transition-all duration-300 select-none"
       :class="{
           'translate-x-0 w-64': !sidebarCollapsed,
           'translate-x-0 w-20': sidebarCollapsed,
           '-translate-x-full lg:translate-x-0': !sidebarOpen
       }"
       @click.outside="sidebarOpen = false">

    <!-- Header Logo -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-[#1E2E4A]">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#2563EB] to-[#22D3EE] flex items-center justify-center shrink-0 shadow-md shadow-blue-900/30">
                <span class="text-white font-black text-sm tracking-tighter">VC</span>
            </div>
            <div x-show="!sidebarCollapsed" class="transition-opacity duration-200">
                <span class="font-bold text-base text-white tracking-tight">VISCODE<span class="text-[#22D3EE]">.</span></span>
                <span class="ml-1.5 text-[10px] font-mono px-1.5 py-0.5 rounded bg-blue-950/80 text-[#22D3EE] border border-cyan-800/50">BMS</span>
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Primary Business Navigation (4 Core Groups) -->
    <div class="flex-1 overflow-y-auto py-5 px-3 space-y-5">

        <!-- 1. DASHBOARD -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                1. Dashboard
            </div>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition group {{ request()->routeIs('dashboard') ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A]' }}"
               title="Business Overview, Action Center & Workflow">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <div x-show="!sidebarCollapsed" class="truncate">
                    <div>Dashboard Utama</div>
                    <div class="text-[10px] font-normal {{ request()->routeIs('dashboard') ? 'text-blue-100' : 'text-slate-400' }}">Overview & Action Center</div>
                </div>
            </a>
        </div>

        <!-- 2. BUSINESS MANAGEMENT (Sales & Orders, Pipeline, Customers, Services) -->
        @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('quotations') || Auth::user()->canAccessModule('leads') || Auth::user()->canAccessModule('customers'))
        @php
            $isBusinessMgmt = request()->routeIs('orders.*', 'quotations.*', 'leads.*', 'customers.*');
            $businessRoute = Auth::user()->canAccessModule('orders') ? route('orders.index') : route('leads.index');
        @endphp
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                2. Business Management
            </div>
            <a href="{{ $businessRoute }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition group {{ $isBusinessMgmt ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A]' }}"
               title="Sales & Orders, Pipeline, Customers & Services">
                <svg class="w-4 h-4 shrink-0 {{ $isBusinessMgmt ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <div x-show="!sidebarCollapsed" class="truncate">
                    <div>Sales & Orders</div>
                    <div class="text-[10px] font-normal {{ $isBusinessMgmt ? 'text-blue-100' : 'text-slate-400' }}">Klien, Penawaran & Pesanan</div>
                </div>
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
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                3. Operations
            </div>
            <a href="{{ $operationsRoute }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition group {{ $isOperations ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A]' }}"
               title="Projects, My Tasks, Review & QC">
                <svg class="w-4 h-4 shrink-0 {{ $isOperations ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <div x-show="!sidebarCollapsed" class="truncate">
                    <div>Work Management</div>
                    <div class="text-[10px] font-normal {{ $isOperations ? 'text-blue-100' : 'text-slate-400' }}">Proyek, Tugas Saya & QC</div>
                </div>
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
            <div x-show="!sidebarCollapsed" class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                4. Finance & Reports
            </div>
            <a href="{{ $financeRoute }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition group {{ $isFinance ? 'bg-[#2563EB] text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#1E2E4A]' }}"
               title="Payments, Expenses, Financial Overview & Monthly Reports">
                <svg class="w-4 h-4 shrink-0 {{ $isFinance ? 'text-white' : 'text-slate-400 group-hover:text-[#22D3EE]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div x-show="!sidebarCollapsed" class="truncate">
                    <div>Finance & Reports</div>
                    <div class="text-[10px] font-normal {{ $isFinance ? 'text-blue-100' : 'text-slate-400' }}">Pembayaran, Kas & Laporan</div>
                </div>
            </a>
        </div>
        @endif

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
                <span x-show="!sidebarCollapsed" class="truncate">Audit Log & System</span>
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
