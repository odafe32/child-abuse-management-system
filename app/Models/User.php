<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    // Role constants - Updated to match AdminController expectations
    const ROLE_ADMIN = 'admin';
    const ROLE_SOCIAL_WORKER = 'social_worker';
    const ROLE_POLICE = 'police_officer'; // Changed from police_officer to police

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
        'email_verified_at', // Added this
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

            // Set default values
            if (empty($model->is_active)) {
                $model->is_active = true;
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
     * Get cases assigned to this social worker
     */
    public function assignedCases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'social_worker_id');
    }

    /**
     * Get cases assigned to this police officer
     */
    public function policeCases(): HasMany
    {
        return $this->hasMany(CaseModel::class, 'police_officer_id');
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
            self::ROLE_POLICE => url('assets/images/default-police-avatar.png'),
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
            self::ROLE_POLICE => 'Police Officer', // Updated display name
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
    public function isPolice(): bool // Updated method name
    {
        return $this->hasRole(self::ROLE_POLICE);
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return self::getRoles()[$this->role] ?? 'Unknown';
    }

    /**
     * Get role display attribute (for blade views)
     */
    public function getRoleDisplayAttribute(): string
    {
        return $this->getRoleDisplayName();
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
        return $query->byRole(self::ROLE_POLICE);
    }

    /**
     * Scope to search users
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('employee_id', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
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
            self::ROLE_POLICE => 'police.dashboard',
            default => 'dashboard'
        };
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->email_verified_at !== null;
    }

    /**
     * Get user's full avatar URL attribute
     */
    public function getAvatarUrlAttribute(): string
    {
        return $this->getAvatarUrl();
    }
}
