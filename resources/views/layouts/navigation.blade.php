<nav x-data="{ open: false, salesMenu: false, opsMenu: false, financeMenu: false }" class="bg-[#14213D] border-b border-slate-800 text-slate-200 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="font-extrabold text-base tracking-wider text-white">
                            VISCODE <span class="text-cyan-400">BMS</span>
                        </span>
                        <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">v1.0</span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden md:flex md:items-center md:space-x-1 lg:space-x-2 text-xs font-semibold">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        Dashboard
                    </a>

                    @if(Auth::user()->canAccessModule('leads'))
                    <!-- Sales & Leads Dropdown -->
                    <div class="relative" x-data="{ openDropdown: false }" @click.outside="openDropdown = false">
                        <button @click="openDropdown = !openDropdown" class="px-3 py-2 rounded-lg transition flex items-center gap-1 {{ request()->routeIs('leads.*') || request()->routeIs('quotations.*') || request()->routeIs('customers.*') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Sales & CRM
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="openDropdown" style="display: none;" class="absolute left-0 mt-2 w-48 rounded-xl bg-[#0B132B] border border-slate-700 shadow-xl py-2 z-50">
                            <a href="{{ route('customers.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('customers.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Data Pelanggan</a>
                            <a href="{{ route('leads.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('leads.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Prospek & Leads</a>
                            <a href="{{ route('quotations.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('quotations.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Penawaran (Quotation)</a>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('projects'))
                    <!-- Operasional Proyek Dropdown -->
                    <div class="relative" x-data="{ openDropdown: false }" @click.outside="openDropdown = false">
                        <button @click="openDropdown = !openDropdown" class="px-3 py-2 rounded-lg transition flex items-center gap-1 {{ request()->routeIs('orders.*') || request()->routeIs('projects.*') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Operasional
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="openDropdown" style="display: none;" class="absolute left-0 mt-2 w-48 rounded-xl bg-[#0B132B] border border-slate-700 shadow-xl py-2 z-50">
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('orders.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Pesanan Masuk</a>
                            <a href="{{ route('projects.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('projects.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Proyek & Tugas</a>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->canAccessModule('payments') || Auth::user()->canAccessModule('financials') || Auth::user()->canAccessModule('purchases'))
                    <!-- Keuangan Dropdown -->
                    <div class="relative" x-data="{ openDropdown: false }" @click.outside="openDropdown = false">
                        <button @click="openDropdown = !openDropdown" class="px-3 py-2 rounded-lg transition flex items-center gap-1 {{ request()->routeIs('payments.*') || request()->routeIs('transactions.*') || request()->routeIs('purchases.*') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                            Keuangan
                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="openDropdown" style="display: none;" class="absolute left-0 mt-2 w-52 rounded-xl bg-[#0B132B] border border-slate-700 shadow-xl py-2 z-50">
                            <a href="{{ route('payments.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('payments.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Verifikasi Pembayaran</a>
                            <a href="{{ route('transactions.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('transactions.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Buku Kas & Mutasi</a>
                            <a href="{{ route('purchases.index') }}" class="block px-4 py-2 text-xs hover:bg-slate-800 hover:text-cyan-300 {{ request()->routeIs('purchases.*') ? 'text-cyan-400 font-bold' : 'text-slate-300' }}">Pengajuan Pembelian</a>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('documentations.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('documentations.*') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        Dokumen & Aset
                    </a>

                    @if(Auth::user()->canAccessModule('reports'))
                    <a href="{{ route('reports.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-cyan-500/20 text-cyan-300 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        Laporan Bulanan
                    </a>
                    @endif

                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
                    <a href="{{ route('activity-logs.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('activity-logs.*') ? 'bg-amber-500/20 text-amber-300 font-bold' : 'text-slate-400 hover:text-amber-200 hover:bg-slate-800' }}">
                        Audit Log
                    </a>
                    @endif
                </div>
            </div>

            <!-- User Menu & Settings -->
            <div class="hidden md:flex md:items-center md:space-x-3">
                <div class="text-right">
                    <div class="text-xs font-bold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] font-semibold text-cyan-400 uppercase tracking-wide">{{ Auth::user()->role_label }}</div>
                </div>

                <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                    <button @click="userMenu = !userMenu" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white border border-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </button>
                    <div x-show="userMenu" style="display: none;" class="absolute right-0 mt-2 w-48 rounded-xl bg-[#0B132B] border border-slate-700 shadow-xl py-2 z-50 text-xs">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Profil Pengguna</a>
                        <a href="{{ route('home') }}" target="_blank" class="block px-4 py-2 text-slate-400 hover:bg-slate-800 hover:text-cyan-300">Lihat Website Publik ↗</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-800">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 hover:bg-rose-500/10">Keluar (Log Out)</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden border-t border-slate-800 bg-[#0B132B] px-4 pt-2 pb-4 space-y-1 text-xs">
        <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Dashboard</a>
        
        @if(Auth::user()->canAccessModule('leads'))
        <div class="pt-2 text-[10px] font-bold uppercase text-slate-500 px-3">Sales & CRM</div>
        <a href="{{ route('customers.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Data Pelanggan</a>
        <a href="{{ route('leads.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Prospek & Leads</a>
        <a href="{{ route('quotations.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Penawaran (Quotation)</a>
        @endif

        @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('projects'))
        <div class="pt-2 text-[10px] font-bold uppercase text-slate-500 px-3">Operasional</div>
        <a href="{{ route('orders.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Pesanan Masuk</a>
        <a href="{{ route('projects.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Proyek & Tugas</a>
        @endif

        @if(Auth::user()->canAccessModule('payments') || Auth::user()->canAccessModule('financials') || Auth::user()->canAccessModule('purchases'))
        <div class="pt-2 text-[10px] font-bold uppercase text-slate-500 px-3">Keuangan</div>
        <a href="{{ route('payments.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Verifikasi Pembayaran</a>
        <a href="{{ route('transactions.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Buku Kas & Mutasi</a>
        <a href="{{ route('purchases.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Pengajuan Pembelian</a>
        @endif

        <div class="pt-2 text-[10px] font-bold uppercase text-slate-500 px-3">Lainnya</div>
        <a href="{{ route('documentations.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Dokumen & Aset</a>
        @if(Auth::user()->canAccessModule('reports'))
        <a href="{{ route('reports.index') }}" class="block py-2 px-3 rounded-lg text-slate-300 hover:bg-slate-800">Laporan Bulanan</a>
        @endif
        @if(Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
        <a href="{{ route('activity-logs.index') }}" class="block py-2 px-3 rounded-lg text-amber-300 hover:bg-slate-800">Audit Log</a>
        @endif

        <div class="pt-4 border-t border-slate-800 flex justify-between items-center px-3">
            <div>
                <div class="font-bold text-white">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-cyan-400 uppercase">{{ Auth::user()->role_label }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-rose-400 hover:underline">Log Out</button>
            </form>
        </div>
    </div>
</nav>
