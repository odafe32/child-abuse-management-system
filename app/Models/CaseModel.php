<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CaseUpdate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CaseModel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cases';

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
     * @var array<int, string>
     */
    protected $fillable = [
        'case_number',
        'abuse_type',
        'description',
        'date_reported',
        'location',
        'status',
        'priority',
        'child_name',
        'child_dob',
        'child_age',
        'child_gender',
        'child_address',
        'child_school',
        'child_class',
        'medical_conditions',
        'injuries_description',
        'reporter_name',
        'reporter_relationship',
        'reporter_phone',
        'reporter_address',
        'reporter_email',
        'social_worker_id',
        'police_officer_id',
        'investigation_notes',
        'closure_notes',
        'closed_at',
          'offender_name',
    'offender_relationship',
    'offender_description',
    'offender_known',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_reported' => 'date',
        'child_dob' => 'date',
        'date_entered' => 'datetime',
        'last_updated' => 'datetime',
        'closed_at' => 'datetime',
        'child_age' => 'integer',
    ];

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

            // Generate case number if not provided
            if (empty($model->case_number)) {
                $model->case_number = $model->generateCaseNumber();
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
     * Generate a unique case number
     */
    public function generateCaseNumber(): string
    {
        $year = date('Y');
        $month = date('m');

        // Get the last case number for this month
        $lastCase = static::where('case_number', 'like', "CAMS-{$year}{$month}-%")
            ->orderBy('case_number', 'desc')
            ->first();

        if ($lastCase) {
            $lastNumber = (int) substr($lastCase->case_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('CAMS-%s%s-%04d', $year, $month, $newNumber);
    }

    /**
     * Get the social worker assigned to this case
     */
    public function socialWorker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'social_worker_id');
    }

    /**
     * Get the police officer assigned to this case
     */
    public function policeOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'police_officer_id');
    }

    /**
     * Get all updates for this case
     */
    public function updates(): HasMany
    {
        return $this->hasMany(CaseUpdate::class, 'case_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get abuse type options
     */
    public static function getAbuseTypes(): array
    {
        return [
            'physical' => 'Physical Abuse',
            'sexual' => 'Sexual Abuse',
            'emotional' => 'Emotional Abuse',
            'neglect' => 'Neglect',
            'psychological' => 'Psychological Abuse',
            'financial' => 'Financial Abuse',
            'other' => 'Other',
        ];
    }

    /**
     * Get status options
     */
    public static function getStatuses(): array
    {
        return [
            'reported' => 'Reported',
            'under_investigation' => 'Under Investigation',
            'assigned_to_police' => 'Assigned to Police',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'transferred' => 'Transferred',
        ];
    }

    /**
     * Get priority options
     */
    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    /**
     * Get gender options
     */
    public static function getGenders(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
    }

    /**
     * Get abuse type display name
     */
    public function getAbuseTypeDisplayAttribute(): string
    {
        return self::getAbuseTypes()[$this->abuse_type] ?? $this->abuse_type;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get priority display name
     */
    public function getPriorityDisplayAttribute(): string
    {
        return self::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'reported' => 'badge-info',
            'under_investigation' => 'badge-warning',
            'assigned_to_police' => 'badge-primary',
            'in_progress' => 'badge-secondary',
            'resolved' => 'badge-success',
            'closed' => 'badge-dark',
            'transferred' => 'badge-light',
            default => 'badge-secondary',
        };
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'badge-success',
            'medium' => 'badge-warning',
            'high' => 'badge-danger',
            'critical' => 'badge-dark',
            default => 'badge-secondary',
        };
    }

    /**
     * Calculate child's age if DOB is provided
     */
    public function getCalculatedAgeAttribute(): ?int
    {
        if ($this->child_dob) {
            return $this->child_dob->diffInYears(now());
        }

        return $this->child_age;
    }

    /**
     * Check if case is active
     */
    public function isActive(): bool
    {
        return !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Check if case is overdue (more than 30 days without update)
     */
    public function isOverdue(): bool
    {
        return $this->last_updated->diffInDays(now()) > 30 && $this->isActive();
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by abuse type
     */
    public function scopeByAbuseType(Builder $query, string $abuseType): Builder
    {
        return $query->where('abuse_type', $abuseType);
    }

    /**
     * Scope to filter by priority
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by social worker
     */
    public function scopeBySocialWorker(Builder $query, string $socialWorkerId): Builder
    {
        return $query->where('social_worker_id', $socialWorkerId);
    }

    /**
     * Scope to filter by police officer
     */
    public function scopeByPoliceOfficer(Builder $query, string $policeOfficerId): Builder
    {
        return $query->where('police_officer_id', $policeOfficerId);
    }

    /**
     * Scope to get active cases
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope to get overdue cases
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('last_updated', '<', now()->subDays(30))
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope to search cases
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('case_number', 'like', "%{$search}%")
              ->orWhere('child_name', 'like', "%{$search}%")
              ->orWhere('reporter_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

// Add helper method
public function getOffenderStatusAttribute(): string
{
    if (!$this->offender_known) {
        return 'Unknown Offender';
    }

    return $this->offender_name ? $this->offender_name : 'Known but Unnamed';
}
}
