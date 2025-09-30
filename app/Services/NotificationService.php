<?php

namespace App\Services;

use App\Models\CamsNotification;
use App\Models\User;
use App\Models\CaseModel;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**m
     * Create a new case assigned notification
     */
    public static function caseAssigned(CaseModel $case, User $socialWorker): void
    {
        self::create([
            'user_id' => $socialWorker->id,
            'type' => CamsNotification::TYPE_CASE_ASSIGNED,
            'title' => 'New Case Assigned',
            'message' => "Case {$case->case_number} has been assigned to you for {$case->child_name}.",
            'data' => [
                'case_id' => $case->id,
                'case_number' => $case->case_number,
                'child_name' => $case->child_name,
                'abuse_type' => $case->abuse_type,
            ],
            'priority' => $case->priority,
        ]);
    }

    /**
     * Create a case updated notification
     */
    public static function caseUpdated(CaseModel $case, User $updatedBy, string $updateType = 'general'): void
    {
        // Notify social worker if updated by someone else
        if ($case->social_worker_id !== $updatedBy->id) {
            self::create([
                'user_id' => $case->social_worker_id,
                'type' => CamsNotification::TYPE_CASE_UPDATED,
                'title' => 'Case Updated',
                'message' => "Case {$case->case_number} has been updated by {$updatedBy->name}.",
                'data' => [
                    'case_id' => $case->id,
                    'case_number' => $case->case_number,
                    'updated_by' => $updatedBy->name,
                    'update_type' => $updateType,
                ],
                'priority' => 'medium',
            ]);
        }

        // Notify police officer if assigned
        if ($case->police_officer_id && $case->police_officer_id !== $updatedBy->id) {
            self::create([
                'user_id' => $case->police_officer_id,
                'type' => CamsNotification::TYPE_CASE_UPDATED,
                'title' => 'Case Updated',
                'message' => "Case {$case->case_number} has been updated by {$updatedBy->name}.",
                'data' => [
                    'case_id' => $case->id,
                    'case_number' => $case->case_number,
                    'updated_by' => $updatedBy->name,
                    'update_type' => $updateType,
                ],
                'priority' => 'medium',
            ]);
        }
    }

    /**
     * Create a police assigned notification
     */
    public static function policeAssigned(CaseModel $case, User $policeOfficer, User $assignedBy): void
    {
        // Notify police officer
        self::create([
            'user_id' => $policeOfficer->id,
            'type' => CamsNotification::TYPE_POLICE_ASSIGNED,
            'title' => 'Case Assigned to You',
            'message' => "Case {$case->case_number} has been assigned to you by {$assignedBy->name}.",
            'data' => [
                'case_id' => $case->id,
                'case_number' => $case->case_number,
                'child_name' => $case->child_name,
                'assigned_by' => $assignedBy->name,
                'priority' => $case->priority,
            ],
            'priority' => $case->priority,
        ]);
    }

    /**
     * Create overdue case notifications
     */
    public static function caseOverdue(CaseModel $case): void
    {
        // Notify social worker
        self::create([
            'user_id' => $case->social_worker_id,
            'type' => CamsNotification::TYPE_CASE_OVERDUE,
            'title' => 'Case Overdue',
            'message' => "Case {$case->case_number} is overdue and requires immediate attention.",
            'data' => [
                'case_id' => $case->id,
                'case_number' => $case->case_number,
                'child_name' => $case->child_name,
                'days_overdue' => $case->created_at->diffInDays(now()),
            ],
            'priority' => 'high',
        ]);

        // Notify police officer if assigned
        if ($case->police_officer_id) {
            self::create([
                'user_id' => $case->police_officer_id,
                'type' => CamsNotification::TYPE_CASE_OVERDUE,
                'title' => 'Case Overdue',
                'message' => "Case {$case->case_number} is overdue and requires immediate attention.",
                'data' => [
                    'case_id' => $case->id,
                    'case_number' => $case->case_number,
                    'child_name' => $case->child_name,
                    'days_overdue' => $case->created_at->diffInDays(now()),
                ],
                'priority' => 'high',
            ]);
        }
    }

    /**
     * Create critical case notification
     */
    public static function criticalCase(CaseModel $case): void
    {
        // Get all admins
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'type' => CamsNotification::TYPE_CASE_CRITICAL,
                'title' => 'Critical Case Alert',
                'message' => "Critical case {$case->case_number} requires immediate administrative attention.",
                'data' => [
                    'case_id' => $case->id,
                    'case_number' => $case->case_number,
                    'child_name' => $case->child_name,
                    'abuse_type' => $case->abuse_type,
                    'social_worker' => $case->socialWorker->name,
                ],
                'priority' => 'critical',
            ]);
        }
    }

    /**
     * Create case resolved notification
     */
    public static function caseResolved(CaseModel $case, User $resolvedBy): void
    {
        // Notify all involved parties
        $users = collect([$case->social_worker_id, $case->police_officer_id])
            ->filter()
            ->unique()
            ->reject(fn($id) => $id === $resolvedBy->id);

        foreach ($users as $userId) {
            self::create([
                'user_id' => $userId,
                'type' => CamsNotification::TYPE_CASE_RESOLVED,
                'title' => 'Case Resolved',
                'message' => "Case {$case->case_number} has been resolved by {$resolvedBy->name}.",
                'data' => [
                    'case_id' => $case->id,
                    'case_number' => $case->case_number,
                    'child_name' => $case->child_name,
                    'resolved_by' => $resolvedBy->name,
                ],
                'priority' => 'medium',
            ]);
        }
    }

    /**
     * Create system alert notification
     */
    public static function systemAlert(User $user, string $title, string $message, array $data = [], string $priority = 'medium'): void
    {
        self::create([
            'user_id' => $user->id,
            'type' => CamsNotification::TYPE_SYSTEM_ALERT,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
        ]);
    }

    /**
     * Create notification
     */
    private static function create(array $data): void
    {
        try {
            CamsNotification::create($data);

            Log::info('Notification created', [
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'title' => $data['title'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(User $user): void
    {
        CamsNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount(User $user): int
    {
        return CamsNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Clean old notifications (older than 30 days)
     */
    public static function cleanOldNotifications(): void
    {
        CamsNotification::where('created_at', '<', now()->subDays(30))
            ->where('is_read', true)
            ->delete();
    }
}
