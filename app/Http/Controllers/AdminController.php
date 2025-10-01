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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function showDashboard()
    {
        // Get system statistics
        $totalUsers = User::count();
        $totalCases = CaseModel::count();
        $activeCases = CaseModel::whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCases = CaseModel::whereIn('status', ['resolved', 'closed'])->count();

        // Get user role counts
        $socialWorkers = User::where('role', 'social_worker')->count();
        $policeOfficers = User::where('role', 'police_officer')->count();
        $admins = User::where('role', 'admin')->count();

        // Get priority case counts
        $criticalCases = CaseModel::where('priority', 'critical')
            ->whereNotIn('status', ['resolved', 'closed'])->count();
        $highPriorityCases = CaseModel::where('priority', 'high')
            ->whereNotIn('status', ['resolved', 'closed'])->count();

        // Get overdue cases
        $overdueCases = CaseModel::where('updated_at', '<', now()->subDays(30))
            ->whereNotIn('status', ['resolved', 'closed'])->count();

        // Get monthly statistics
        $newCasesThisMonth = CaseModel::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $resolvedThisMonth = CaseModel::whereIn('status', ['resolved', 'closed'])
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)->count();

        // Get recent cases
        $recentCases = CaseModel::with(['socialWorker', 'policeOfficer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $viewData = [
            'meta_title' => 'Admin Dashboard | CAMS',
            'meta_desc' => 'CAMS Admin Dashboard - Monitor system statistics, users, and cases.',
            'meta_image' => url('logo.png'),

            // System statistics
            'totalUsers' => $totalUsers,
            'totalCases' => $totalCases,
            'activeCases' => $activeCases,
            'resolvedCases' => $resolvedCases,

            // User statistics
            'socialWorkers' => $socialWorkers,
            'policeOfficers' => $policeOfficers,
            'admins' => $admins,

            // Case statistics
            'criticalCases' => $criticalCases,
            'highPriorityCases' => $highPriorityCases,
            'overdueCases' => $overdueCases,
            'newCasesThisMonth' => $newCasesThisMonth,
            'resolvedThisMonth' => $resolvedThisMonth,

            // Recent data
            'recentCases' => $recentCases,
            'recentUsers' => $recentUsers,
        ];

        return view('admin.dashboard', $viewData);
    }

    /**
     * Display user management page
     */
    public function manageUsers(Request $request): View
    {
        // Build query for users
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply department filter
        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('email_verified_at');
            }
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        // Calculate statistics
        $totalUsers = User::count();
        $socialWorkers = User::where('role', 'social_worker')->count();
        $policeOfficers = User::where('role', 'police_officer')->count();

        $admins = User::where('role', 'admin')->count();

        $viewData = [
            'meta_title' => 'User Management | CAMS',
            'meta_desc' => 'Manage system users, roles, and permissions.',
            'meta_image' => url('logo.png'),
            'users' => $users,
            'totalUsers' => $totalUsers,
            'socialWorkers' => $socialWorkers,
            'policeOfficers' => $policeOfficers,
            'admins' => $admins,
        ];

        return view('admin.users', $viewData);
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:users'],
            'role' => ['required', 'string', 'in:admin,social_worker,police_officer'],

            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'email', 'employee_id', 'role', 'department', 'phone']);
            $data['password'] = Hash::make($request->password);
            $data['email_verified_at'] = now(); // Auto-verify admin created accounts

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $avatarPath;
            }

            $user = User::create($data);

            DB::commit();

            Log::info('Admin created new user', [
                'admin_id' => Auth::id(),
                'new_user_id' => $user->id,
                'new_user_email' => $user->email,
                'new_user_role' => $user->role,
            ]);

            return redirect()->route('admin.users')
                ->with('success', "User '{$user->name}' has been created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create user', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password', 'password_confirmation']),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Show user details
     */
    public function showUser(string $id): View
    {
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Get user data for editing
     */
    public function editUser(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'employee_id' => $user->employee_id,
                    'role' => $user->role,
                    'department' => $user->department,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user data for editing', [
                'user_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load user data.',
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
           'role' => ['required', 'string', 'in:admin,social_worker,police_officer'],

            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'reset_password' => ['nullable', 'boolean'],
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'email', 'employee_id', 'role', 'department', 'phone']);

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

            // Handle password reset
            if ($request->boolean('reset_password')) {
                $newPassword = Str::random(12);
                $data['password'] = Hash::make($newPassword);

                Log::info('Password reset for user', [
                    'admin_id' => Auth::id(),
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }

            $oldRole = $user->role;
            $user->update($data);

            // If role changed, handle case reassignments
            if ($oldRole !== $user->role) {
                $this->handleRoleChange($user, $oldRole);
            }

            DB::commit();

            Log::info('Admin updated user', [
                'admin_id' => Auth::id(),
                'updated_user_id' => $user->id,
                'updated_user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'updated_fields' => array_keys($data),
            ]);

            $message = "User '{$user->name}' has been updated successfully.";
            if ($request->boolean('reset_password')) {
                $message .= ' A new password has been generated.';
            }

            return redirect()->route('admin.users')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update user', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Delete user
     */
    public function destroyUser(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            DB::beginTransaction();

            $userName = $user->name;
            $userEmail = $user->email;

            // Handle case reassignments before deleting
            $reassignmentResult = $this->handleUserDeletion($user);

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            DB::commit();

            Log::info('Admin deleted user', [
                'admin_id' => Auth::id(),
                'deleted_user_name' => $userName,
                'deleted_user_email' => $userEmail,
                'reassignment_result' => $reassignmentResult,
            ]);

            $message = "User '{$userName}' has been deleted successfully.";
            if (!empty($reassignmentResult['messages'])) {
                $message .= ' ' . implode(' ', $reassignmentResult['messages']);
            }

            return redirect()->route('admin.users')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete user', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.users')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Handle role changes with better error handling
     */
    private function handleRoleChange(User $user, string $oldRole): array
    {
        $result = ['success' => true, 'messages' => []];

        try {
            // If user was a social worker, reassign their cases
            if ($oldRole === 'social_worker') {
                $casesToReassign = CaseModel::where('social_worker_id', $user->id)->get();

                if ($casesToReassign->count() > 0) {
                    // Try to find another social worker to reassign cases to
                    $availableSocialWorker = User::where('role', 'social_worker')
                        ->where('id', '!=', $user->id)
                        ->first();

                    if ($availableSocialWorker) {
                        CaseModel::where('social_worker_id', $user->id)
                            ->update(['social_worker_id' => $availableSocialWorker->id]);

                        $result['messages'][] = "{$casesToReassign->count()} cases reassigned to {$availableSocialWorker->name}.";
                    } else {
                        // No available social worker, set to null (requires nullable column)
                        CaseModel::where('social_worker_id', $user->id)
                            ->update(['social_worker_id' => null]);

                        $result['messages'][] = "{$casesToReassign->count()} cases unassigned (no available social workers).";
                    }
                }
            }

            // If user was a police officer, reassign their cases
            if ($oldRole === 'police_officer') {
                $casesToReassign = CaseModel::where('police_officer_id', $user->id)->get();

                if ($casesToReassign->count() > 0) {
                    // Try to find another police officer to reassign cases to
                    $availablePoliceOfficer = User::where('role', 'police_officer')
                        ->where('id', '!=', $user->id)
                        ->first();

                    if ($availablePoliceOfficer) {
                        CaseModel::where('police_officer_id', $user->id)
                            ->update(['police_officer_id' => $availablePoliceOfficer->id]);

                        $result['messages'][] = "{$casesToReassign->count()} cases reassigned to {$availablePoliceOfficer->name}.";
                    } else {
                        // No available police officer, set to null (requires nullable column)
                        CaseModel::where('police_officer_id', $user->id)
                            ->update(['police_officer_id' => null]);

                        $result['messages'][] = "{$casesToReassign->count()} cases unassigned (no available police officers).";
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Error handling role change', [
                'user_id' => $user->id,
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'error' => $e->getMessage(),
            ]);

            $result['success'] = false;
            $result['messages'][] = 'Warning: Some cases may not have been properly reassigned.';
        }

        return $result;
    }

    /**
     * Handle user deletion with better error handling
     */
    private function handleUserDeletion(User $user): array
    {
        $result = ['success' => true, 'messages' => []];

        try {
            // Handle social worker cases
            if ($user->role === 'social_worker') {
                $casesToReassign = CaseModel::where('social_worker_id', $user->id)->get();

                if ($casesToReassign->count() > 0) {
                    // Try to find another social worker to reassign cases to
                    $availableSocialWorker = User::where('role', 'social_worker')
                        ->where('id', '!=', $user->id)
                        ->first();

                    if ($availableSocialWorker) {
                        CaseModel::where('social_worker_id', $user->id)
                            ->update(['social_worker_id' => $availableSocialWorker->id]);

                        $result['messages'][] = "{$casesToReassign->count()} cases reassigned to {$availableSocialWorker->name}.";
                    } else {
                        // No available social worker, set to null
                        CaseModel::where('social_worker_id', $user->id)
                            ->update(['social_worker_id' => null]);

                        $result['messages'][] = "{$casesToReassign->count()} cases unassigned (no available social workers).";
                    }
                }
            }

            // Handle police officer cases
            if ($user->role === 'police_officer') {
                $casesToReassign = CaseModel::where('police_officer_id', $user->id)->get();

                if ($casesToReassign->count() > 0) {
                    // Try to find another police officer to reassign cases to
                    $availablePoliceOfficer = User::where('role', 'police_officer')
                        ->where('id', '!=', $user->id)
                        ->first();

                    if ($availablePoliceOfficer) {
                        CaseModel::where('police_officer_id', $user->id)
                            ->update(['police_officer_id' => $availablePoliceOfficer->id]);

                        $result['messages'][] = "{$casesToReassign->count()} cases reassigned to {$availablePoliceOfficer->name}.";
                    } else {
                        // No available police officer, set to null
                        CaseModel::where('police_officer_id', $user->id)
                            ->update(['police_officer_id' => null]);

                        $result['messages'][] = "{$casesToReassign->count()} cases unassigned (no available police officers).";
                    }
                }
            }

            // Delete user's notifications
            if (Schema::hasTable('cams_notifications')) {
                CamsNotification::where('user_id', $user->id)->delete();
            }

            // Update case updates to show user as deleted (don't delete the history)
            if (Schema::hasTable('case_updates')) {
                // Don't set to null, instead we could add a 'deleted_user_name' field
                // For now, we'll leave the user_id as is for historical purposes
                // CaseUpdate::where('user_id', $user->id)->update(['user_id' => null]);
            }

        } catch (\Exception $e) {
            Log::error('Error handling user deletion cleanup', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'error' => $e->getMessage(),
            ]);

            $result['success'] = false;
            $result['messages'][] = 'Warning: Some data cleanup may not have completed properly.';
        }

        return $result;
    }

    /**
     * Display case management page
     */
    public function manageCases(Request $request): View
    {
        // Build query for cases
        $query = CaseModel::with(['socialWorker', 'policeOfficer']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('child_name', 'like', "%{$search}%")
                  ->orWhere('reporter_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('abuse_type')) {
            $query->where('abuse_type', $request->abuse_type);
        }

        if ($request->filled('social_worker')) {
            $query->where('social_worker_id', $request->social_worker);
        }

        if ($request->filled('police_officer')) {
            $query->where('police_officer_id', $request->police_officer);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        // Get paginated results
        $cases = $query->orderBy('created_at', 'desc')->paginate(25);

        // Get filter options
        $socialWorkers = User::where('role', 'social_worker')->orderBy('name')->get();
        $policeOfficers = User::where('role', 'police_officer')->orderBy('name')->get();

        // Calculate statistics
        $totalCases = CaseModel::count();
        $activeCases = CaseModel::whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCases = CaseModel::whereIn('status', ['resolved', 'closed'])->count();
        $criticalCases = CaseModel::where('priority', 'critical')
            ->whereNotIn('status', ['resolved', 'closed'])->count();

        $viewData = [
            'meta_title' => 'Case Management | CAMS',
            'meta_desc' => 'Manage and oversee all child abuse cases in the system.',
            'meta_image' => url('logo.png'),
            'cases' => $cases,
            'socialWorkers' => $socialWorkers,
            'policeOfficers' => $policeOfficers,
            'totalCases' => $totalCases,
            'activeCases' => $activeCases,
            'resolvedCases' => $resolvedCases,
            'criticalCases' => $criticalCases,
        ];

        return view('admin.cases', $viewData);
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

        return view('admin.profile', $viewData);
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

        Log::info('Admin profile updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'updated_fields' => array_keys($data),
        ]);

        return redirect()->route('admin.profile')
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

        Log::info('Admin password updated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password updated successfully.');
    }

    public function removeAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            Log::info('Admin avatar removed', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('admin.profile')
                ->with('success', 'Avatar removed successfully.');
        }

        return redirect()->route('admin.profile')
            ->with('error', 'No avatar to remove.');
    }


/**
 * Show case details (for modal)
 */
public function showCase(string $id): View
{
    $case = CaseModel::with(['socialWorker', 'policeOfficer', 'updates.user'])->findOrFail($id);

    return view('admin.cases.show', compact('case'));
}

/**
 * Get case data (for AJAX)
 */
public function getCaseData(string $id): JsonResponse
{
    try {
        $case = CaseModel::with(['socialWorker', 'policeOfficer'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'case' => [
                'id' => $case->id,
                'case_number' => $case->case_number,
                'child_name' => $case->child_name,
                'status' => $case->status,
                'priority' => $case->priority,
                'abuse_type' => $case->abuse_type,
                'social_worker_id' => $case->social_worker_id,
                'police_officer_id' => $case->police_officer_id,
                'description' => $case->description,
                'location' => $case->location,
                'date_reported' => $case->date_reported->format('Y-m-d'),
                'social_worker' => $case->socialWorker ? $case->socialWorker->name : null,
                'police_officer' => $case->policeOfficer ? $case->policeOfficer->name : null,
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to get case data', [
            'case_id' => $id,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to load case data.',
        ], 500);
    }
}

/**
 * Assign case to social worker and/or police officer
 */
/**
 * Assign case to social worker and/or police officer
 */
public function assignCase(Request $request, string $id): RedirectResponse
{
    $case = CaseModel::findOrFail($id);

    $request->validate([
        'social_worker_id' => ['nullable', 'exists:users,id'],
        'police_officer_id' => ['nullable', 'exists:users,id'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ]);

    // Validate that selected users have correct roles
    if ($request->filled('social_worker_id')) {
        $socialWorker = User::where('id', $request->social_worker_id)
            ->where('role', 'social_worker')
            ->first();

        if (!$socialWorker) {
            return redirect()->back()->with('error', 'Invalid social worker selected.');
        }
    }

    if ($request->filled('police_officer_id')) {
        $policeOfficer = User::where('id', $request->police_officer_id)
            ->where('role', 'police_officer')
            ->first();

        if (!$policeOfficer) {
            return redirect()->back()->with('error', 'Invalid police officer selected.');
        }
    }

    try {
        DB::beginTransaction();

        $oldSocialWorkerId = $case->social_worker_id;
        $oldPoliceOfficerId = $case->police_officer_id;

        // Update case assignments
        $case->update([
            'social_worker_id' => $request->social_worker_id,
            'police_officer_id' => $request->police_officer_id,
        ]);

        // Create case update record
        $updateNotes = [];

        if ($oldSocialWorkerId != $request->social_worker_id) {
            if ($request->social_worker_id) {
                $socialWorker = User::find($request->social_worker_id);
                $updateNotes[] = "Assigned to social worker: {$socialWorker->name}";
            } else {
                $updateNotes[] = "Unassigned from social worker";
            }
        }

        if ($oldPoliceOfficerId != $request->police_officer_id) {
            if ($request->police_officer_id) {
                $policeOfficer = User::find($request->police_officer_id);
                $updateNotes[] = "Assigned to police officer: {$policeOfficer->name}";
            } else {
                $updateNotes[] = "Unassigned from police officer";
            }
        }

        if ($request->filled('notes')) {
            $updateNotes[] = "Notes: " . $request->notes;
        }

        if (!empty($updateNotes)) {
            CaseUpdate::create([
                'case_id' => $case->id,
                'user_id' => Auth::id(),
                'update_type' => 'assignment',
                'description' => implode('. ', $updateNotes),
            ]);
        }

        // Send notifications to assigned users safely
        $this->sendAssignmentNotifications($case, $request, $oldSocialWorkerId, $oldPoliceOfficerId);

        DB::commit();

        Log::info('Admin assigned case', [
            'admin_id' => Auth::id(),
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'social_worker_id' => $request->social_worker_id,
            'police_officer_id' => $request->police_officer_id,
        ]);

        return redirect()->route('admin.cases')
            ->with('success', "Case {$case->case_number} has been assigned successfully.");

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to assign case', [
            'admin_id' => Auth::id(),
            'case_id' => $case->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()
            ->with('error', 'Failed to assign case. Please try again.');
    }
}

/**
 * Send assignment notifications safely
 */
private function sendAssignmentNotifications($case, $request, $oldSocialWorkerId, $oldPoliceOfficerId)
{
    try {
        // Check if notifications table exists
        if (!Schema::hasTable('cams_notifications')) {
            Log::info('Notifications table does not exist, skipping notifications');
            return;
        }

        // Send notification to newly assigned social worker
        if ($request->social_worker_id && $request->social_worker_id != $oldSocialWorkerId) {
            try {
                CamsNotification::create([
                    'user_id' => $request->social_worker_id,
                    'title' => 'New Case Assignment',
                    'message' => "You have been assigned to case {$case->case_number}: {$case->child_name}",
                    'type' => 'case_assignment',
                    'data' => json_encode(['case_id' => $case->id]),
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create notification for social worker', [
                    'user_id' => $request->social_worker_id,
                    'case_id' => $case->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send notification to newly assigned police officer
        if ($request->police_officer_id && $request->police_officer_id != $oldPoliceOfficerId) {
            try {
                CamsNotification::create([
                    'user_id' => $request->police_officer_id,
                    'title' => 'New Case Assignment',
                    'message' => "You have been assigned to case {$case->case_number}: {$case->child_name}",
                    'type' => 'case_assignment',
                    'data' => json_encode(['case_id' => $case->id]),
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create notification for police officer', [
                    'user_id' => $request->police_officer_id,
                    'case_id' => $case->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    } catch (\Exception $e) {
        Log::warning('Failed to send assignment notifications', [
            'case_id' => $case->id,
            'error' => $e->getMessage(),
        ]);
    }
}

/**
 * Update case status and priority
 */
/**
 * Update case status and priority
 */
public function updateCaseStatus(Request $request, string $id): RedirectResponse
{
    $case = CaseModel::findOrFail($id);

    $request->validate([
        'status' => ['required', 'string', 'in:' . implode(',', array_keys(CaseModel::getStatuses()))],
        'priority' => ['required', 'string', 'in:' . implode(',', array_keys(CaseModel::getPriorities()))],
        'notes' => ['nullable', 'string', 'max:1000'],
    ]);

    try {
        DB::beginTransaction();

        $oldStatus = $case->status;
        $oldPriority = $case->priority;

        // Update case
        $case->update([
            'status' => $request->status,
            'priority' => $request->priority,
        ]);

        // Create case update record
        $updateNotes = [];

        if ($oldStatus != $request->status) {
            $statusDisplay = CaseModel::getStatuses()[$request->status];
            $updateNotes[] = "Status changed to: {$statusDisplay}";
        }

        if ($oldPriority != $request->priority) {
            $priorityDisplay = CaseModel::getPriorities()[$request->priority];
            $updateNotes[] = "Priority changed to: {$priorityDisplay}";
        }

        if ($request->filled('notes')) {
            $updateNotes[] = "Notes: " . $request->notes;
        }

        if (!empty($updateNotes)) {
            CaseUpdate::create([
                'case_id' => $case->id,
                'user_id' => Auth::id(),
                'update_type' => 'status_change',
                'description' => implode('. ', $updateNotes),
            ]);
        }

        // Send notifications to assigned users if status changed significantly
        if ($oldStatus != $request->status) {
            $this->sendCaseStatusNotifications($case, $request->status);
        }

        DB::commit();

        Log::info('Admin updated case status', [
            'admin_id' => Auth::id(),
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'old_priority' => $oldPriority,
            'new_priority' => $request->priority,
        ]);

        return redirect()->route('admin.cases')
            ->with('success', "Case {$case->case_number} has been updated successfully.");

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to update case status', [
            'admin_id' => Auth::id(),
            'case_id' => $case->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()
            ->with('error', 'Failed to update case. Please try again.');
    }
}

/**
 * Send case status notifications safely
 */
private function sendCaseStatusNotifications($case, $newStatus)
{
    try {
        // Check if notifications table exists and get its structure
        if (!Schema::hasTable('cams_notifications')) {
            Log::info('Notifications table does not exist, skipping notifications');
            return;
        }

        // Get the column type for user_id
        $columns = Schema::getColumnListing('cams_notifications');
        if (!in_array('user_id', $columns)) {
            Log::info('user_id column does not exist in notifications table');
            return;
        }

        $notificationUsers = [];

        if ($case->social_worker_id) {
            $notificationUsers[] = $case->social_worker_id;
        }

        if ($case->police_officer_id) {
            $notificationUsers[] = $case->police_officer_id;
        }

        foreach ($notificationUsers as $userId) {
            try {
                // Try to create notification - if it fails due to type mismatch, log and continue
                CamsNotification::create([
                    'user_id' => $userId,
                    'title' => 'Case Status Updated',
                    'message' => "Case {$case->case_number} status changed to: " . CaseModel::getStatuses()[$newStatus],
                    'type' => 'case_update',
                    'data' => json_encode(['case_id' => $case->id]),
                ]);
            } catch (\Exception $e) {
                // Log the notification error but don't fail the whole operation
                Log::warning('Failed to create notification for user', [
                    'user_id' => $userId,
                    'case_id' => $case->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    } catch (\Exception $e) {
        Log::warning('Failed to send case status notifications', [
            'case_id' => $case->id,
            'error' => $e->getMessage(),
        ]);
    }
}

/**
 * Delete a case
 */
public function deleteCase(string $id): RedirectResponse
{
    $case = CaseModel::findOrFail($id);

    try {
        DB::beginTransaction();

        $caseNumber = $case->case_number;
        $childName = $case->child_name;

        // Delete related records
        CaseUpdate::where('case_id', $case->id)->delete();
        CamsNotification::where('data->case_id', $case->id)->delete();

        // Delete evidence files if they exist
        if ($case->evidence_files) {
            $evidenceFiles = json_decode($case->evidence_files, true);
            if (is_array($evidenceFiles)) {
                foreach ($evidenceFiles as $file) {
                    if (Storage::disk('private')->exists($file)) {
                        Storage::disk('private')->delete($file);
                    }
                }
            }
        }

        // Delete the case
        $case->delete();

        DB::commit();

        Log::info('Admin deleted case', [
            'admin_id' => Auth::id(),
            'case_number' => $caseNumber,
            'child_name' => $childName,
        ]);

        return redirect()->route('admin.cases')
            ->with('success', "Case {$caseNumber} has been deleted successfully.");

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to delete case', [
            'admin_id' => Auth::id(),
            'case_id' => $case->id,
            'error' => $e->getMessage(),
        ]);

        return redirect()->back()
            ->with('error', 'Failed to delete case. Please try again.');
    }
}
}
