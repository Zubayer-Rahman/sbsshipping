<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\ActivityLog;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    // ── Role Helpers ──────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isPowerUser(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'power_user']);
    }

    public function hasFullAccess(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'power_user']);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'Admin',
            'power_user'  => 'Power User',
            default       => 'User',
        };
    }

    public function roleBadgeColor(): string
    {
        return match ($this->role) {
            'super_admin' => '#ef4444',
            'admin'       => '#1a56db',
            'power_user'  => '#10b981',
            default       => '#64748b',
        };
    }

    // ── Static: available roles ───────────────────────────────────────────────

    public static function availableRoles(): array
    {
        return [
            'user'        => 'User',
            'power_user'  => 'Power User (Full Access)',
            'admin'       => 'Admin',
            'super_admin' => 'Super Admin',
        ];
    }
}
