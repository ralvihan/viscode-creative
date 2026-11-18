<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Role definitions
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_CEO = 'ceo';

    public const ROLE_FINANCE = 'finance';

    public const ROLE_MARKETING = 'marketing';

    public const ROLE_WEB_DEVELOPER = 'web_developer';

    public const ROLE_GRAPHIC_DESIGNER = 'graphic_designer';

    public const ROLE_UI_UX_DESIGNER = 'ui_ux_designer';

    public const ROLE_SALES = 'sales';

    public const ROLE_QC_ADMIN = 'qc_admin';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isCeo(): bool
    {
        return $this->role === self::ROLE_CEO;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    public function isSales(): bool
    {
        return $this->role === self::ROLE_SALES;
    }

    public function isMarketing(): bool
    {
        return $this->role === self::ROLE_MARKETING;
    }

    public function isQc(): bool
    {
        return $this->role === self::ROLE_QC_ADMIN;
    }

    public function isWebDeveloper(): bool
    {
        return $this->role === self::ROLE_WEB_DEVELOPER;
    }

    public function isGraphicDesigner(): bool
    {
        return $this->role === self::ROLE_GRAPHIC_DESIGNER;
    }

    public function isUiUxDesigner(): bool
    {
        return $this->role === self::ROLE_UI_UX_DESIGNER;
    }

    public function isTechnicalTeam(): bool
    {
        return in_array($this->role, [
            self::ROLE_WEB_DEVELOPER,
            self::ROLE_GRAPHIC_DESIGNER,
            self::ROLE_UI_UX_DESIGNER,
        ], true);
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $roles = (array) $roles;

        return in_array($this->role, $roles, true);
    }

    /**
     * Check module access permission
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->isSuperAdmin() || $this->isCeo()) {
            return true;
        }

        return match ($module) {
            'customers', 'leads', 'quotations' => in_array($this->role, [self::ROLE_SALES, self::ROLE_MARKETING], true),
            'orders' => in_array($this->role, [self::ROLE_SALES, self::ROLE_FINANCE, self::ROLE_QC_ADMIN], true),
            'payments', 'financials' => $this->role === self::ROLE_FINANCE,
            'purchases' => true, // All team members can create purchase requests; approval is CEO
            'projects', 'tasks' => in_array($this->role, [
                self::ROLE_WEB_DEVELOPER,
                self::ROLE_GRAPHIC_DESIGNER,
                self::ROLE_UI_UX_DESIGNER,
                self::ROLE_QC_ADMIN,
                self::ROLE_SALES,
            ], true),
            'documentation' => true,
            'reports' => $this->role === self::ROLE_FINANCE,
            'activity_logs', 'users', 'master_data', 'services', 'roles' => false,
            default => false,
        };
    }

    /**
     * All valid system role keys.
     *
     * @return list<string>
     */
    public static function availableRoles(): array
    {
        return array_keys(self::roleDefinitions());
    }

    /**
     * Master RBAC definitions for every role in Viscode BMS.
     *
     * @return array<string, array{label: string, badge: string, level: string, description: string, modules: list<string>}>
     */
    public static function roleDefinitions(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => [
                'label' => 'Super Admin',
                'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
                'level' => 'System Owner',
                'description' => 'Akses penuh seluruh modul operasional, Master & User Management, Status Override, dan Audit Log sistem.',
                'modules' => ['Semua Modul Operasional', 'Master & User Management', 'Status Override', 'Audit Log & System'],
            ],
            self::ROLE_CEO => [
                'label' => 'CEO / Project Manager',
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200',
                'level' => 'Executive',
                'description' => 'Akses penuh supervisi bisnis, persetujuan pengajuan pembelian, pengesahan laporan bulanan, manajemen proyek, dan Master Data.',
                'modules' => ['Dashboard Executive', 'Sales & Orders', 'Work Management', 'Finance & Approval', 'Master & User Management', 'Audit Log'],
            ],
            self::ROLE_FINANCE => [
                'label' => 'Finance Manager',
                'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'level' => 'Department Lead',
                'description' => 'Mengelola verifikasi pembayaran masuk, pencatatan buku kas, pembukuan belanja operasional, dan penyusunan laporan bulanan.',
                'modules' => ['Dashboard', 'Orders (Tagihan)', 'Payments Verification', 'Purchases & Cashbook', 'Monthly Reports'],
            ],
            self::ROLE_SALES => [
                'label' => 'Sales & Customer Relations',
                'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
                'level' => 'Commercial',
                'description' => 'Menangani prospek masuk, follow-up klien, pembuatan penawaran (Quotation), konversi pesanan (Order), dan serah terima akhir.',
                'modules' => ['Dashboard', 'Sales Pipeline (Leads)', 'Quotations & Orders', 'Customers', 'Projects (Pantau)', 'Purchases'],
            ],
            self::ROLE_MARKETING => [
                'label' => 'Marketing Manager',
                'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'level' => 'Commercial',
                'description' => 'Mengelola akuisisi prospek/lead dari kampanye promosi, review awal prospek, follow-up, dan database pelanggan.',
                'modules' => ['Dashboard', 'Sales Pipeline (Leads)', 'Quotations', 'Customers', 'Purchases'],
            ],
            self::ROLE_WEB_DEVELOPER => [
                'label' => 'Web Developer',
                'badge' => 'bg-cyan-50 text-cyan-800 border-cyan-200',
                'level' => 'Technical Production',
                'description' => 'Mengerjakan proyek dan tugas pengembangan website, mengunggah deliverable/aset, serta mengajukan hasil ke Quality Control.',
                'modules' => ['Dashboard', 'Projects & My Tasks', 'Deliverables & Assets', 'Purchases'],
            ],
            self::ROLE_UI_UX_DESIGNER => [
                'label' => 'UI/UX Designer',
                'badge' => 'bg-sky-50 text-sky-700 border-sky-200',
                'level' => 'Technical Production',
                'description' => 'Merancang wireframe, prototype, dan antarmuka pengguna pada proyek aktif serta mengirimkan hasil ke tahap QC.',
                'modules' => ['Dashboard', 'Projects & My Tasks', 'Deliverables & Assets', 'Purchases'],
            ],
            self::ROLE_GRAPHIC_DESIGNER => [
                'label' => 'Graphic Designer',
                'badge' => 'bg-violet-50 text-violet-700 border-violet-200',
                'level' => 'Technical Production',
                'description' => 'Mengerjakan aset desain grafis, konten sosial media, poster, dan ID card event serta mengirim hasil ke tahap QC.',
                'modules' => ['Dashboard', 'Projects & My Tasks', 'Deliverables & Assets', 'Purchases'],
            ],
            self::ROLE_QC_ADMIN => [
                'label' => 'Quality Control & Admin',
                'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                'level' => 'Quality Assurance',
                'description' => 'Memeriksa kualitas hasil tugas dan proyek (Approve QC / Minta Revisi), mengelola dokumentasi, dan membantu serah terima pesanan.',
                'modules' => ['Dashboard', 'Orders (Serah Terima)', 'Projects & QC Review', 'Deliverables & Assets', 'Purchases'],
            ],
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleDefinitions()[$this->role]['label'] ?? ucfirst(str_replace('_', ' ', $this->role));
    }
}
