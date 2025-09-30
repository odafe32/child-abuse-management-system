<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class CaseUpdate extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'case_updates';

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
        'case_id',
        'user_id',
        'update_type',
        'content',        // Main content field
        'description',    // Alternative content field (if your DB uses this)
        'metadata',
        'is_internal',
        'priority',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Get the case that this update belongs to
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    /**
     * Get the user who created this update
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get update type options
     */
    public static function getUpdateTypes(): array
    {
        return [
            'status_change' => 'Status Change',
            'assignment' => 'Assignment',
            'investigation_note' => 'Investigation Note',
            'evidence' => 'Evidence Added',
            'interview' => 'Interview Conducted',
            'follow_up' => 'Follow-up Action',
            'closure' => 'Case Closure',
            'transfer' => 'Case Transfer',
            'other' => 'Other',
        ];
    }

    /**
     * Get the display name for update type
     */
    public function getUpdateTypeDisplayAttribute(): string
    {
        return self::getUpdateTypes()[$this->update_type] ?? ucfirst(str_replace('_', ' ', $this->update_type));
    }

    /**
     * Scope to filter by update type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('update_type', $type);
    }

    /**
     * Scope to filter by case
     */
    public function scopeByCase($query, string $caseId)
    {
        return $query->where('case_id', $caseId);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent updates
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
