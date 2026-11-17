@props([
    'compactHeader' => false,
])

@php
    $currentUser = Auth::user();
    $currentRole = $currentUser->role;

    $mainFlowSteps = [
        [
            'step' => '01',
            'phase' => 'Fase 1: Akuisisi Prospek',
            'title' => 'Input Klien & Prospek Baru (Satu Kali Input)',
            'status_from' => 'FORM / INPUT',
            'status_to' => 'NEW',
            'status_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'roles' => ['sales', 'marketing', 'ceo', 'super_admin'],
            'role_badge' => 'Calon Klien (Web Publik) / Sales (Yoga) / Marketing (Feni)',
            'menu_group' => 'Sidebar: 1. Dashboard atau 2. Sales & Orders',
            'menu_tab' => 'Halaman Publik (/) ATAU Tab: Sales Pipeline (Leads)',
            'menu_url' => $currentUser->canAccessModule('leads') ? route('leads.index') : route('dashboard'),
            'menu_btn_label' => 'Buka Sales Pipeline',
            'action_button' => '+ Intake Klien / Lead',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Klik tombol "+ Intake Klien / Lead" di pojok kanan atas Dashboard atau "+ Input Klien & Lead Baru" di tab Sales Pipeline. Isi nama, WhatsApp, layanan, dan kebutuhan awal, lalu klik "Simpan Klien & Lead". (Atau otomatis masuk saat klien mengisi form di Landing Page).',
            'next_role' => 'Sales (Yoga K) / Marketing (Feni)',
            'next_menu' => 'Sidebar: 2. Sales & Orders -> Tab: Sales Pipeline (Leads)',
            'next_explanation' => 'Data Klien & Prospek tersimpan otomatis dengan status NEW tanpa perlu input ulang di menu lain. Selanjutnya Sales memeriksa prospek tersebut.',
        ],
        [
            'step' => '02',
            'phase' => 'Fase 1: Akuisisi Prospek',
            'title' => 'Review Data Prospek Masuk',
            'status_from' => 'NEW',
            'status_to' => 'REVIEWED',
            'status_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'roles' => ['sales', 'marketing', 'ceo', 'super_admin'],
            'role_badge' => 'Sales (Yoga K) / Marketing (Feni) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 2. Business Management (Sales & Orders)',
            'menu_tab' => 'Tab: Sales Pipeline (Leads)',
            'menu_url' => $currentUser->canAccessModule('leads') ? route('leads.index') : null,
            'menu_btn_label' => 'Buka Tab Sales Pipeline',
            'action_button' => 'Review Lead',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Cari baris prospek berstatus NEW pada tabel, klik tombol biru "Review Lead" di kolom paling kanan. Periksa detail kebutuhan klien pada pop-up, sesuaikan layanan/estimasi nilai jika perlu, lalu klik "Selesaikan Review Lead".',
            'next_role' => 'Sales (Yoga K) / Marketing (Feni)',
            'next_menu' => 'Tetap di Tab: Sales Pipeline (Leads)',
            'next_explanation' => 'Status prospek berubah menjadi REVIEWED. Tombol pada baris tabel otomatis berganti menjadi "Catat Follow-up" dan "Buat Penawaran".',
        ],
        [
            'step' => '03',
            'phase' => 'Fase 1: Akuisisi Prospek',
            'title' => 'Catat Komunikasi & Follow-up Klien',
            'status_from' => 'REVIEWED',
            'status_to' => 'FOLLOW_UP',
            'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'roles' => ['sales', 'marketing', 'ceo', 'super_admin'],
            'role_badge' => 'Sales (Yoga K) / Marketing (Feni)',
            'menu_group' => 'Sidebar: 2. Business Management (Sales & Orders)',
            'menu_tab' => 'Tab: Sales Pipeline (Leads)',
            'menu_url' => $currentUser->canAccessModule('leads') ? route('leads.index') : null,
            'menu_btn_label' => 'Buka Tab Sales Pipeline',
            'action_button' => 'Catat Follow-up',
            'action_button_color' => 'bg-amber-500 text-white',
            'action_detail' => 'Pada baris prospek berstatus REVIEWED, klik tombol "Catat Follow-up". Pilih status hasil komunikasi (Masih Dalam Proses Follow-up / Siap Diberikan Penawaran), isi ringkasan diskusi WhatsApp/Meeting, lalu klik "Simpan Riwayat Follow-up".',
            'next_role' => 'Sales (Yoga K)',
            'next_menu' => 'Tetap di Tab: Sales Pipeline (Leads)',
            'next_explanation' => 'Status berubah ke FOLLOW_UP. Kini Sales siap menerbitkan penawaran harga resmi dengan klik tombol "Buat Penawaran".',
        ],
        [
            'step' => '04',
            'phase' => 'Fase 2: Penawaran & Deal Order',
            'title' => 'Buat & Kirim Penawaran Harga (Quotation)',
            'status_from' => 'FOLLOW_UP',
            'status_to' => 'QUOTATION_SENT / SENT',
            'status_color' => 'bg-purple-50 text-purple-700 border-purple-200',
            'roles' => ['sales', 'ceo', 'super_admin'],
            'role_badge' => 'Sales (Yoga K) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 2. Business Management (Sales & Orders)',
            'menu_tab' => 'Tab: Sales Pipeline (Leads) -> lanjut ke Tab: Sales & Orders',
            'menu_url' => $currentUser->canAccessModule('leads') ? route('leads.index') : ($currentUser->canAccessModule('orders') ? route('orders.index') : null),
            'menu_btn_label' => 'Buka Sales Pipeline',
            'action_button' => 'Buat Penawaran -> Simpan & Kirim Penawaran',
            'action_button_color' => 'bg-purple-600 text-white',
            'action_detail' => 'Klik tombol ungu "Buat Penawaran" pada baris Lead. Data klien & paket otomatis terisi. Masukkan Harga Pekerjaan, Diskon (opsional), dan Estimasi Deadline, lalu klik "Simpan & Kirim Penawaran". (Jika pilih Simpan Draft, buka Tab: Sales & Orders lalu klik "Kirim Penawaran").',
            'next_role' => 'Sales (Yoga K) / CEO (Raihan)',
            'next_menu' => 'Sidebar: 2. Sales & Orders -> Tab: Sales & Orders (Tabel Atas: Daftar Penawaran)',
            'next_explanation' => 'Status Lead menjadi QUOTATION_SENT dan Quotation baru muncul di Tab: Sales & Orders dengan status SENT untuk menunggu persetujuan klien.',
        ],
        [
            'step' => '05',
            'phase' => 'Fase 2: Penawaran & Deal Order',
            'title' => 'Tandai Penawaran Disetujui & Konversi ke Order',
            'status_from' => 'SENT -> ACCEPTED',
            'status_to' => 'ORDER: WAITING_PAYMENT',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['sales', 'ceo', 'super_admin'],
            'role_badge' => 'Sales (Yoga K) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 2. Business Management (Sales & Orders)',
            'menu_tab' => 'Tab: Sales & Orders (Bagian Atas: Daftar Penawaran Harga)',
            'menu_url' => $currentUser->canAccessModule('orders') ? route('orders.index') : null,
            'menu_btn_label' => 'Buka Tab Sales & Orders',
            'action_button' => '1. Tandai Disetujui -> 2. Konversi ke Order',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Saat klien deal: (1) Klik tombol hijau "Tandai Disetujui" pada baris Quotation (status jadi ACCEPTED). (2) Tombol otomatis berubah menjadi biru "Konversi ke Order" — klik tombol tersebut, tentukan minimal DP & deadline proyek, lalu klik "Terbitkan Pesanan (Order)".',
            'next_role' => 'Sales (Yoga K) / Finance (Sutan Raja)',
            'next_menu' => 'Sidebar: 2. Sales & Orders (Tabel Bawah: Daftar Pesanan Proyek) atau Sidebar: 4. Finance & Reports',
            'next_explanation' => 'Quotation berubah menjadi CONVERTED, Lead menjadi WON, dan Pesanan baru tercipta dengan status WAITING_PAYMENT (menunggu pembayaran DP).',
        ],
        [
            'step' => '06',
            'phase' => 'Fase 3: Pembayaran DP & Aktivasi Proyek',
            'title' => 'Catat Pembayaran DP / Tagihan Masuk dari Klien',
            'status_from' => 'WAITING_PAYMENT',
            'status_to' => 'PAYMENT: PENDING',
            'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'roles' => ['sales', 'finance', 'ceo', 'super_admin'],
            'role_badge' => 'Sales (Yoga K) / Finance Manager (Sutan Raja)',
            'menu_group' => 'Sidebar: 2. Sales & Orders ATAU 4. Finance & Reports',
            'menu_tab' => 'Tab: Sales & Orders ATAU Tab: Pembayaran Masuk',
            'menu_url' => $currentUser->canAccessModule('orders') ? route('orders.index') : ($currentUser->canAccessModule('payments') ? route('payments.index') : null),
            'menu_btn_label' => 'Buka Sales & Orders',
            'action_button' => 'Catat Pembayaran',
            'action_button_color' => 'bg-amber-500 text-white',
            'action_detail' => 'Pada baris Order berstatus WAITING_PAYMENT di Tab: Sales & Orders, klik tombol kuning "Catat Pembayaran" (atau klik "+ Catat Pembayaran Baru" di menu Finance -> Pembayaran Masuk). Isi nominal transfer & upload foto bukti transfer, lalu klik "Simpan Pembayaran".',
            'next_role' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'next_menu' => 'Sidebar: 4. Finance & Reports -> Tab: Pembayaran Masuk (Payments)',
            'next_explanation' => 'Data pembayaran masuk ke antrean Finance dengan status PENDING. Tugas kini BERPINDAH KE ROLE FINANCE untuk memverifikasi mutasi rekening.',
        ],
        [
            'step' => '07',
            'phase' => 'Fase 3: Pembayaran DP & Aktivasi Proyek',
            'title' => 'Verifikasi Pembayaran (Trigger Otomatis Buat Proyek!)',
            'status_from' => 'PAYMENT: PENDING',
            'status_to' => 'VERIFIED & PROJECT: QUEUED',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['finance', 'ceo', 'super_admin'],
            'role_badge' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Pembayaran Masuk (Payments)',
            'menu_url' => $currentUser->canAccessModule('payments') ? route('payments.index') : null,
            'menu_btn_label' => 'Buka Pembayaran Masuk',
            'action_button' => 'Verifikasi Pembayaran',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Buka Sidebar: 4. Finance & Reports -> Tab: Pembayaran Masuk. Pada baris pembayaran berstatus PENDING, periksa bukti transfer lalu klik tombol hijau "Verifikasi Pembayaran" dan konfirmasi pada pop-up.',
            'next_role' => 'Tim Teknis: Web Dev / UI/UX (Fikri) / Graphic Design (Naufal) / CEO (Raihan)',
            'next_menu' => 'Sidebar: 3. Operations (Work Management) -> Tab: Proyek & Tugas',
            'next_explanation' => 'OTOMATIS: (1) Uang masuk ke Buku Kas, (2) Status Order menjadi IN_PROGRESS, dan (3) Sistem otomatis membuat Proyek baru (status QUEUED) beserta template tugas di menu Operations! Tugas berpindah ke TIM TEKNIS.',
        ],
        [
            'step' => '08',
            'phase' => 'Fase 4: Eksekusi Proyek & Quality Control',
            'title' => 'Mulai Proyek, Kerjakan Tugas & Ajukan Pemeriksaan QC',
            'status_from' => 'PROJECT: QUEUED',
            'status_to' => 'IN_PROGRESS -> REVIEW',
            'status_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'roles' => ['web_developer', 'ui_ux_designer', 'graphic_designer', 'ceo', 'super_admin'],
            'role_badge' => 'Tim Teknis: Naufal (Desain) / Fikri (UI/UX) / Web Dev / CEO (Raihan)',
            'menu_group' => 'Sidebar: 3. Operations (Work Management)',
            'menu_tab' => 'Tab: Proyek & Tugas',
            'menu_url' => $currentUser->canAccessModule('projects') ? route('projects.index') : null,
            'menu_btn_label' => 'Buka Work Management',
            'action_button' => '1. Mulai Proyek -> 2. Kerjakan & Kirim QC -> 3. Ajukan QC',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Buka Sidebar: 3. Operations (Work Management). (1) Klik tombol biru "Mulai Proyek" pada kartu proyek (status jadi IN_PROGRESS). (2) Pada rincian tugas di bawahnya, klik "Kerjakan" lalu setelah beres klik "Kirim QC". (3) Setelah semua progres siap diperiksa, klik tombol ungu "Ajukan QC" pada kartu proyek.',
            'next_role' => 'Quality Control & Admin (Morren) / CEO (Raihan)',
            'next_menu' => 'Tetap di Sidebar: 3. Operations (Work Management) -> Tab: Proyek & Tugas',
            'next_explanation' => 'Status Proyek beralih ke REVIEW. Tugas kini BERPINDAH KE ROLE QC & ADMIN (Morren) untuk memeriksa kualitas hasil kerja tim teknis.',
        ],
        [
            'step' => '09',
            'phase' => 'Fase 4: Eksekusi Proyek & Quality Control',
            'title' => 'Pemeriksaan & Persetujuan Quality Control (Approve QC)',
            'status_from' => 'PROJECT: REVIEW',
            'status_to' => 'COMPLETED & ORDER: READY_FOR_DELIVERY',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['qc_admin', 'ceo', 'super_admin'],
            'role_badge' => 'Quality Control & Admin (Morren) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 3. Operations (Work Management)',
            'menu_tab' => 'Tab: Proyek & Tugas',
            'menu_url' => $currentUser->canAccessModule('projects') ? route('projects.index') : null,
            'menu_btn_label' => 'Buka Work Management',
            'action_button' => 'Approve (Tugas) & Setujui QC (Selesai)',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Role QC (Morren) membuka Sidebar: 3. Operations. (1) Klik tombol hijau "Approve" pada item tugas yang berstatus REVIEW. (2) Klik tombol hijau "Setujui QC (Selesai)" pada kartu proyek (atau klik ikon detail -> "Minta Revisi" jika perlu perbaikan oleh tim teknis).',
            'next_role' => 'Finance (Sutan) [Jika belum lunas] & Sales (Yoga) / QC (Morren)',
            'next_menu' => 'Sidebar: 2. Sales & Orders -> Tab: Sales & Orders',
            'next_explanation' => 'Proyek resmi COMPLETED dan status Order otomatis menjadi READY_FOR_DELIVERY. Tugas kembali ke Sales/Finance untuk pelunasan akhir dan penyerahan file ke klien.',
        ],
        [
            'step' => '10',
            'phase' => 'Fase 5: Pelunasan & Serah Terima Akhir',
            'title' => 'Verifikasi Pelunasan Tagihan & Serah Terima Hasil ke Klien',
            'status_from' => 'READY_FOR_DELIVERY + PAID',
            'status_to' => 'ORDER: COMPLETED',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['sales', 'finance', 'qc_admin', 'ceo', 'super_admin'],
            'role_badge' => 'Finance (Sutan) -> Sales (Yoga K) / QC Admin (Morren)',
            'menu_group' => 'Sidebar: 2. Business Management (Sales & Orders)',
            'menu_tab' => 'Tab: Sales & Orders (Tabel Bawah: Daftar Pesanan Proyek)',
            'menu_url' => $currentUser->canAccessModule('orders') ? route('orders.index') : null,
            'menu_btn_label' => 'Buka Tab Sales & Orders',
            'action_button' => 'Catat Pelunasan -> Serah Terima Hasil',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => '(1) Jika pembayaran baru DP (PARTIALLY_PAID), klik "Catat Pelunasan" lalu Role Finance klik "Verifikasi Pembayaran" di menu Pembayaran Masuk hingga status bayar PAID (LUNAS). (2) Setelah PAID, tombol di baris Order otomatis berubah menjadi hijau "Serah Terima Hasil". Klik tombol tersebut, masukkan link file final (GDrive/Figma/GitHub), lalu klik "Selesaikan & Serahkan Pesanan".',
            'next_role' => 'Selesai (Terekam di Dokumentasi & Laporan Bulanan)',
            'next_menu' => 'Sidebar: 3. Operations -> Tab: Dokumentasi & Sidebar: 4. Finance -> Tab: Laporan Bulanan',
            'next_explanation' => 'Status Order resmi COMPLETED! Seluruh siklus pesanan selesai dan otomatis masuk ke rekap Laporan Keuangan Bulanan.',
        ],
    ];

    $purchaseFlowSteps = [
        [
            'step' => 'P1',
            'phase' => 'Alur Pengeluaran Operasional',
            'title' => 'Ajukan Pembelian Kebutuhan Proyek / Operasional',
            'status_from' => 'FORM PENGAJUAN',
            'status_to' => 'PENDING',
            'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'roles' => ['ceo', 'finance', 'marketing', 'sales', 'web_developer', 'graphic_designer', 'ui_ux_designer', 'qc_admin', 'super_admin'],
            'role_badge' => 'Semua Anggota Tim Viscode',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Pengajuan Pembelian (Purchases)',
            'menu_url' => route('purchases.index'),
            'menu_btn_label' => 'Buka Pengajuan Pembelian',
            'action_button' => '+ Ajukan Pembelian',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Buka Sidebar: 4. Finance & Reports -> Tab: Pengajuan Pembelian. Klik tombol "+ Ajukan Pembelian" di kanan atas, isi nama barang/layanan (misal: Hosting/Domain/Aset), tujuan, dan estimasi biaya, lalu klik "Kirim Pengajuan".',
            'next_role' => 'CEO / Project Manager (Raihan Alvian)',
            'next_menu' => 'Tetap di Sidebar: 4. Finance & Reports -> Tab: Pengajuan Pembelian',
            'next_explanation' => 'Status pengajuan menjadi PENDING. Tugas berpindah ke Role CEO untuk menyetujui atau menolak anggaran tersebut.',
        ],
        [
            'step' => 'P2',
            'phase' => 'Alur Pengeluaran Operasional',
            'title' => 'Persetujuan Anggaran Pembelian oleh CEO',
            'status_from' => 'PENDING',
            'status_to' => 'APPROVED',
            'status_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'roles' => ['ceo', 'super_admin'],
            'role_badge' => 'CEO / Project Manager (Raihan Alvian)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Pengajuan Pembelian (Purchases)',
            'menu_url' => route('purchases.index'),
            'menu_btn_label' => 'Buka Pengajuan Pembelian',
            'action_button' => 'Setujui Pengajuan',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Role CEO membuka Tab: Pengajuan Pembelian, lalu pada baris berstatus PENDING mengklik tombol hijau "Setujui Pengajuan" dan memberikan catatan persetujuan.',
            'next_role' => 'Pemohon Pembelian / Finance Manager (Sutan Raja)',
            'next_menu' => 'Tetap di Tab: Pengajuan Pembelian (Purchases)',
            'next_explanation' => 'Status menjadi APPROVED. Tim dapat melakukan transaksi belanja dan mengumpulkan bukti struk/nota.',
        ],
        [
            'step' => 'P3',
            'phase' => 'Alur Pengeluaran Operasional',
            'title' => 'Catat Realisasi Belanja & Upload Bukti Nota',
            'status_from' => 'APPROVED',
            'status_to' => 'PURCHASED',
            'status_color' => 'bg-purple-50 text-purple-700 border-purple-200',
            'roles' => ['ceo', 'finance', 'marketing', 'sales', 'web_developer', 'graphic_designer', 'ui_ux_designer', 'qc_admin', 'super_admin'],
            'role_badge' => 'Pemohon Pembelian / Finance Manager (Sutan Raja)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Pengajuan Pembelian (Purchases)',
            'menu_url' => route('purchases.index'),
            'menu_btn_label' => 'Buka Pengajuan Pembelian',
            'action_button' => 'Catat Pembelian & Nota',
            'action_button_color' => 'bg-purple-600 text-white',
            'action_detail' => 'Pada baris pengajuan berstatus APPROVED, klik tombol ungu "Catat Pembelian & Nota". Masukkan nominal biaya aktual yang dibayarkan dan unggah foto struk/invoice, lalu klik "Simpan Realisasi Pembelian".',
            'next_role' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'next_menu' => 'Tetap di Tab: Pengajuan Pembelian (Purchases)',
            'next_explanation' => 'Status berubah menjadi PURCHASED. Selanjutnya Finance membukukan pengeluaran tersebut ke Buku Kas Usaha.',
        ],
        [
            'step' => 'P4',
            'phase' => 'Alur Pengeluaran Operasional',
            'title' => 'Bukukan Pengeluaran ke Buku Kas Usaha',
            'status_from' => 'PURCHASED',
            'status_to' => 'COMPLETED (TERCATAT DI BUKU KAS)',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['finance', 'ceo', 'super_admin'],
            'role_badge' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Pengajuan Pembelian -> Otomatis masuk Tab: Buku Kas & Transaksi',
            'menu_url' => route('purchases.index'),
            'menu_btn_label' => 'Buka Pengajuan Pembelian',
            'action_button' => 'Bukukan ke Kas',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Role Finance mengklik tombol hijau "Bukukan ke Kas" pada baris berstatus PURCHASED, lalu mengonfirmasi dialog persetujuan.',
            'next_role' => 'Selesai (Otomatis Masuk Buku Kas & Laporan Bulanan)',
            'next_menu' => 'Sidebar: 4. Finance & Reports -> Tab: Buku Kas & Transaksi',
            'next_explanation' => 'Status menjadi COMPLETED dan sistem otomatis mencatat transaksi pengeluaran (Expense) di Buku Kas.',
        ],
    ];

    $reportFlowSteps = [
        [
            'step' => 'R1',
            'phase' => 'Alur Laporan Bulanan',
            'title' => 'Generate / Kalkulasi Otomatis Draft Laporan Bulanan',
            'status_from' => 'REKAP KAS & ORDER',
            'status_to' => 'DRAFT',
            'status_color' => 'bg-slate-100 text-slate-700 border-slate-200',
            'roles' => ['finance', 'ceo', 'super_admin'],
            'role_badge' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Laporan Bulanan (Monthly Reports)',
            'menu_url' => $currentUser->canAccessModule('reports') ? route('reports.index') : null,
            'menu_btn_label' => 'Buka Laporan Bulanan',
            'action_button' => 'Generate / Perbarui Draft',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Buka Sidebar: 4. Finance & Reports -> Tab: Laporan Bulanan. Pilih periode Bulan & Tahun di bagian atas, lalu klik tombol "Generate / Perbarui Draft".',
            'next_role' => 'Finance Manager (Sutan Raja)',
            'next_menu' => 'Tetap di Tab: Laporan Bulanan',
            'next_explanation' => 'Sistem menghitung otomatis total pemasukan, pengeluaran, laba bersih, dan jumlah proyek dalam status DRAFT.',
        ],
        [
            'step' => 'R2',
            'phase' => 'Alur Laporan Bulanan',
            'title' => 'Ajukan Laporan Bulanan untuk Direview CEO',
            'status_from' => 'DRAFT',
            'status_to' => 'SUBMITTED',
            'status_color' => 'bg-amber-50 text-amber-700 border-amber-200',
            'roles' => ['finance', 'ceo', 'super_admin'],
            'role_badge' => 'Finance Manager (Sutan Raja)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Laporan Bulanan (Monthly Reports)',
            'menu_url' => $currentUser->canAccessModule('reports') ? route('reports.index') : null,
            'menu_btn_label' => 'Buka Laporan Bulanan',
            'action_button' => 'Ajukan Review CEO',
            'action_button_color' => 'bg-blue-600 text-white',
            'action_detail' => 'Setelah angka rekapitulasi diperiksa oleh Finance, klik tombol biru "Ajukan Review CEO" pada baris laporan berstatus DRAFT.',
            'next_role' => 'CEO / Project Manager (Raihan Alvian)',
            'next_menu' => 'Tetap di Tab: Laporan Bulanan',
            'next_explanation' => 'Status laporan berubah menjadi SUBMITTED dan menunggu pengesahan dari CEO.',
        ],
        [
            'step' => 'R3',
            'phase' => 'Alur Laporan Bulanan',
            'title' => 'Persetujuan Laporan Bulanan oleh CEO',
            'status_from' => 'SUBMITTED',
            'status_to' => 'APPROVED',
            'status_color' => 'bg-blue-50 text-blue-700 border-blue-200',
            'roles' => ['ceo', 'super_admin'],
            'role_badge' => 'CEO / Project Manager (Raihan Alvian)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Laporan Bulanan (Monthly Reports)',
            'menu_url' => $currentUser->canAccessModule('reports') ? route('reports.index') : null,
            'menu_btn_label' => 'Buka Laporan Bulanan',
            'action_button' => 'Setujui Laporan',
            'action_button_color' => 'bg-emerald-600 text-white',
            'action_detail' => 'Role CEO memeriksa laporan berstatus SUBMITTED lalu mengklik tombol hijau "Setujui Laporan".',
            'next_role' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'next_menu' => 'Tetap di Tab: Laporan Bulanan',
            'next_explanation' => 'Status laporan menjadi APPROVED dan siap dikunci sebagai arsip resmi bulanan.',
        ],
        [
            'step' => 'R4',
            'phase' => 'Alur Laporan Bulanan',
            'title' => 'Kunci Finalisasi & Cetak PDF Laporan Resmi',
            'status_from' => 'APPROVED',
            'status_to' => 'FINALIZED',
            'status_color' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'roles' => ['finance', 'ceo', 'super_admin'],
            'role_badge' => 'Finance Manager (Sutan Raja) / CEO (Raihan)',
            'menu_group' => 'Sidebar: 4. Finance & Reports',
            'menu_tab' => 'Tab: Laporan Bulanan (Monthly Reports)',
            'menu_url' => $currentUser->canAccessModule('reports') ? route('reports.index') : null,
            'menu_btn_label' => 'Buka Laporan Bulanan',
            'action_button' => 'Kunci Finalisasi & Export PDF',
            'action_button_color' => 'bg-slate-900 text-white',
            'action_detail' => 'Klik tombol "Kunci Finalisasi" pada baris laporan berstatus APPROVED untuk mengunci data permanen, dan klik "Export PDF" di kanan atas untuk mengunduh laporan resmi.',
            'next_role' => 'Selesai (Arsip Resmi Perusahaan)',
            'next_menu' => 'Tersimpan Permanen di Laporan Bulanan',
            'next_explanation' => 'Status laporan menjadi FINALIZED (Terkunci) dan tidak dapat diubah lagi demi integritas audit.',
        ],
    ];
