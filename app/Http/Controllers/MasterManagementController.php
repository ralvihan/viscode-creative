<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MasterManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeMasterAccess();

        $users = User::orderBy('name')->get();
        $roleDefinitions = User::roleDefinitions();
        $roleUserCounts = $users->groupBy('role')->map->count();

        $services = Service::withCount(['orders', 'quotations', 'leads'])
            ->orderByDesc('is_active')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $customers = Customer::withCount(['orders', 'quotations', 'leads'])
            ->latest()
            ->get();

        $referenceData = [
            'service_categories' => [
                ['code' => 'web_development', 'label' => 'Web Development', 'description' => 'Landing page, website profil UMKM, hingga aplikasi web custom.'],
                ['code' => 'graphic_design', 'label' => 'Graphic Design', 'description' => 'Poster, flyer, konten media sosial, dan identitas visual brand.'],
                ['code' => 'id_card_event', 'label' => 'ID Card & Event', 'description' => 'Desain ID card panitia, lanyard, sertifikat, dan atribut acara.'],
                ['code' => 'bundling', 'label' => 'Paket Bundling', 'description' => 'Paket gabungan website, desain grafis, dan kebutuhan event.'],
            ],
            'customer_types' => [
                ['code' => 'umkm', 'label' => 'UMKM / Usaha Lokal', 'description' => 'Pelaku usaha mikro, kecil, menengah, toko, dan kuliner lokal.'],
                ['code' => 'organisasi', 'label' => 'Organisasi Kampus / Sekolah', 'description' => 'Himpunan, BEM, UKM, OSIS, dan panitia kegiatan pendidikan.'],
                ['code' => 'mahasiswa', 'label' => 'Mahasiswa / Komunitas', 'description' => 'Kebutuhan personal mahasiswa, proyek akademik, atau komunitas.'],
                ['code' => 'event_organizer', 'label' => 'Event Organizer', 'description' => 'Penyelenggara seminar, festival, konser, dan perlombaan.'],
                ['code' => 'individu', 'label' => 'Individu / Personal', 'description' => 'Klien perorangan seperti portofolio pribadi atau undangan digital.'],
                ['code' => 'lainnya', 'label' => 'Lainnya', 'description' => 'Segmen klien umum di luar kategori utama.'],
            ],
            'lead_sources' => [
                ['code' => 'website', 'label' => 'Website Publik Viscode', 'description' => 'Masuk otomatis melalui formulir konsultasi halaman depan.'],
                ['code' => 'whatsapp', 'label' => 'WhatsApp Bisnis', 'description' => 'Calon klien menghubungi langsung melalui chat WhatsApp resmi.'],
                ['code' => 'instagram', 'label' => 'Instagram / Sosial Media', 'description' => 'Prospek yang berasal dari DM, konten feed, atau iklan media sosial.'],
                ['code' => 'referral', 'label' => 'Rekomendasi / Referral', 'description' => 'Rekomendasi dari klien terdahulu, dosen, atau relasi tim.'],
                ['code' => 'direct', 'label' => 'Pertemuan Langsung', 'description' => 'Prospek dari presentasi langsung, pameran, atau networking.'],
                ['code' => 'lainnya', 'label' => 'Lainnya', 'description' => 'Sumber prospek alternatif lainnya.'],
            ],
            'payment_methods' => [
                ['code' => 'Transfer Bank / QRIS', 'label' => 'Transfer Bank / QRIS', 'description' => 'Metode utama pembayaran DP maupun pelunasan tagihan klien.'],
                ['code' => 'Transfer BCA', 'label' => 'Transfer Bank BCA', 'description' => 'Pembayaran langsung ke rekening kas operasional BCA.'],
                ['code' => 'Transfer Mandiri', 'label' => 'Transfer Bank Mandiri', 'description' => 'Pembayaran langsung ke rekening kas operasional Mandiri.'],
                ['code' => 'QRIS Viscode', 'label' => 'QRIS Viscode Creative', 'description' => 'Pembayaran instan melalui QRIS resmi Viscode Creative.'],
                ['code' => 'Tunai / Cash', 'label' => 'Tunai / Cash', 'description' => 'Pembayaran tunai langsung dengan bukti kwitansi fisik/digital.'],
            ],
            'project_task_templates' => [
                ['category' => 'Web Development', 'tasks' => ['Analisis Brief & Wireframe', 'Desain UI/UX & Prototype', 'Development Frontend & Backend', 'Testing Responsif & Persiapan QC']],
                ['category' => 'Graphic Design', 'tasks' => ['Analisis Brief & Referensi Visual', 'Pembuatan Konsep Desain Awal', 'Finalisasi Aset High-Res & Format Cetak/Digital']],
                ['category' => 'ID Card & Event', 'tasks' => ['Pengumpulan Data & Layout Template', 'Desain Final & Penyesuaian Ukuran Cetak', 'Quality Check & Export File Siap Cetak']],
                ['category' => 'Paket Bundling', 'tasks' => ['Kickoff Brief & Struktur Paket', 'Produksi Aset Desain & Konten', 'Pengembangan Website / Landing Page', 'Integrasi & Final Quality Control']],
            ],
        ];

        $activeTab = $request->get('tab', 'users');

        return view('master.index', compact(
            'users',
            'roleDefinitions',
            'roleUserCounts',
            'services',
            'customers',
            'referenceData',
            'activeTab'
        ));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::availableRoles())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validated['role'] === User::ROLE_SUPER_ADMIN && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Admin yang dapat membuat akun dengan role Super Admin.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        ActivityLogger::log(
            'MasterUsers',
            (string) $user->id,
            'CREATE',
            null,
            ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
            'Menambahkan akun pengguna baru: '.$user->name.' ('.$user->role_label.')'
        );

        return redirect()->route('master.index', ['tab' => 'users'])
            ->with('success', 'Akun pengguna '.$user->name.' berhasil ditambahkan dengan role '.$user->role_label.'.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(User::availableRoles())],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (($user->isSuperAdmin() || $validated['role'] === User::ROLE_SUPER_ADMIN) && ! auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Hanya Super Admin yang dapat mengubah akun atau menetapkan role Super Admin.');
        }

        if ($user->isSuperAdmin() && $validated['role'] !== User::ROLE_SUPER_ADMIN) {
            $superAdminCount = User::where('role', User::ROLE_SUPER_ADMIN)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role Super Admin terakhir di sistem.');
            }
        }

        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        ActivityLogger::log(
            'MasterUsers',
            (string) $user->id,
            'UPDATE',
            $old,
            ['name' => $user->name, 'email' => $user->email, 'role' => $user->role],
            'Memperbarui data pengguna: '.$user->name.(! empty($validated['password']) ? ' (termasuk reset password)' : '')
        );

        return redirect()->route('master.index', ['tab' => 'users'])
            ->with('success', 'Data pengguna '.$user->name.' berhasil diperbarui.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $this->authorizeMasterAccess();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan login.');
        }

        if ($user->isSuperAdmin()) {
            if (! auth()->user()->isSuperAdmin()) {
                return back()->with('error', 'Hanya Super Admin yang dapat menghapus akun Super Admin.');
            }

            if (User::where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
                return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir di sistem.');
            }
        }

        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];
        $userName = $user->name;
        $user->delete();

        ActivityLogger::log(
            'MasterUsers',
            (string) $user->id,
            'DELETE',
            $old,
            null,
            'Menghapus akun pengguna: '.$userName
        );

        return redirect()->route('master.index', ['tab' => 'users'])
            ->with('success', 'Akun pengguna '.$userName.' berhasil dihapus.');
    }

    public function storeService(Request $request): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['web_development', 'graphic_design', 'id_card_event', 'bundling'])],
            'base_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $service = Service::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'base_price' => $validated['base_price'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log(
            'MasterServices',
            (string) $service->id,
            'CREATE',
            null,
            $service->toArray(),
            'Menambahkan master layanan/paket baru: '.$service->name
        );

        return redirect()->route('master.index', ['tab' => 'services'])
            ->with('success', 'Master layanan "'.$service->name.'" berhasil ditambahkan!');
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['web_development', 'graphic_design', 'id_card_event', 'bundling'])],
            'base_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $old = $service->toArray();

        $service->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'base_price' => $validated['base_price'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', false),
        ]);

        ActivityLogger::log(
            'MasterServices',
            (string) $service->id,
            'UPDATE',
            $old,
            $service->toArray(),
            'Memperbarui master layanan/paket: '.$service->name
        );

        return redirect()->route('master.index', ['tab' => 'services'])
            ->with('success', 'Master layanan "'.$service->name.'" berhasil diperbarui!');
    }

    public function toggleService(Service $service): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $old = ['is_active' => $service->is_active];
        $service->update(['is_active' => ! $service->is_active]);

        $statusLabel = $service->is_active ? 'AKTIF' : 'NONAKTIF';

        ActivityLogger::log(
            'MasterServices',
            (string) $service->id,
            'TOGGLE_STATUS',
            $old,
            ['is_active' => $service->is_active],
            'Mengubah status layanan "'.$service->name.'" menjadi '.$statusLabel
        );

        return redirect()->route('master.index', ['tab' => 'services'])
            ->with('success', 'Status layanan "'.$service->name.'" berhasil diubah menjadi '.$statusLabel.'.');
    }

    public function destroyService(Service $service): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $usageCount = $service->orders()->count() + $service->quotations()->count() + $service->leads()->count();
        if ($usageCount > 0) {
            return redirect()->route('master.index', ['tab' => 'services'])
                ->with('error', 'Layanan "'.$service->name.'" sedang digunakan oleh '.$usageCount.' data transaksi/prospek. Silakan nonaktifkan saja agar riwayat transaksi tetap terjaga.');
        }

        $old = $service->toArray();
        $serviceName = $service->name;
        $service->delete();

        ActivityLogger::log(
            'MasterServices',
            (string) $service->id,
            'DELETE',
            $old,
            null,
            'Menghapus master layanan: '.$serviceName
        );

        return redirect()->route('master.index', ['tab' => 'services'])
            ->with('success', 'Master layanan "'.$serviceName.'" berhasil dihapus.');
    }

    public function destroyCustomer(Customer $customer): RedirectResponse
    {
        $this->authorizeMasterAccess();

        $usageCount = $customer->orders()->count() + $customer->quotations()->count() + $customer->leads()->count();
        if ($usageCount > 0) {
            return redirect()->route('master.index', ['tab' => 'customers'])
                ->with('error', 'Pelanggan "'.$customer->name.'" memiliki '.$usageCount.' riwayat prospek/penawaran/pesanan sehingga tidak dapat dihapus permanen.');
        }

        $old = $customer->toArray();
        $customerName = $customer->name;
        $customer->delete();

        ActivityLogger::log(
            'MasterCustomers',
            (string) $customer->id,
            'DELETE',
            $old,
            null,
            'Menghapus master pelanggan: '.$customerName
        );

        return redirect()->route('master.index', ['tab' => 'customers'])
            ->with('success', 'Data pelanggan "'.$customerName.'" berhasil dihapus.');
    }

    private function authorizeMasterAccess(): void
    {
        abort_if(
            ! auth()->user()->canAccessModule('master_data'),
            403,
            'Hanya Super Admin dan CEO yang memiliki hak akses ke Master & User Management.'
        );
    }
}
