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
            'activity_logs', 'users' => false,
            default => false,
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_CEO => 'CEO / Project Manager',
            self::ROLE_FINANCE => 'Finance Manager',
            self::ROLE_MARKETING => 'Marketing Manager',
            self::ROLE_WEB_DEVELOPER => 'Web Developer',
            self::ROLE_GRAPHIC_DESIGNER => 'Graphic Designer',
            self::ROLE_UI_UX_DESIGNER => 'UI/UX Designer',
            self::ROLE_SALES => 'Sales & Customer Relations',
            self::ROLE_QC_ADMIN => 'Quality Control & Admin',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