@endphp

<div x-data="{
    activeTrackTab: 'main_flow',
    roleFilter: 'all'
}" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

    <!-- Top Banner Header -->
    <div class="bg-gradient-to-r from-[#17243B] via-[#1E2E4A] to-[#17243B] px-6 py-5 text-white border-b border-slate-800">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-cyan-500/15 border border-cyan-400/30 text-[#22D3EE] text-[11px] font-bold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#22D3EE] animate-pulse"></span>
                    Interactive System Navigation & Role Handover Tracker
                </div>
                <h3 class="text-lg sm:text-xl font-bold tracking-tight text-white flex items-center gap-2.5">
                    Panduan Tracking Alur Sistem: Klik Menu Apa &rarr; Klik Action Apa &rarr; Pindah ke Role Mana
                </h3>
                <p class="text-xs text-slate-300 mt-1 max-w-3xl leading-relaxed">
                    Berbeda dari ringkasan angka pipeline, panduan tracking ini menunjukkan secara persis <strong class="text-white">menu & tab mana yang harus dibuka</strong>, <strong class="text-[#22D3EE]">tombol action apa yang harus diklik</strong>, serta <strong class="text-emerald-300">ke role atau menu mana tugas berpindah selanjutnya</strong>.
                </p>
            </div>

            @if($compactHeader)
                <div class="shrink-0">
                    <a href="{{ route('workflow-guide.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#2563EB] hover:bg-blue-500 text-white text-xs font-bold shadow-sm transition">
                        <span>Buka Halaman Penuh Alur Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Sub-Navigation Tabs (Jenis Alur) & Role Filter -->
        <div class="mt-5 pt-4 border-t border-white/10 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
            <!-- Pilihan Alur -->
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button"
                        @click="activeTrackTab = 'main_flow'"
                        :class="activeTrackTab === 'main_flow' ? 'bg-[#2563EB] text-white border-blue-400 shadow-sm' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10 hover:text-white'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border transition cursor-pointer flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-white/15 flex items-center justify-center text-[11px]">1</span>
                    <span>Alur Utama: Lead &rarr; Penawaran &rarr; Order &rarr; Proyek &rarr; Lunas (10 Tahap)</span>
                </button>

                <button type="button"
                        @click="activeTrackTab = 'purchase_flow'"
                        :class="activeTrackTab === 'purchase_flow' ? 'bg-[#2563EB] text-white border-blue-400 shadow-sm' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10 hover:text-white'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border transition cursor-pointer flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-white/15 flex items-center justify-center text-[11px]">2</span>
                    <span>Alur Pengajuan Pembelian & Kas Keluar (4 Tahap)</span>
                </button>

                <button type="button"
                        @click="activeTrackTab = 'report_flow'"
                        :class="activeTrackTab === 'report_flow' ? 'bg-[#2563EB] text-white border-blue-400 shadow-sm' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10 hover:text-white'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border transition cursor-pointer flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg bg-white/15 flex items-center justify-center text-[11px]">3</span>
                    <span>Alur Pengesahan Laporan Bulanan (4 Tahap)</span>
                </button>

                <button type="button"
                        @click="activeTrackTab = 'role_map'"
                        :class="activeTrackTab === 'role_map' ? 'bg-[#2563EB] text-white border-blue-400 shadow-sm' : 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10 hover:text-white'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold border transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#22D3EE]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Peta Role Tim & Hak Akses Menu</span>
                </button>
            </div>

            <!-- Filter Role Interaktif -->
            <div x-show="activeTrackTab !== 'role_map'" class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl px-3 py-1.5 self-start xl:self-auto min-w-[290px]">
                <span class="text-[11px] font-semibold text-slate-300 whitespace-nowrap">Filter Tahapan Role:</span>
                <select x-model="roleFilter" class="vc-select2-dark bg-[#17243B] text-white text-xs font-semibold border border-white/20 rounded-lg px-2.5 py-1 focus:border-cyan-400 focus:ring-0 min-h-[34px]">
                    <option value="all">Semua Role (Tampilkan Utuh)</option>
                    <option value="{{ $currentRole }}">✨ Hanya Tugas Role Saya ({{ $currentUser->role_label }})</option>
                    <option value="sales">Sales & Customer Relations (Yoga K)</option>
                    <option value="marketing">Marketing Manager (Feni)</option>
                    <option value="finance">Finance Manager (Sutan Raja)</option>
                    <option value="tech">Tim Teknis (Desainer / UI-UX / Web Dev)</option>
                    <option value="qc_admin">Quality Control & Admin (Morren)</option>
                    <option value="ceo">CEO / Project Manager (Raihan Alvian)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Legend / Petunjuk Membaca Kartu Tracking -->
    <div x-show="activeTrackTab !== 'role_map'" class="bg-slate-50 border-b border-slate-200 px-6 py-3 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex flex-wrap items-center gap-4 text-slate-600">
            <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Cara Membaca Tracking:</span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <strong>Kolom 1 (Buka Menu Apa):</strong> Lokasi Sidebar & Tab yang harus dibuka
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <strong>Kolom 2 (Klik Action Apa):</strong> Nama tombol persis di tabel & form yang diisi
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                <strong>Kolom 3 (Pindah ke Role/Menu Mana):</strong> Status baru & siapa yang melanjutkan
            </span>
        </div>
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 font-semibold text-[11px]">
            <span>Login Anda saat ini:</span>
            <strong class="underline">{{ $currentUser->name }} ({{ $currentUser->role_label }})</strong>
        </div>
    </div>

    <div class="p-6">
        <!-- ============================================================= -->
        <!-- TAB 1: ALUR UTAMA (10 LANGKAH TRACKING DETAIL)                -->
        <!-- ============================================================= -->
        <div x-show="activeTrackTab === 'main_flow'" class="space-y-6">
            <!-- Quick Visual Breadcrumb Bar (1 -> 10) -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3">
                    Peta Jejak Tracking 10 Tahap (Dari Klien Masuk Hingga Proyek Selesai & Lunas):
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2">
                    @foreach($mainFlowSteps as $stepItem)
                        <a href="#track-step-{{ $stepItem['step'] }}"
                           class="p-2 rounded-xl border border-slate-200 bg-white hover:border-blue-400 hover:bg-blue-50/40 transition flex flex-col justify-between group">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[10px] font-mono font-black px-1.5 py-0.5 rounded bg-slate-900 text-white group-hover:bg-blue-600 transition">
                                    #{{ $stepItem['step'] }}
                                </span>
                                @if(in_array($currentRole, $stepItem['roles'], true))
                                    <span class="w-2 h-2 rounded-full bg-blue-600" title="Melibatkan Role Anda"></span>
                                @endif
                            </div>
                            <div class="text-[11px] font-bold text-slate-800 mt-1.5 line-clamp-2 leading-tight">
                                {{ $stepItem['title'] }}
                            </div>
                            <div class="text-[9px] font-mono text-blue-600 font-bold mt-1 truncate">
                                &rarr; {{ $stepItem['status_to'] }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Vertical Tracking Steps -->
            <div class="relative pl-4 sm:pl-8 border-l-2 border-blue-200 space-y-6">
                @foreach($mainFlowSteps as $item)
                    @php
                        $isMyRole = in_array($currentRole, $item['roles'], true);
                        $rolesJson = json_encode($item['roles']);
                        $isTechStep = in_array('web_developer', $item['roles'], true);
                    @endphp
                    <div id="track-step-{{ $item['step'] }}"
                         x-show="roleFilter === 'all' || ({{ $rolesJson }}).includes(roleFilter) || (roleFilter === 'tech' && {{ $isTechStep ? 'true' : 'false' }})"
                         class="relative bg-white rounded-2xl border {{ $isMyRole ? 'border-blue-300 ring-1 ring-blue-500/10' : 'border-slate-200' }} p-5 shadow-2xs hover:shadow-md transition-all">

                        <!-- Timeline Circle Node -->
                        <div class="absolute -left-[29px] sm:-left-[45px] top-5 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center font-mono text-xs font-black shadow-sm border-2 border-white {{ $isMyRole ? 'bg-blue-600 text-white' : 'bg-slate-800 text-white' }}">
                            {{ $item['step'] }}
                        </div>

                        <!-- Step Header -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                        {{ $item['phase'] }}
                                    </span>
                                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border {{ $item['status_color'] }}">
                                        {{ $item['status_from'] }} &rarr; {{ $item['status_to'] }}
                                    </span>
                                    @if($isMyRole)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white">
                                            ✨ Tugas Role Anda ({{ $currentUser->role_label }})
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-base font-bold text-slate-900 mt-1.5">
                                    Tahap {{ $item['step'] }}: {{ $item['title'] }}
                                </h4>
                            </div>

                            <div class="text-left md:text-right shrink-0">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Siapa yang Menjalankan (Role):</div>
                                <div class="text-xs font-bold text-slate-800 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 mt-0.5 inline-block">
                                    👤 {{ $item['role_badge'] }}
                                </div>
                            </div>
                        </div>

                        <!-- 3 Tracking Columns: (1) Buka Menu Apa -> (2) Klik Action Apa -> (3) Pindah ke Role/Menu Mana -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

                            <!-- BOX 1: BUKA MENU & TAB APA? -->
                            <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                            1. Pergi ke Menu Mana?
                                        </span>
                                        <span class="text-[10px] font-mono font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">LOKASI MENU</span>
                                    </div>
                                    <div class="text-xs font-bold text-slate-900">{{ $item['menu_group'] }}</div>
                                    <div class="text-xs text-blue-700 font-semibold mt-1 bg-white px-2.5 py-1.5 rounded-lg border border-blue-100">
                                        📂 {{ $item['menu_tab'] }}
                                    </div>
                                </div>

                                @if($item['menu_url'])
                                    <div class="mt-3 pt-2.5 border-t border-blue-200/60 flex justify-end">
                                        <a href="{{ $item['menu_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-2xs transition">
                                            <span>{{ $item['menu_btn_label'] }}</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-3 pt-2.5 border-t border-blue-200/60 text-[11px] text-slate-500 italic">
                                        Menu ini khusus untuk role terkait ({{ $item['role_badge'] }}).
                                    </div>
                                @endif
                            </div>

                            <!-- BOX 2: KLIK TOMBOL ACTION APA? -->
                            <div class="p-4 rounded-xl bg-amber-50/40 border border-amber-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                                            2. Klik Tombol Action Apa?
                                        </span>
                                        <span class="text-[10px] font-mono font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">TOMBOL AKSI</span>
                                    </div>

                                    <!-- Visual Button Preview -->
                                    <div class="mb-2.5">
                                        <span class="text-[10px] text-slate-500 block mb-1">Cari & klik tombol ini pada halaman:</span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold shadow-2xs {{ $item['action_button_color'] }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            {{ $item['action_button'] }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-700 leading-relaxed">
                                        {{ $item['action_detail'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- BOX 3: PINDAH KE ROLE / MENU MANA SELANJUTNYA? -->
                            <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                                            3. Pindah ke Role / Menu Mana?
                                        </span>
                                        <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">ESTAFET TUGAS</span>
                                    </div>

                                    <div class="space-y-1.5 mb-2.5">
                                        <div class="bg-white p-2 rounded-lg border border-emerald-200/80">
                                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Diteruskan ke Role:</span>
                                            <span class="text-xs font-bold text-emerald-800">👉 {{ $item['next_role'] }}</span>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg border border-emerald-200/80">
                                            <span class="text-[10px] font-bold uppercase text-slate-400 block">Pantau di Menu Selanjutnya:</span>
                                            <span class="text-xs font-semibold text-slate-800">📍 {{ $item['next_menu'] }}</span>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-600 leading-relaxed">
                                        {{ $item['next_explanation'] }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 2: ALUR PENGAJUAN PEMBELIAN & PENGELUARAN (4 TAHAP)       -->
        <!-- ============================================================= -->
        <div x-show="activeTrackTab === 'purchase_flow'" class="space-y-6" style="display: none;">
            <div class="p-4 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-900 leading-relaxed">
                <strong>💡 Kapan Alur Ini Digunakan?</strong> Setiap kali anggota tim membutuhkan pembelian barang/layanan operasional (seperti beli Domain, Sewa Hosting/VPS, Lisensi Font/Plugin, atau Cetak ID Card/Poster), gunakan menu <strong>Sidebar: 4. Finance & Reports &rarr; Tab: Pengajuan Pembelian</strong> dengan urutan 4 langkah berikut:
            </div>

            <div class="relative pl-4 sm:pl-8 border-l-2 border-purple-200 space-y-6">
                @foreach($purchaseFlowSteps as $item)
                    @php
                        $isMyRole = in_array($currentRole, $item['roles'], true);
                    @endphp
                    <div class="relative bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs">
                        <div class="absolute -left-[29px] sm:-left-[45px] top-5 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-mono text-xs font-black shadow-sm border-2 border-white">
                            {{ $item['step'] }}
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border {{ $item['status_color'] }}">
                                        {{ $item['status_from'] }} &rarr; {{ $item['status_to'] }}
                                    </span>
                                    @if($isMyRole)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-600 text-white">
                                            ✨ Dapat Dilakukan Role Anda
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-base font-bold text-slate-900 mt-1.5">{{ $item['title'] }}</h4>
                            </div>
                            <div class="text-xs font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                👤 Role: {{ $item['role_badge'] }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                            <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-blue-800 mb-1.5">1. Pergi ke Menu Mana?</div>
                                    <div class="text-xs font-bold text-slate-900">{{ $item['menu_group'] }}</div>
                                    <div class="text-xs text-blue-700 font-semibold mt-1 bg-white px-2.5 py-1.5 rounded-lg border border-blue-100">📂 {{ $item['menu_tab'] }}</div>
                                </div>
                                @if($item['menu_url'])
                                    <div class="mt-3 pt-2 border-t border-blue-200/60 flex justify-end">
                                        <a href="{{ $item['menu_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">
                                            <span>{{ $item['menu_btn_label'] }}</span> &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 rounded-xl bg-amber-50/40 border border-amber-200/80">
                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-900 mb-2">2. Klik Tombol Action Apa?</div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold mb-2 {{ $item['action_button_color'] }}">
                                    {{ $item['action_button'] }}
                                </span>
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $item['action_detail'] }}</p>
                            </div>

                            <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/80">
                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-2">3. Pindah ke Role / Menu Mana?</div>
                                <div class="text-xs font-bold text-emerald-800 bg-white p-2 rounded-lg border border-emerald-200 mb-1.5">👉 Ke Role: {{ $item['next_role'] }}</div>
                                <div class="text-xs font-semibold text-slate-800 bg-white p-2 rounded-lg border border-emerald-200 mb-2">📍 Menu: {{ $item['next_menu'] }}</div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $item['next_explanation'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 3: ALUR LAPORAN KEUANGAN BULANAN (4 TAHAP)                -->
        <!-- ============================================================= -->
        <div x-show="activeTrackTab === 'report_flow'" class="space-y-6" style="display: none;">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 leading-relaxed">
                <strong>📊 Alur Tutup Buku & Pengesahan Laporan Bulanan:</strong> Digunakan oleh <strong>Finance Manager (Sutan Raja)</strong> dan <strong>CEO (Raihan Alvian)</strong> setiap akhir bulan pada menu <strong>Sidebar: 4. Finance & Reports &rarr; Tab: Laporan Bulanan</strong>.
            </div>

            <div class="relative pl-4 sm:pl-8 border-l-2 border-emerald-200 space-y-6">
                @foreach($reportFlowSteps as $item)
                    <div class="relative bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs">
                        <div class="absolute -left-[29px] sm:-left-[45px] top-5 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-mono text-xs font-black shadow-sm border-2 border-white">
                            {{ $item['step'] }}
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full border {{ $item['status_color'] }}">
                                    {{ $item['status_from'] }} &rarr; {{ $item['status_to'] }}
                                </span>
                                <h4 class="text-base font-bold text-slate-900 mt-1.5">{{ $item['title'] }}</h4>
                            </div>
                            <div class="text-xs font-bold text-slate-800 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                👤 Role: {{ $item['role_badge'] }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
                            <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-200/80 flex flex-col justify-between">
                                <div>
                                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-blue-800 mb-1.5">1. Pergi ke Menu Mana?</div>
                                    <div class="text-xs font-bold text-slate-900">{{ $item['menu_group'] }}</div>
                                    <div class="text-xs text-blue-700 font-semibold mt-1 bg-white px-2.5 py-1.5 rounded-lg border border-blue-100">📂 {{ $item['menu_tab'] }}</div>
                                </div>
                                @if($item['menu_url'])
                                    <div class="mt-3 pt-2 border-t border-blue-200/60 flex justify-end">
                                        <a href="{{ $item['menu_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold">
                                            <span>{{ $item['menu_btn_label'] }}</span> &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 rounded-xl bg-amber-50/40 border border-amber-200/80">
                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-amber-900 mb-2">2. Klik Tombol Action Apa?</div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold mb-2 {{ $item['action_button_color'] }}">
                                    {{ $item['action_button'] }}
                                </span>
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $item['action_detail'] }}</p>
                            </div>

                            <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-200/80">
                                <div class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-2">3. Pindah ke Role / Menu Mana?</div>
                                <div class="text-xs font-bold text-emerald-800 bg-white p-2 rounded-lg border border-emerald-200 mb-1.5">👉 Ke Role: {{ $item['next_role'] }}</div>
                                <div class="text-xs font-semibold text-slate-800 bg-white p-2 rounded-lg border border-emerald-200 mb-2">📍 Menu: {{ $item['next_menu'] }}</div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $item['next_explanation'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 4: PETA ROLE TIM, AKUN LOGIN & HAK AKSES MENU             -->
        <!-- ============================================================= -->
        <div x-show="activeTrackTab === 'role_map'" class="space-y-5" style="display: none;">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed">
                Berikut adalah daftar lengkap <strong>8 Role Anggota Tim Viscode Creative</strong> beserta menu Sidebar yang bisa diakses dan tugas utamanya di dalam alur sistem:
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-4">Anggota Tim & Email Login</th>
                            <th class="py-3.5 px-4">Role Sistem</th>
                            <th class="py-3.5 px-4">Menu Sidebar yang Diakses</th>
                            <th class="py-3.5 px-4">Tombol Action Utama yang Menjadi Tanggung Jawabnya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Yoga K
                                <div class="font-mono text-[11px] text-blue-600 font-normal">yoga@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold">Sales & Customer Relations</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                1. Dashboard<br>
                                2. Sales & Orders (Semua Tab)<br>
                                3. Operations (Pantau Proyek)<br>
                                4. Finance (Pengajuan Pembelian)
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[Review Lead]</strong> & <strong>[Catat Follow-up]</strong> di Tab <em>Sales Pipeline</em></div>
                                <div>&bull; Klik <strong>[Buat Penawaran]</strong>, <strong>[Tandai Disetujui]</strong>, & <strong>[Konversi ke Order]</strong></div>
                                <div>&bull; Klik <strong>[Catat Pembayaran]</strong> & <strong>[Serah Terima Hasil]</strong> di Tab <em>Sales & Orders</em></div>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Feni Dwi Lestari
                                <div class="font-mono text-[11px] text-blue-600 font-normal">feni@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold">Marketing Manager</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                1. Dashboard<br>
                                2. Sales & Orders (Tab Sales Pipeline, Klien, Penawaran)<br>
                                4. Finance (Pengajuan Pembelian)
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[+ Intake Klien / Lead]</strong> dari kampanye promosi / media sosial</div>
                                <div>&bull; Klik <strong>[Review Lead]</strong> & <strong>[Catat Follow-up]</strong> di Tab <em>Sales Pipeline</em></div>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Sutan Raja
                                <div class="font-mono text-[11px] text-blue-600 font-normal">sutan@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">Finance Manager</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                1. Dashboard<br>
                                2. Sales & Orders (Pantau Tagihan Order)<br>
                                4. Finance & Reports (Semua Tab: Pembayaran, Pembelian, Buku Kas, Laporan)
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[Verifikasi Pembayaran]</strong> di Tab <em>Pembayaran Masuk</em> (otomatis memicu pembuatan Proyek!)</div>
                                <div>&bull; Klik <strong>[Bukukan ke Kas]</strong> di Tab <em>Pengajuan Pembelian</em></div>
                                <div>&bull; Klik <strong>[Generate Draft]</strong> & <strong>[Ajukan Review CEO]</strong> di Tab <em>Laporan Bulanan</em></div>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Ahmad Naufal Akbar &amp; M. Fikri AlFauzan
                                <div class="font-mono text-[11px] text-blue-600 font-normal">naufal@viscode.id / fikri@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 font-bold">Tim Teknis (Graphic / UI-UX / Web Dev)</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                1. Dashboard<br>
                                3. Operations (Tab Proyek & Tugas, Dokumentasi)<br>
                                4. Finance (Pengajuan Pembelian)
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[Mulai Proyek]</strong> pada proyek berstatus <code>QUEUED</code> di menu <em>Operations</em></div>
                                <div>&bull; Klik <strong>[Kerjakan]</strong> & <strong>[Kirim QC]</strong> pada daftar tugas teknis</div>
                                <div>&bull; Klik <strong>[Ajukan QC]</strong> pada proyek yang siap diperiksa Quality Control</div>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Morren
                                <div class="font-mono text-[11px] text-blue-600 font-normal">morren@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-bold">Quality Control &amp; Admin</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                1. Dashboard<br>
                                2. Sales & Orders (Serah Terima Order)<br>
                                3. Operations (Review QC Proyek & Tugas, Dokumentasi)
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[Approve]</strong> atau <strong>[Revisi]</strong> pada tugas tim teknis di menu <em>Operations</em></div>
                                <div>&bull; Klik <strong>[Setujui QC (Selesai)]</strong> pada kartu proyek berstatus <code>REVIEW</code></div>
                                <div>&bull; Klik <strong>[Serah Terima Hasil]</strong> pada Order yang sudah <code>READY_FOR_DELIVERY</code> &amp; <code>PAID</code></div>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Raihan Alvian &amp; Ziddan Azzahra
                                <div class="font-mono text-[11px] text-blue-600 font-normal">raihan@viscode.id / ziddan@viscode.id</div>
                            </td>
                            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold">CEO / Project Manager &amp; Super Admin</span></td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                Seluruh Menu Sidebar (1 s/d 5) + Audit Log &amp; System
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 space-y-1">
                                <div>&bull; Klik <strong>[Setujui Pengajuan]</strong> di Tab <em>Pengajuan Pembelian</em></div>
                                <div>&bull; Klik <strong>[Setujui Laporan]</strong> &amp; <strong>[Kunci Finalisasi]</strong> di Tab <em>Laporan Bulanan</em></div>
                                <div>&bull; Supervisi seluruh proyek, pembagian tugas tim, serta persetujuan eksekutif</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
