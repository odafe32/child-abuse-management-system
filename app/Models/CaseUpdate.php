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
        'description',
        'old_values',
        'new_values',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
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
     * Get the case this update belongs to
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    /**
     * Get the user who made this update
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get update type options
     */
    public static function getUpdateTypes(): array
    {
        return [
            'case_created' => 'Case Created',
            'status_changed' => 'Status Changed',
            'assigned_police' => 'Assigned to Police',
            'note_added' => 'Note Added',
            'information_updated' => 'Information Updated',
            'case_closed' => 'Case Closed',
            'case_transferred' => 'Case Transferred',
        ];
    }

    /**
     * Get update type display name
     */
    public function getUpdateTypeDisplayAttribute(): string
    {
        return self::getUpdateTypes()[$this->update_type] ?? $this->update_type;
    }
}
