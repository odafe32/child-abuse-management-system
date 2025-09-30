<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_SOCIAL_WORKER = 'social_worker';
    const ROLE_POLICE_OFFICER = 'police_officer';

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'department',
        'phone',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    /**
     * Generate a new UUID for the model.
     *
     * @return string
     */
    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    /**
     * Get avatar URL or default avatar
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        // Return default avatar based on role
        return $this->getDefaultAvatar();
    }

    /**
     * Get default avatar based on role
     */
    public function getDefaultAvatar(): string
    {
        $defaultAvatars = [
            self::ROLE_ADMIN => url('assets/images/default-admin-avatar.png'),
            self::ROLE_SOCIAL_WORKER => url('assets/images/default-social-worker-avatar.png'),
            self::ROLE_POLICE_OFFICER => url('assets/images/default-police-avatar.png'),
        ];

        return $defaultAvatars[$this->role] ?? url('assets/images/default-avatar.png');
    }

    /**
     * Get user initials for avatar fallback
     */
    public function getInitials(): string
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_SOCIAL_WORKER => 'Social Worker',
            self::ROLE_POLICE_OFFICER => 'Police Officer',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is social worker
     */
    public function isSocialWorker(): bool
    {
        return $this->hasRole(self::ROLE_SOCIAL_WORKER);
    }

    /**
     * Check if user is police officer
     */
    public function isPoliceOfficer(): bool
    {
        return $this->hasRole(self::ROLE_POLICE_OFFICER);
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    /**
     * Scope to filter active users
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role
     */
    public function scopeByRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get admins
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->byRole(self::ROLE_ADMIN);
    }

    /**
     * Scope to get social workers
     */
    public function scopeSocialWorkers(Builder $query): Builder
    {
        return $query->byRole(self::ROLE_SOCIAL_WORKER);
    }

    /**
     * Scope to get police officers
     */
    public function scopePoliceOfficers(Builder $query): Builder
    {
        return $query->byRole(self::ROLE_POLICE_OFFICER);
    }

    /**
     * Update last login information
     */
    public function updateLastLogin(string $ipAddress = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Get dashboard route based on user role
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_SOCIAL_WORKER => 'social-worker.dashboard',
            self::ROLE_POLICE_OFFICER => 'police.dashboard',
            default => 'dashboard'
        };
    }
}
