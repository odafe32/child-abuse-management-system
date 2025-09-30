<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CamsNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'priority',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_CASE_ASSIGNED = 'case_assigned';
    const TYPE_CASE_UPDATED = 'case_updated';
    const TYPE_CASE_OVERDUE = 'case_overdue';
    const TYPE_CASE_CRITICAL = 'case_critical';
    const TYPE_POLICE_ASSIGNED = 'police_assigned';
    const TYPE_CASE_RESOLVED = 'case_resolved';
    const TYPE_SYSTEM_ALERT = 'system_alert';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all notification types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_CASE_ASSIGNED => 'Case Assigned',
            self::TYPE_CASE_UPDATED => 'Case Updated',
            self::TYPE_CASE_OVERDUE => 'Case Overdue',
            self::TYPE_CASE_CRITICAL => 'Critical Case',
            self::TYPE_POLICE_ASSIGNED => 'Police Assigned',
            self::TYPE_CASE_RESOLVED => 'Case Resolved',
            self::TYPE_SYSTEM_ALERT => 'System Alert',
        ];
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-success',
            'medium' => 'bg-warning text-dark',
            'high' => 'bg-danger',
            'critical' => 'bg-dark',
            default => 'bg-secondary'
        };
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CASE_ASSIGNED => 'ri-file-add-line',
            self::TYPE_CASE_UPDATED => 'ri-file-edit-line',
            self::TYPE_CASE_OVERDUE => 'ri-alarm-warning-line',
            self::TYPE_CASE_CRITICAL => 'ri-error-warning-line',
            self::TYPE_POLICE_ASSIGNED => 'ri-shield-user-line',
            self::TYPE_CASE_RESOLVED => 'ri-check-double-line',
            self::TYPE_SYSTEM_ALERT => 'ri-notification-3-line',
            default => 'ri-information-line'
        };
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get time ago formatted
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
