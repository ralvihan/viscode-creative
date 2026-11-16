<!-- TOP NAVBAR (Pure White #FFFFFF with #E2E8F0 subtle border) -->
<header class="sticky top-0 z-30 h-16 bg-white border-b border-slate-200/80 shadow-sm flex items-center justify-between px-4 sm:px-8 select-none">
    <div class="flex items-center gap-3">
        <!-- Mobile Sidebar Toggle -->
        <button type="button" @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Desktop Sidebar Collapse Toggle -->
        <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition cursor-pointer" title="Perluas / Ciutkan Navigasi">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
        </button>

        <!-- Dynamic Context / Breadcrumb -->
        <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 font-medium">
            <span class="hover:text-slate-600">Viscode BMS</span>
            <span>/</span>
            <span class="font-semibold text-slate-800">
                @if(request()->routeIs('dashboard'))
                    Command Center
                @elseif(request()->routeIs('customers.*'))
                    Manajemen Pelanggan
                @elseif(request()->routeIs('leads.*'))
                    Prospek & Leads
                @elseif(request()->routeIs('quotations.*'))
                    Penawaran (Quotation)
                @elseif(request()->routeIs('orders.*'))
                    Manajemen Pesanan
                @elseif(request()->routeIs('payments.*'))
                    Verifikasi Pembayaran
                @elseif(request()->routeIs('projects.*'))
                    Proyek & Tugas
                @elseif(request()->routeIs('purchases.*'))
                    Pengajuan Pembelian
                @elseif(request()->routeIs('documentations.*'))
                    Dokumen & Aset
                @elseif(request()->routeIs('transactions.*'))
                    Buku Kas Keuangan
                @elseif(request()->routeIs('reports.*'))
                    Laporan Bulanan Usaha
                @elseif(request()->routeIs('activity-logs.*'))
                    Audit Trail Aktivitas
                @elseif(request()->routeIs('profile.*'))
                    Pengaturan Profil
                @else
                    Dashboard
                @endif
            </span>
        </div>
    </div>

    <!-- Right Side Actions & User Menu -->
    <div class="flex items-center gap-3">
        <!-- Shortcut to Public Website -->
        <a href="{{ route('home') }}" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-blue-600 hover:bg-blue-50/70 border border-slate-200 transition">
            <span>Website Publik</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>

        <!-- Role Badge Pill -->
        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60 uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            {{ Auth::user()->role_label }}
        </div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ userMenu: false }" @click.outside="userMenu = false">
            <button type="button" @click="userMenu = !userMenu" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <div class="w-8 h-8 rounded-xl bg-[#17243B] text-white font-bold flex items-center justify-center text-xs shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="hidden md:block text-left text-xs leading-tight">
                    <div class="font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-slate-500">{{ Auth::user()->email }}</div>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Dropdown Content -->
            <div x-show="userMenu" style="display: none;"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-slate-200 shadow-xl py-2 z-50 text-xs">
                
                <div class="px-4 py-2 border-b border-slate-100">
                    <div class="font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-slate-500 text-[11px] truncate">{{ Auth::user()->email }}</div>
                    <div class="mt-1 text-[10px] font-bold text-blue-600 uppercase">{{ Auth::user()->role_label }}</div>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-medium transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Pengaturan Profil
                    </a>

                    <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-medium transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka Website Publik
                    </a>
                </div>

                <div class="border-t border-slate-100 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-red-600 hover:bg-red-50 font-semibold transition text-left cursor-pointer">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar (Log Out)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
