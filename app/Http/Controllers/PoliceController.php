<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CaseModel;
use App\Models\CaseUpdate;
use App\Models\CamsNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class PoliceController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();

        // Get basic case counts
        $totalCases = CaseModel::where('police_officer_id', $user->id)->count();
        $activeCases = CaseModel::where('police_officer_id', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCases = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])->count();

        // Get priority-based counts
        $highPriorityCases = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('priority', ['high', 'critical'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $criticalCases = CaseModel::where('police_officer_id', $user->id)
            ->where('priority', 'critical')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $underInvestigation = CaseModel::where('police_officer_id', $user->id)
            ->where('status', 'under_investigation')
            ->count();

        // Get overdue cases (more than 30 days without update)
        $overdueCases = CaseModel::where('police_officer_id', $user->id)
            ->where('updated_at', '<', now()->subDays(30))
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        // Get monthly statistics
        $resolvedThisMonth = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $newCasesThisMonth = CaseModel::where('police_officer_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Calculate average resolution time
        $resolvedCasesWithTime = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('closed_at')
            ->get();

        $avgResolutionTime = 0;
        if ($resolvedCasesWithTime->count() > 0) {
            $totalDays = $resolvedCasesWithTime->sum(function ($case) {
                return $case->date_reported->diffInDays($case->closed_at);
            });
            $avgResolutionTime = round($totalDays / $resolvedCasesWithTime->count());
        }

        // Get priority cases for the main table (high and critical priority, active cases)
        $priorityCases = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('priority', ['high', 'critical'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with(['socialWorker'])
            ->orderByRaw("FIELD(priority, 'critical', 'high')")
            ->orderBy('date_reported', 'asc')
            ->limit(10)
            ->get();

        // Get recent updates/activity
        $recentUpdates = collect();

        // Try to get case updates if the table exists and has the right structure
        try {
            if (Schema::hasTable('case_updates')) {
                $recentUpdates = CaseUpdate::whereHas('case', function ($query) use ($user) {
                    $query->where('police_officer_id', $user->id);
                })
                ->with(['case', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            }
        } catch (\Exception $e) {
            // If case_updates table doesn't exist or has issues, create fake recent activity
            // from recent case updates
            $recentCases = CaseModel::where('police_officer_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            $recentUpdates = $recentCases->map(function ($case) {
                return (object) [
                    'case' => $case,
                    'update_type' => 'status_change',
                    'content' => 'Case updated',
                    'description' => 'Case updated',
                    'created_at' => $case->updated_at,
                    'user' => Auth::user()
                ];
            });
        }

        $viewData = [
            'meta_title' => 'Dashboard | CAMS',
            'meta_desc' => 'CAMS Police Officer Dashboard - Monitor your assigned cases and investigations.',
            'meta_image' => url('logo.png'),

            // Basic counts
            'totalCases' => $totalCases,
            'activeCases' => $activeCases,
            'resolvedCases' => $resolvedCases,

            // Priority counts
            'highPriorityCases' => $highPriorityCases,
            'criticalCases' => $criticalCases,
            'underInvestigation' => $underInvestigation,
            'overdueCases' => $overdueCases,

            // Monthly stats
            'resolvedThisMonth' => $resolvedThisMonth,
            'newCasesThisMonth' => $newCasesThisMonth,
            'avgResolutionTime' => $avgResolutionTime,

            // Data collections
            'priorityCases' => $priorityCases,
            'recentUpdates' => $recentUpdates,
        ];

        return view('police.dashboard', $viewData);
    }

    /**
     * Show assigned cases to the police officer
     */
    public function showAssignedCases(Request $request): View
    {
        $user = Auth::user();

        // Build query for assigned cases
        $query = CaseModel::where('police_officer_id', $user->id)
            ->with(['socialWorker', 'updates']);

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('abuse_type')) {
            $query->byAbuseType($request->abuse_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        // Get paginated results
        $cases = $query->orderBy('created_at', 'desc')->paginate(25);

        // Calculate statistics
        $totalCases = CaseModel::where('police_officer_id', $user->id)->count();
        $underInvestigation = CaseModel::where('police_officer_id', $user->id)
            ->byStatus('under_investigation')->count();
        $highPriority = CaseModel::where('police_officer_id', $user->id)
            ->byPriority('high')->count();
        $resolvedCases = CaseModel::where('police_officer_id', $user->id)
            ->byStatus('resolved')->count();

        $viewData = [
            'meta_title' => 'Assigned Cases | CAMS',
            'meta_desc' => 'Manage and investigate your assigned child abuse cases.',
            'meta_image' => url('logo.png'),
            'cases' => $cases,
            'totalCases' => $totalCases,
            'underInvestigation' => $underInvestigation,
            'highPriority' => $highPriority,
            'resolvedCases' => $resolvedCases,
        ];

        return view('police.assigned-cases', $viewData);
    }

    /**
     * Show case details
     */
    public function showCaseDetails(string $id): View
    {
        $case = CaseModel::with(['socialWorker', 'policeOfficer', 'updates.user'])
            ->where('police_officer_id', Auth::id())
            ->findOrFail($id);

        return view('police.partials.case-details', compact('case'));
    }

    /**
     * Get case data for editing
     */
    public function getCaseData(string $id): JsonResponse
    {
        $case = CaseModel::where('police_officer_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $case->id,
            'case_number' => $case->case_number,
            'status' => $case->status,
            'investigation_notes' => $case->investigation_notes,
            'priority' => $case->priority,
        ]);
    }

    /**
     * Update case status and investigation notes
     */
    public function updateCaseStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:under_investigation,in_progress,resolved,closed'],
            'investigation_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $case = CaseModel::where('police_officer_id', Auth::id())
            ->findOrFail($id);

        $oldStatus = $case->status;

        $case->update([
            'status' => $request->status,
            'investigation_notes' => $request->investigation_notes,
            'last_updated' => now(),
        ]);

        // Create case update record with only available fields
        $updateData = [
            'case_id' => $case->id,
            'user_id' => Auth::id(),
        ];

        // Add update_type if column exists
        if (Schema::hasColumn('case_updates', 'update_type')) {
            $updateData['update_type'] = 'status_change';
        }

        // Add content or description based on what exists
        $contentText = "Status changed from '{$oldStatus}' to '{$request->status}'" .
                      ($request->investigation_notes ? "\n\nInvestigation Notes: " . $request->investigation_notes : '');

        if (Schema::hasColumn('case_updates', 'content')) {
            $updateData['content'] = $contentText;
        } elseif (Schema::hasColumn('case_updates', 'description')) {
            $updateData['description'] = $contentText;
        }

        // Add metadata if column exists
        if (Schema::hasColumn('case_updates', 'metadata')) {
            $updateData['metadata'] = json_encode([
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'investigation_notes' => $request->investigation_notes,
            ]);
        }

        try {
            CaseUpdate::create($updateData);
        } catch (\Exception $e) {
            Log::error('Error creating case update', [
                'error' => $e->getMessage(),
                'update_data' => $updateData,
                'available_columns' => Schema::getColumnListing('case_updates'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating case status. Please try again.',
            ], 500);
        }

        // Send notification to social worker about the update
        if ($case->socialWorker) {
            NotificationService::caseUpdated($case, Auth::user(), 'status_change');
        }

        Log::info('Police officer updated case status', [
            'user_id' => Auth::id(),
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Case status updated successfully.',
        ]);
    }

    /**
     * Add investigation note to case
     */
    public function addInvestigationNote(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'note_type' => ['required', 'string', 'in:investigation,evidence,interview,follow_up,other'],
            'content' => ['required', 'string', 'max:5000'],
            'evidence_files.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $case = CaseModel::where('police_officer_id', Auth::id())
            ->findOrFail($id);

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $path = $file->store('case-evidence/' . $case->id, 'local');
                $uploadedFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Create case update record with only available fields
        $updateData = [
            'case_id' => $case->id,
            'user_id' => Auth::id(),
        ];

        // Add update_type if column exists
        if (Schema::hasColumn('case_updates', 'update_type')) {
            $updateData['update_type'] = 'investigation_note';
        }

        // Add content or description based on what exists
        $contentText = $request->content;
        if (!empty($uploadedFiles)) {
            $contentText .= "\n\nEvidence files attached: " . count($uploadedFiles) . " file(s)";
        }

        if (Schema::hasColumn('case_updates', 'content')) {
            $updateData['content'] = $contentText;
        } elseif (Schema::hasColumn('case_updates', 'description')) {
            $updateData['description'] = $contentText;
        }

        // Add metadata if column exists
        if (Schema::hasColumn('case_updates', 'metadata')) {
            $metadata = [
                'note_type' => $request->note_type,
            ];
            if (!empty($uploadedFiles)) {
                $metadata['evidence_files'] = $uploadedFiles;
            }
            $updateData['metadata'] = json_encode($metadata);
        }

        try {
            CaseUpdate::create($updateData);
        } catch (\Exception $e) {
            Log::error('Error creating investigation note', [
                'error' => $e->getMessage(),
                'update_data' => $updateData,
                'available_columns' => Schema::getColumnListing('case_updates'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding investigation note. Please try again.',
            ], 500);
        }

        // Update case last_updated timestamp
        $case->touch();

        // Send notification to social worker about the update
        if ($case->socialWorker) {
            NotificationService::caseUpdated($case, Auth::user(), 'investigation_note');
        }

        Log::info('Police officer added investigation note', [
            'user_id' => Auth::id(),
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'note_type' => $request->note_type,
            'files_count' => count($uploadedFiles),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Investigation note added successfully.',
        ]);
    }

    /**
     * Download evidence file
     */
    public function downloadEvidence(string $caseId, string $filename): Response
    {
        $case = CaseModel::where('police_officer_id', Auth::id())
            ->findOrFail($caseId);

        $filePath = 'case-evidence/' . $caseId . '/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($filePath);
    }

    /**
     * Show cases history for the police officer
     */
    public function showCasesHistory(Request $request): View
    {
        $user = Auth::user();

        // Build query for cases history (all cases ever assigned to this officer)
        $query = CaseModel::where('police_officer_id', $user->id)
            ->with(['socialWorker', 'updates' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(3);
            }]);

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('abuse_type')) {
            $query->byAbuseType($request->abuse_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        if ($request->filled('year')) {
            $query->whereYear('date_reported', $request->year);
        }

        // Get paginated results ordered by most recent first
        $cases = $query->orderBy('updated_at', 'desc')->paginate(20);

        // Calculate statistics
        $totalCases = CaseModel::where('police_officer_id', $user->id)->count();
        $closedCases = CaseModel::where('police_officer_id', $user->id)
            ->whereIn('status', ['resolved', 'closed'])->count();
        $activeCases = CaseModel::where('police_officer_id', $user->id)
            ->whereNotIn('status', ['resolved', 'closed'])->count();
        $thisYearCases = CaseModel::where('police_officer_id', $user->id)
            ->whereYear('date_reported', now()->year)->count();

        // Get years for filter dropdown
        $availableYears = CaseModel::where('police_officer_id', $user->id)
            ->selectRaw('YEAR(date_reported) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Get monthly statistics for current year
        $monthlyStats = CaseModel::where('police_officer_id', $user->id)
            ->whereYear('date_reported', now()->year)
            ->selectRaw('MONTH(date_reported) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // Get status distribution data for pie chart
        $statusDistribution = CaseModel::where('police_officer_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                $statuses = CaseModel::getStatuses();
                $statusLabel = $statuses[$item->status] ?? ucfirst(str_replace('_', ' ', $item->status));
                return [$statusLabel => $item->count];
            });

        // Get priority distribution data
        $priorityDistribution = CaseModel::where('police_officer_id', $user->id)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function ($item) {
                $priorities = CaseModel::getPriorities();
                $priorityLabel = $priorities[$item->priority] ?? ucfirst($item->priority);
                return [$priorityLabel => $item->count];
            });

        // Get abuse type distribution data
        $abuseTypeDistribution = CaseModel::where('police_officer_id', $user->id)
            ->selectRaw('abuse_type, COUNT(*) as count')
            ->groupBy('abuse_type')
            ->get()
            ->mapWithKeys(function ($item) {
                $abuseTypes = CaseModel::getAbuseTypes();
                $abuseTypeLabel = $abuseTypes[$item->abuse_type] ?? ucfirst(str_replace('_', ' ', $item->abuse_type));
                return [$abuseTypeLabel => $item->count];
            });

        $viewData = [
            'meta_title' => 'Cases History | CAMS',
            'meta_desc' => 'View your complete case history and statistics.',
            'meta_image' => url('logo.png'),
            'cases' => $cases,
            'totalCases' => $totalCases,
            'closedCases' => $closedCases,
            'activeCases' => $activeCases,
            'thisYearCases' => $thisYearCases,
            'availableYears' => $availableYears,
            'monthlyStats' => $monthlyStats,
            'statusDistribution' => $statusDistribution,
            'priorityDistribution' => $priorityDistribution,
            'abuseTypeDistribution' => $abuseTypeDistribution,
        ];

        return view('police.cases-history', $viewData);
    }

    /**
     * Get case timeline for modal display
     */
    public function getCaseTimeline(string $id): View
    {
        $case = CaseModel::with(['updates.user'])
            ->where('police_officer_id', Auth::id())
            ->findOrFail($id);

        return view('police.partials.case-timeline', compact('case'));
    }

    // NOTIFICATION METHODS FOR POLICE

    /**
     * Display notifications page for police
     */
    public function showNotifications(Request $request): View
    {
        $user = Auth::user();

        $query = CamsNotification::forUser($user->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->read();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }

        $notifications = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => CamsNotification::forUser($user->id)->count(),
            'unread' => CamsNotification::forUser($user->id)->unread()->count(),
            'critical' => CamsNotification::forUser($user->id)->byPriority('critical')->unread()->count(),
        ];

        $viewData = [
            'meta_title' => 'Notifications | CAMS',
            'meta_desc' => 'View and manage your CAMS notifications',
            'meta_image' => url('logo.png'),
            'notifications' => $notifications,
            'stats' => $stats,
            'filters' => $request->only(['type', 'priority', 'status']),
            'types' => CamsNotification::getTypes(),
        ];

        return view('police.notifications.index', $viewData);
    }

    /**
     * Get notifications for dropdown (AJAX) - Police version
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();

        $notifications = CamsNotification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = NotificationService::getUnreadCount($user);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read - Police version
     */
    public function markNotificationAsRead(CamsNotification $notification): JsonResponse
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read - Police version
     */
    public function markAllNotificationsAsRead(): JsonResponse
    {
        $user = Auth::user();
        NotificationService::markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification - Police version
     */
    public function deleteNotification(CamsNotification $notification): JsonResponse
    {
        // Ensure user owns the notification
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get unread count - Police version
     */
    public function getUnreadNotificationCount(): JsonResponse
    {
        $user = Auth::user();
        $count = NotificationService::getUnreadCount($user);

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    // Profile methods (keeping existing functionality)
    public function showProfile(): View
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'Profile | CAMS',
            'meta_desc' => 'Manage your profile information and settings.',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('police.profile', $viewData);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = $request->only(['name', 'email', 'employee_id', 'department', 'phone']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        Log::info('Police officer profile updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'updated_fields' => array_keys($data),
        ]);

        return redirect()->route('police.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Log::info('Police officer password updated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('police.profile')
            ->with('success', 'Password updated successfully.');
    }

    public function removeAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            Log::info('Police officer avatar removed', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('police.profile')
                ->with('success', 'Avatar removed successfully.');
        }

        return redirect()->route('police.profile')
            ->with('error', 'No avatar to remove.');
    }
}
