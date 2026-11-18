<!-- TOP NAVBAR (Pure White #FFFFFF with #E2E8F0 subtle border + Live Notification Pop-Up) -->
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
                    Prospek &amp; Leads
                @elseif(request()->routeIs('quotations.*'))
                    Penawaran (Quotation)
                @elseif(request()->routeIs('orders.*'))
                    Manajemen Pesanan
                @elseif(request()->routeIs('payments.*'))
                    Verifikasi Pembayaran
                @elseif(request()->routeIs('projects.*'))
                    Proyek &amp; Tugas
                @elseif(request()->routeIs('purchases.*'))
                    Pengajuan Pembelian
                @elseif(request()->routeIs('documentations.*'))
                    Dokumen &amp; Aset
                @elseif(request()->routeIs('transactions.*'))
                    Buku Kas Keuangan
                @elseif(request()->routeIs('reports.*'))
                    Laporan Bulanan Usaha
                @elseif(request()->routeIs('master.*'))
                    Master &amp; User Management
                @elseif(request()->routeIs('workflow-guide.*'))
                    Panduan &amp; Alur Sistem
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

    <!-- Right Side Actions: Role Badge + Notification Center + User Profile -->
    <div class="flex items-center gap-2.5 sm:gap-3">

        <!-- Role Badge Pill -->
        <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60 uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            {{ Auth::user()->role_label }}
        </div>

        <!-- ========================================================= -->
        <!-- IN-APP SYSTEM NOTIFICATION CENTER (BELL + POP-UP)         -->
        <!-- ========================================================= -->
        <div class="relative"
             x-data="{
                 notifOpen: false,
                 categoryFilter: 'all',
                 items: {{ Js::from($topbarNotifications ?? []) }},
                 unreadCount: {{ (int) ($topbarUnreadCount ?? 0) }},
                 categories: {{ Js::from($notificationCategories ?? []) }},
                 csrf: document.querySelector('meta[name=csrf-token]')?.content || '',

                 get filteredItems() {
                     if (this.categoryFilter === 'all') return this.items;
                     return this.items.filter(i => i.category === this.categoryFilter);
                 },

                 categoryLabel(cat) {
                     return this.categories[cat]?.label || 'Aktivitas';
                 },

                 categoryBadge(cat) {
                     return this.categories[cat]?.badge || 'bg-slate-100 text-slate-700 border-slate-200';
                 },

                 async markRead(item) {
                     if (!item.is_unread) return;
                     try {
                         const res = await fetch(item.read_url, {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': this.csrf,
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                         if (res.ok) {
                             const data = await res.json();
                             item.is_unread = false;
                             this.unreadCount = data.unread_count;
                             if (window.showToast) window.showToast('Notifikasi ditandai dibaca', 'success');
                         }
                     } catch (e) {}
                 },

                 visitNotification(item) {
                     if (item.is_unread) {
                         item.is_unread = false;
                         this.unreadCount = Math.max(0, this.unreadCount - 1);
                     }
                     window.location.href = item.visit_url;
                 },

                 async deleteNotification(item) {
                     try {
                         const res = await fetch(item.delete_url, {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': this.csrf,
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                         if (res.ok) {
                             const data = await res.json();
                             this.items = this.items.filter(n => n.id !== item.id);
                             this.unreadCount = data.unread_count;
                             if (window.showToast) window.showToast('Notifikasi dihapus', 'info');
                         }
                     } catch (e) {}
                 },

                 async markAllRead() {
                     if (this.unreadCount === 0) return;
                     try {
                         const res = await fetch('{{ route('notifications.read-all') }}', {
                             method: 'POST',
                             headers: {
                                 'X-CSRF-TOKEN': this.csrf,
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                         if (res.ok) {
                             this.items = this.items.map(i => ({ ...i, is_unread: false }));
                             this.unreadCount = 0;
                             if (window.showToast) window.showToast('Semua notifikasi ditandai dibaca', 'success');
                         }
                     } catch (e) {}
                 },

                 async clearAll() {
                     if (this.items.length === 0) return;
                     const confirm = window.confirmAction
                         ? await window.confirmAction({
                             title: 'Hapus Semua Notifikasi?',
                             text: 'Seluruh daftar notifikasi Anda akan dibersihkan secara permanen.',
                             icon: 'warning',
                             confirmButtonText: 'Ya, Hapus Semua',
                             isDanger: true
                         })
                         : { isConfirmed: true };

                     if (!confirm.isConfirmed) return;

                     try {
                         const res = await fetch('{{ route('notifications.clear-all') }}', {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': this.csrf,
                                 'Accept': 'application/json',
                                 'X-Requested-With': 'XMLHttpRequest'
                             }
                         });
                         if (res.ok) {
                             this.items = [];
                             this.unreadCount = 0;
                             if (window.showToast) window.showToast('Seluruh notifikasi dibersihkan', 'success');
                         }
                     } catch (e) {}
                 }
             }"
             @click.outside="notifOpen = false"
             @keydown.escape.window="notifOpen = false">

            <!-- Bell Trigger Button -->
            <button type="button"
                    @click="notifOpen = !notifOpen"
                    class="relative p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-blue-50 hover:border-blue-200 text-slate-600 hover:text-blue-600 transition cursor-pointer flex items-center justify-center"
                    :class="notifOpen ? 'bg-blue-50 border-blue-300 text-blue-600 ring-2 ring-blue-500/15' : ''"
                    title="Notifikasi Aktivitas Sistem">
                <svg class="w-5 h-5" :class="unreadCount > 0 ? 'animate-pulse text-blue-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>

                <!-- Unread Counter Badge -->
                <span x-show="unreadCount > 0"
                      x-text="unreadCount > 99 ? '99+' : unreadCount"
                      style="display: none;"
                      class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1.5 rounded-full bg-rose-600 text-white text-[10px] font-mono font-bold flex items-center justify-center shadow-sm ring-2 ring-white">
                </span>
            </button>

            <!-- Top-Right Notification Pop-Up Panel -->
            <div x-show="notifOpen"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                 class="absolute right-0 mt-2.5 w-[340px] sm:w-[430px] rounded-2xl bg-white border border-slate-200 shadow-2xl z-50 overflow-hidden">

                <!-- Pop-up Header -->
                <div class="px-4 py-3.5 bg-[#17243B] text-white flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#22D3EE]" :class="unreadCount > 0 ? 'animate-ping' : ''"></span>
                        <div>
                            <h4 class="text-xs font-bold tracking-tight">Notifikasi Aktivitas Sistem</h4>
                            <p class="text-[10px] text-slate-300">
                                <span x-text="unreadCount"></span> belum dibaca &bull; <span x-text="items.length"></span> total terbaru
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                x-show="unreadCount > 0"
                                @click="markAllRead()"
                                class="px-2 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[10px] font-semibold text-[#22D3EE] transition cursor-pointer"
                                title="Tandai semua sudah dibaca">
                            Baca Semua
                        </button>
                        <button type="button"
                                x-show="items.length > 0"
                                @click="clearAll()"
                                class="px-2 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/35 text-[10px] font-semibold text-rose-200 transition cursor-pointer"
                                title="Hapus semua notifikasi">
                            Hapus Semua
                        </button>
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 flex items-center gap-1.5 overflow-x-auto">
                    <button type="button" @click="categoryFilter = 'all'"
                            :class="categoryFilter === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Semua
                    </button>
                    <button type="button" @click="categoryFilter = 'pesanan_baru'"
                            :class="categoryFilter === 'pesanan_baru' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Pesanan Baru
                    </button>
                    <button type="button" @click="categoryFilter = 'user_action'"
                            :class="categoryFilter === 'user_action' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Aksi User
                    </button>
                    <button type="button" @click="categoryFilter = 'perubahan_data'"
                            :class="categoryFilter === 'perubahan_data' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Perubahan Data
                    </button>
                    <button type="button" @click="categoryFilter = 'keuangan'"
                            :class="categoryFilter === 'keuangan' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Keuangan
                    </button>
                    <button type="button" @click="categoryFilter = 'operasional'"
                            :class="categoryFilter === 'operasional' ? 'bg-purple-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'"
                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap transition cursor-pointer">
                        Proyek &amp; QC
                    </button>
                </div>

                <!-- Scrollable Notification List -->
                <div class="max-h-[400px] overflow-y-auto divide-y divide-slate-100">
                    <template x-for="item in filteredItems" :key="item.id">
                        <div class="p-3.5 transition-colors hover:bg-slate-50"
                             :class="item.is_unread ? 'bg-blue-50/40' : 'bg-white'">
                            <div class="flex items-start gap-3">
                                <!-- Unread / Read Indicator Dot -->
                                <div class="mt-1.5 shrink-0">
                                    <span class="block w-2.5 h-2.5 rounded-full"
                                          :class="item.is_unread ? 'bg-blue-600 ring-4 ring-blue-100' : 'bg-slate-300'"></span>
                                </div>

                                <!-- Clickable Content Area (Click to Read & Direct!) -->
                                <div class="flex-1 min-w-0">
                                    <div @click="visitNotification(item)" class="cursor-pointer group">
                                        <div class="flex items-center justify-between gap-2 mb-1">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border"
                                                  :class="categoryBadge(item.category)"
                                                  x-text="categoryLabel(item.category)">
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono whitespace-nowrap" x-text="item.time_ago"></span>
                                        </div>

                                        <div class="text-xs font-bold text-slate-900 group-hover:text-blue-600 transition leading-snug" x-text="item.title"></div>
                                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed line-clamp-2" x-text="item.message"></p>
                                    </div>

                                    <!-- Per-Item Action Row: Baca | Direct | Hapus -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-100/80 flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-slate-400 truncate">
                                            Modul: <strong class="text-slate-600 font-mono" x-text="item.module"></strong>
                                        </span>

                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button"
                                                    x-show="item.is_unread"
                                                    @click.stop="markRead(item)"
                                                    class="px-2 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[10px] font-bold transition cursor-pointer inline-flex items-center gap-1"
                                                    title="Tandai sudah dibaca">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Baca</span>
                                            </button>

                                            <button type="button"
                                                    @click.stop="visitNotification(item)"
                                                    class="px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-[10px] font-bold transition cursor-pointer inline-flex items-center gap-1"
                                                    title="Baca & arahkan ke halaman terkait">
                                                <span>Buka</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                            </button>

                                            <button type="button"
                                                    @click.stop="deleteNotification(item)"
                                                    class="p-1 rounded-lg bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition cursor-pointer"
                                                    title="Hapus notifikasi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="filteredItems.length === 0" class="py-10 px-6 text-center">
                        <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div class="text-xs font-bold text-slate-700">Tidak ada notifikasi</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Seluruh aktivitas pada kategori ini sudah bersih atau belum ada pembaruan terbaru.</p>
                    </div>
                </div>

                <!-- Pop-up Footer -->
                @if(Auth::user()->isSuperAdmin() || Auth::user()->isCeo())
                <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">Klik notifikasi untuk baca &amp; buka halaman</span>
                    <a href="{{ route('activity-logs.index') }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                        Lihat Audit Log &rarr;
                    </a>
                </div>
                @endif
            </div>
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
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
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
