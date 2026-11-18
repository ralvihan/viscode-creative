<x-app-layout x-data="{
    activeTab: '{{ $activeTab }}',
    userRoleFilter: 'all',
    userSearch: '',
    serviceCategoryFilter: 'all',
    customerSearch: '',

    openCreateUser: false,
    openEditUser: false,
    editUser: {},

    openCreateService: false,
    openEditService: false,
    editService: {},

    openCreateCustomer: false,
    openEditCustomer: false,
    editCustomer: {},

    openUserEditModal(user) {
        this.editUser = { ...user };
        this.openEditUser = true;
        this.$nextTick(() => {
            if (window.jQuery && this.$refs.editUserRole) {
                window.jQuery(this.$refs.editUserRole).val(user.role).trigger('change.select2');
            }
        });
    },
    openServiceEditModal(service) {
        this.editService = { ...service, is_active: Boolean(service.is_active) };
        this.openEditService = true;
        this.$nextTick(() => {
            if (window.jQuery && this.$refs.editServiceCategory) {
                window.jQuery(this.$refs.editServiceCategory).val(service.category).trigger('change.select2');
            }
        });
    },
    openCustomerEditModal(customer) {
        this.editCustomer = { ...customer };
        this.openEditCustomer = true;
        this.$nextTick(() => {
            if (window.jQuery && this.$refs.editCustomerType) {
                window.jQuery(this.$refs.editCustomerType).val(customer.type).trigger('change.select2');
            }
        });
    },
    jumpToRoleUsers(roleKey) {
        this.userRoleFilter = roleKey;
        this.activeTab = 'users';
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Master Data &amp; User Management
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Pusat pengelolaan data master non-transaksional: Akun Pengguna (Users), Role &amp; RBAC, Katalog Layanan &amp; Paket, Database Pelanggan, serta Referensi Sistem.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" x-show="activeTab === 'users' || activeTab === 'roles'" @click="openCreateUser = true"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>+ Tambah User Baru</span>
                </button>

                <button type="button" x-show="activeTab === 'services'" style="display: none;" @click="openCreateService = true"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Layanan / Paket</span>
                </button>

                <button type="button" x-show="activeTab === 'customers'" style="display: none;" @click="openCreateCustomer = true"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Tambah Pelanggan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- ========================================================= -->
        <!-- SUMMARY MASTER DATA KPI CARDS                             -->
        <!-- ========================================================= -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <button type="button" @click="activeTab = 'users'"
                    :class="activeTab === 'users' ? 'border-blue-500 ring-2 ring-blue-500/15 bg-blue-50/30' : 'border-slate-200 bg-white hover:border-blue-300'"
                    class="rounded-2xl border p-4 text-left shadow-2xs transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Master Users</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $users->count() }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Akun anggota tim aktif</div>
            </button>

            <button type="button" @click="activeTab = 'roles'"
                    :class="activeTab === 'roles' ? 'border-indigo-500 ring-2 ring-indigo-500/15 bg-indigo-50/30' : 'border-slate-200 bg-white hover:border-indigo-300'"
                    class="rounded-2xl border p-4 text-left shadow-2xs transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Master Roles (RBAC)</span>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ count($roleDefinitions) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Matriks hak akses modul</div>
            </button>

            <button type="button" @click="activeTab = 'services'"
                    :class="activeTab === 'services' ? 'border-emerald-500 ring-2 ring-emerald-500/15 bg-emerald-50/30' : 'border-slate-200 bg-white hover:border-emerald-300'"
                    class="rounded-2xl border p-4 text-left shadow-2xs transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Master Layanan &amp; Paket</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $services->count() }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">{{ $services->where('is_active', true)->count() }} Aktif &bull; {{ $services->where('is_active', false)->count() }} Nonaktif</div>
            </button>

            <button type="button" @click="activeTab = 'customers'"
                    :class="activeTab === 'customers' ? 'border-amber-500 ring-2 ring-amber-500/15 bg-amber-50/30' : 'border-slate-200 bg-white hover:border-amber-300'"
                    class="rounded-2xl border p-4 text-left shadow-2xs transition cursor-pointer">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Master Pelanggan</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-bold text-slate-900 font-mono mt-2">{{ $customers->count() }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">Database profil klien</div>
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- SEGMENTED WORKSPACE TABS (MASTER & USER MANAGEMENT)       -->
        <!-- ========================================================= -->
        <div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
            <button type="button" @click="activeTab = 'users'"
                    :class="activeTab === 'users' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>1. User Management</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'users' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ $users->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'roles'"
                    :class="activeTab === 'roles' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>2. Roles &amp; RBAC</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'roles' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ count($roleDefinitions) }}</span>
            </button>

            <button type="button" @click="activeTab = 'services'"
                    :class="activeTab === 'services' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>3. Master Layanan &amp; Paket</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'services' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ $services->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'customers'"
                    :class="activeTab === 'customers' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>4. Master Pelanggan</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'customers' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ $customers->count() }}</span>
            </button>

            <button type="button" @click="activeTab = 'references'"
                    :class="activeTab === 'references' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                <span>5. Master Referensi Sistem</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="activeTab === 'references' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">5</span>
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 1: USER MANAGEMENT                                    -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'users'" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 lg:pb-0">
                    <button type="button" @click="userRoleFilter = 'all'"
                            :class="userRoleFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>Semua Role</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="userRoleFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700'">{{ $users->count() }}</span>
                    </button>

                    @foreach($roleDefinitions as $roleKey => $roleMeta)
                        @if(($roleUserCounts[$roleKey] ?? 0) > 0)
                        <button type="button" @click="userRoleFilter = '{{ $roleKey }}'"
                                :class="userRoleFilter === '{{ $roleKey }}' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                                class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                            <span>{{ $roleMeta['label'] }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="userRoleFilter === '{{ $roleKey }}' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ $roleUserCounts[$roleKey] ?? 0 }}</span>
                        </button>
                        @endif
                    @endforeach
                </div>

                <div class="w-full lg:w-64 shrink-0">
                    <input type="text" x-model="userSearch" placeholder="Cari nama atau email user..."
                           class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-xs focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Pengguna</th>
                            <th class="py-3.5 px-4">Email Login</th>
                            <th class="py-3.5 px-4">Role &amp; Hak Akses</th>
                            <th class="py-3.5 px-4">Terdaftar</th>
                            <th class="py-3.5 px-4 text-right">Kelola Akun</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($users as $u)
                        @php
                            $roleBadge = $roleDefinitions[$u->role]['badge'] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            $searchHaystack = strtolower($u->name.' '.$u->email.' '.$u->role_label);
                        @endphp
                        <tr x-show="(userRoleFilter === 'all' || userRoleFilter === '{{ $u->role }}') && (!userSearch || '{{ addslashes($searchHaystack) }}'.includes(userSearch.toLowerCase()))"
                            class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#17243B] text-[#22D3EE] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[9px] font-bold uppercase">Akun Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">ID #{{ $u->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-700">{{ $u->email }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                                    {{ $u->role_label }}
                                </span>
                                <div class="text-[10px] text-slate-400 font-mono mt-1">kode: {{ $u->role }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <button type="button"
                                            @click="openUserEditModal({{ Js::from(['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role]) }})"
                                            class="px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                        Edit / Role
                                    </button>

                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('master.users.destroy', $u) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Hapus Akun Pengguna?',
                                                  html: 'Apakah Anda yakin ingin menghapus akun <b>{{ addslashes($u->name) }}</b> ({{ $u->email }})?<br><span class=\'text-xs text-rose-600\'>Pengguna tidak akan dapat login kembali ke sistem.</span>',
                                                  icon: 'warning',
                                                  confirmButtonText: 'Ya, Hapus User',
                                                  isDanger: true
                                              })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 2: ROLES & RBAC MATRIX                                -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'roles'" style="display: none;" class="space-y-4">
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-xs text-blue-900 leading-relaxed">
                <strong>Matriks Master Role &amp; RBAC (Role-Based Access Control):</strong> Menentukan hak akses modul serta batas wewenang persetujuan setiap anggota tim di dalam Viscode BMS. Perubahan role pengguna dilakukan pada tab <strong>1. User Management</strong>.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($roleDefinitions as $roleKey => $roleMeta)
                    @php
                        $countInRole = $roleUserCounts[$roleKey] ?? 0;
                    @endphp
                    <div class="bg-white border border-slate-200 hover:border-blue-300 rounded-2xl p-5 shadow-2xs flex flex-col justify-between transition">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $roleMeta['badge'] }}">
                                    {{ $roleMeta['label'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-mono text-[10px] font-bold">
                                    {{ $countInRole }} User
                                </span>
                            </div>

                            <div class="mt-2.5 flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                                <span>Key: <strong class="text-slate-700">{{ $roleKey }}</strong></span>
                                <span>&bull;</span>
                                <span>{{ $roleMeta['level'] }}</span>
                            </div>

                            <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                                {{ $roleMeta['description'] }}
                            </p>

                            <div class="mt-3.5">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Modul yang Diizinkan:</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($roleMeta['modules'] as $mod)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-700">
                                            {{ $mod }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                {{ $countInRole > 0 ? $countInRole.' anggota terdaftar' : 'Belum ada anggota' }}
                            </span>
                            <button type="button" @click="jumpToRoleUsers('{{ $roleKey }}')"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 transition cursor-pointer">
                                Lihat User &rarr;
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 3: MASTER LAYANAN & PAKET (FULL CRUD)                 -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'services'" style="display: none;" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button type="button" @click="serviceCategoryFilter = 'all'"
                            :class="serviceCategoryFilter === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>Semua Layanan</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="serviceCategoryFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-700'">{{ $services->count() }}</span>
                    </button>

                    @foreach(['web_development' => 'Web Development', 'graphic_design' => 'Graphic Design', 'id_card_event' => 'ID Card & Event', 'bundling' => 'Paket Bundling'] as $catKey => $catLabel)
                    <button type="button" @click="serviceCategoryFilter = '{{ $catKey }}'"
                            :class="serviceCategoryFilter === '{{ $catKey }}' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>{{ $catLabel }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="serviceCategoryFilter === '{{ $catKey }}' ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-700'">{{ $services->where('category', $catKey)->count() }}</span>
                    </button>
                    @endforeach
                </div>

                <button type="button" @click="openCreateService = true"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer shrink-0">
                    + Tambah Layanan Baru
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Nama Layanan &amp; Deskripsi</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4 text-right">Harga Dasar</th>
                            <th class="py-3.5 px-4 text-center">Dipakai di Transaksi</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($services as $svc)
                        @php
                            $svcUsage = $svc->orders_count + $svc->quotations_count + $svc->leads_count;
                        @endphp
                        <tr x-show="serviceCategoryFilter === 'all' || serviceCategoryFilter === '{{ $svc->category }}'"
                            class="hover:bg-slate-50/80 transition {{ !$svc->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                            <td class="py-3.5 px-4 max-w-sm">
                                <div class="font-bold text-slate-900 text-sm">{{ $svc->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $svc->description ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ str_replace('_', ' ', $svc->category) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($svc->base_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100">{{ $svc->orders_count }} Order</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100">{{ $svc->quotations_count }} Quo</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100">{{ $svc->leads_count }} Lead</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($svc->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">AKTIF</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">NONAKTIF</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            @click="openServiceEditModal({{ Js::from($svc) }})"
                                            class="px-2.5 py-1.5 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                        Edit
                                    </button>

                                    <form action="{{ route('master.services.toggle', $svc) }}" method="POST" class="inline"
                                          onsubmit="return window.confirmFormSubmit(event, {
                                              title: '{{ $svc->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Layanan?',
                                              text: 'Status layanan {{ addslashes($svc->name) }} akan diubah menjadi {{ $svc->is_active ? 'NONAKTIF' : 'AKTIF' }}.',
                                              icon: 'question',
                                              confirmButtonText: 'Ya, Ubah Status'
                                          })">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-white hover:bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                            {{ $svc->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    @if($svcUsage === 0)
                                        <form action="{{ route('master.services.destroy', $svc) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Hapus Master Layanan?',
                                                  html: 'Yakin ingin menghapus layanan <b>{{ addslashes($svc->name) }}</b> secara permanen?',
                                                  icon: 'warning',
                                                  confirmButtonText: 'Ya, Hapus',
                                                  isDanger: true
                                              })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">Belum ada data master layanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 4: MASTER PELANGGAN (DATABASE KLIEN)                  -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'customers'" style="display: none;" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="w-full sm:w-80">
                    <input type="text" x-model="customerSearch" placeholder="Cari nama, nomor WA, atau email klien..."
                           class="w-full rounded-xl bg-white border-slate-300 text-slate-800 text-xs focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('customers.index') }}" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl transition">
                        Buka Mode Operasional Klien &rarr;
                    </a>
                    <button type="button" @click="openCreateCustomer = true" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer">
                        + Tambah Pelanggan
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Pelanggan / Instansi</th>
                            <th class="py-3.5 px-4">Kontak (WA &amp; Email)</th>
                            <th class="py-3.5 px-4">Segmen</th>
                            <th class="py-3.5 px-4 text-center">Riwayat Relasi</th>
                            <th class="py-3.5 px-4 text-right">Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($customers as $cust)
                        @php
                            $custUsage = $cust->orders_count + $cust->quotations_count + $cust->leads_count;
                            $custHaystack = strtolower($cust->name.' '.$cust->phone.' '.($cust->email ?? '').' '.$cust->type);
                        @endphp
                        <tr x-show="!customerSearch || '{{ addslashes($custHaystack) }}'.includes(customerSearch.toLowerCase())"
                            class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $cust->name }}</div>
                                @if($cust->notes)
                                    <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $cust->notes }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-semibold text-slate-800">{{ $cust->phone }}</div>
                                <div class="text-[11px] text-slate-500">{{ $cust->email ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ str_replace('_', ' ', $cust->type) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700">{{ $cust->orders_count }} Order</span>
                                <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700">{{ $cust->quotations_count }} Quo</span>
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700">{{ $cust->leads_count }} Lead</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            @click="openCustomerEditModal({{ Js::from($cust) }})"
                                            class="px-2.5 py-1.5 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                        Edit
                                    </button>
                                    @if($custUsage === 0)
                                        <form action="{{ route('master.customers.destroy', $cust) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Hapus Master Pelanggan?',
                                                  html: 'Hapus permanen data pelanggan <b>{{ addslashes($cust->name) }}</b>?',
                                                  icon: 'warning',
                                                  confirmButtonText: 'Ya, Hapus',
                                                  isDanger: true
                                              })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-xl transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">Belum ada data master pelanggan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- TAB 5: MASTER REFERENSI & KONFIGURASI SISTEM              -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'references'" style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kategori Layanan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Master Kategori Layanan</h3>
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-mono font-bold">{{ count($referenceData['service_categories']) }} Item</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($referenceData['service_categories'] as $ref)
                    <div class="py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-900">{{ $ref['label'] }}</span>
                            <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">{{ $ref['code'] }}</code>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $ref['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Segmen Pelanggan -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Master Segmen / Tipe Pelanggan</h3>
                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-mono font-bold">{{ count($referenceData['customer_types']) }} Item</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($referenceData['customer_types'] as $ref)
                    <div class="py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-900">{{ $ref['label'] }}</span>
                            <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">{{ $ref['code'] }}</code>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $ref['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sumber Prospek -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Master Sumber Prospek (Lead Sources)</h3>
                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-mono font-bold">{{ count($referenceData['lead_sources']) }} Item</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($referenceData['lead_sources'] as $ref)
                    <div class="py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-900">{{ $ref['label'] }}</span>
                            <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">{{ $ref['code'] }}</code>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $ref['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Master Metode Pembayaran Standar</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold">{{ count($referenceData['payment_methods']) }} Item</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($referenceData['payment_methods'] as $ref)
                    <div class="py-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-slate-900">{{ $ref['label'] }}</span>
                            <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">{{ $ref['code'] }}</code>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $ref['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Template Tugas Proyek -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs lg:col-span-2">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Master Template Standar Tugas Proyek (Auto-Checklist)</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Daftar tugas bawaan yang otomatis diterapkan saat Proyek baru diinisialisasi di menu Operations.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-[10px] font-mono font-bold">{{ count($referenceData['project_task_templates']) }} Template</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($referenceData['project_task_templates'] as $tpl)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-xs font-bold text-slate-900 mb-2">{{ $tpl['category'] }}</div>
                        <ul class="space-y-1.5 text-[11px] text-slate-600">
                            @foreach($tpl['tasks'] as $idx => $taskName)
                                <li class="flex items-start gap-1.5">
                                    <span class="font-mono font-bold text-blue-600">{{ $idx + 1 }}.</span>
                                    <span>{{ $taskName }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH USER BARU                                           -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateUser" title="Tambah Akun Pengguna Baru" maxWidth="lg">
        <form action="{{ route('master.users.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Raka Pratama" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="nama@viscode.id" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Role &amp; Hak Akses Sistem <span class="text-rose-500">*</span></label>
                <select name="role" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                    <option value="">-- Pilih Role Pengguna --</option>
                    @foreach($roleDefinitions as $roleKey => $roleMeta)
                        @if($roleKey !== 'super_admin' || Auth::user()->isSuperAdmin())
                            <option value="{{ $roleKey }}">{{ $roleMeta['label'] }} ({{ $roleMeta['level'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Password Awal <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Konfirmasi Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8" placeholder="Ulangi password" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openCreateUser = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan User Baru</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL: EDIT USER & ROLE                                           -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openEditUser" title="Edit Data Pengguna & Role" maxWidth="lg">
        <form :action="`/master-management/users/${editUser.id}`" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editUser.name" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" x-model="editUser.email" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Role &amp; Hak Akses <span class="text-rose-500">*</span></label>
                <select name="role" x-ref="editUserRole" x-model="editUser.role" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                    @foreach($roleDefinitions as $roleKey => $roleMeta)
                        @if($roleKey !== 'super_admin' || Auth::user()->isSuperAdmin())
                            <option value="{{ $roleKey }}">{{ $roleMeta['label'] }} ({{ $roleMeta['level'] }})</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="text-xs font-bold text-slate-700">Reset Password (Opsional — kosongkan jika tidak ingin mengganti password)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tetap" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openEditUser = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Perubahan User</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH MASTER LAYANAN / PAKET                              -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateService" title="Tambah Master Layanan / Paket Baru" maxWidth="lg">
        <form action="{{ route('master.services.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Layanan / Paket <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Website Company Profile Pro" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kategori Layanan <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="web_development">Web Development</option>
                        <option value="graphic_design">Graphic Design</option>
                        <option value="id_card_event">ID Card &amp; Event</option>
                        <option value="bundling">Paket Bundling</option>
                    </select>
                </div>

                <x-viscode-rupiah-input
                    name="base_price"
                    label="Harga Dasar Standar"
                    placeholder="Contoh: 500.000"
                    :required="true"
                />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Deskripsi Ruang Lingkup Layanan</label>
                <textarea name="description" rows="3" placeholder="Jelaskan fitur, deliverable, atau benefit paket ini..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20"></textarea>
            </div>

            <label class="inline-flex items-center gap-2.5 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Aktifkan langsung pada katalog penawaran &amp; pemesanan</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openCreateService = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Master Layanan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL: EDIT MASTER LAYANAN / PAKET                                -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openEditService" title="Edit Master Layanan / Paket" maxWidth="lg">
        <form :action="`/master-management/services/${editService.id}`" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Layanan / Paket <span class="text-rose-500">*</span></label>
                <input type="text" name="name" x-model="editService.name" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Kategori Layanan <span class="text-rose-500">*</span></label>
                    <select name="category" x-ref="editServiceCategory" x-model="editService.category" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20">
                        <option value="web_development">Web Development</option>
                        <option value="graphic_design">Graphic Design</option>
                        <option value="id_card_event">ID Card &amp; Event</option>
                        <option value="bundling">Paket Bundling</option>
                    </select>
                </div>

                <x-viscode-rupiah-input
                    name="base_price"
                    label="Harga Dasar Standar"
                    placeholder="Contoh: 500.000"
                    :required="true"
                    alpineValue="editService.base_price"
                />
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Deskripsi Ruang Lingkup Layanan</label>
                <textarea name="description" x-model="editService.description" rows="3" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-600 focus:ring focus:ring-blue-500/20"></textarea>
            </div>

            <label class="inline-flex items-center gap-2.5 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" x-model="editService.is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Layanan Aktif (tampil di pilihan Penawaran &amp; Pesanan)</span>
            </label>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openEditService = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Perubahan Layanan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL: TAMBAH MASTER PELANGGAN                                    -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateCustomer" title="Tambah Master Pelanggan Baru" maxWidth="lg">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Klien / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: CV Maju Jaya" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" required placeholder="081234567890" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="klien@email.com" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Segmen Pelanggan <span class="text-rose-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                        <option value="umkm">UMKM / Usaha Lokal</option>
                        <option value="organisasi">Organisasi Kampus / Sekolah</option>
                        <option value="mahasiswa">Mahasiswa / Komunitas</option>
                        <option value="event_organizer">Event Organizer</option>
                        <option value="individu">Individu</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Profil (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan khusus mengenai profil klien..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openCreateCustomer = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Master Pelanggan</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL: EDIT MASTER PELANGGAN                                      -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openEditCustomer" title="Edit Data Master Pelanggan" maxWidth="lg">
        <form :action="`/customers/${editCustomer.id}`" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nama Klien / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editCustomer.name" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" x-model="editCustomer.phone" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email (Opsional)</label>
                    <input type="email" name="email" x-model="editCustomer.email" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Segmen Pelanggan <span class="text-rose-500">*</span></label>
                    <select name="type" x-ref="editCustomerType" x-model="editCustomer.type" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm">
                        <option value="umkm">UMKM / Usaha Lokal</option>
                        <option value="organisasi">Organisasi Kampus / Sekolah</option>
                        <option value="mahasiswa">Mahasiswa / Komunitas</option>
                        <option value="event_organizer">Event Organizer</option>
                        <option value="individu">Individu</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Catatan Profil (Opsional)</label>
                <textarea name="notes" x-model="editCustomer.notes" rows="2" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="openEditCustomer = false" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Perubahan Pelanggan</button>
            </div>
        </form>
    </x-viscode-modal>
</x-app-layout>
