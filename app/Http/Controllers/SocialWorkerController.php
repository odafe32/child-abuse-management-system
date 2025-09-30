<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CaseModel;
use App\Models\CaseUpdate;
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

class SocialWorkerController extends Controller
{
    /**
     * Show the social worker dashboard
     */
    public function showDashboard(): View
    {
        $user = Auth::user();

        // Get dashboard statistics
        $stats = [
            'total_cases' => CaseModel::bySocialWorker($user->id)->count(),
            'active_cases' => CaseModel::bySocialWorker($user->id)->active()->count(),
            'completed_cases' => CaseModel::bySocialWorker($user->id)->whereIn('status', ['resolved', 'closed'])->count(),
            'overdue_cases' => CaseModel::bySocialWorker($user->id)->overdue()->count(),
            'high_priority_cases' => CaseModel::bySocialWorker($user->id)->byPriority('high')->active()->count(),
            'critical_cases' => CaseModel::bySocialWorker($user->id)->byPriority('critical')->active()->count(),
        ];

        // Get recent cases
        $recentCases = CaseModel::bySocialWorker($user->id)
            ->with(['policeOfficer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get overdue cases
        $overdueCases = CaseModel::bySocialWorker($user->id)
            ->overdue()
            ->with(['policeOfficer'])
            ->orderBy('last_updated', 'asc')
            ->limit(5)
            ->get();

        $viewData = [
            'meta_title' => 'Dashboard | CAMS',
            'meta_desc' => 'CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.',
            'meta_image' => url('logo.png'),
            'user' => $user,
            'stats' => $stats,
            'recentCases' => $recentCases,
            'overdueCases' => $overdueCases,
        ];

        return view('social-worker.dashboard', $viewData);
    }

    /**
     * Show all cases
     */
    public function showCases(Request $request): View
    {
        $user = Auth::user();
        $query = CaseModel::bySocialWorker($user->id)->with(['policeOfficer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('abuse_type')) {
            $query->byAbuseType($request->abuse_type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $cases = $query->paginate(15)->withQueryString();

        $viewData = [
            'meta_title' => 'Cases | CAMS',
            'meta_desc' => 'Manage and track abuse cases.',
            'meta_image' => url('logo.png'),
            'cases' => $cases,
            'filters' => $request->only(['status', 'abuse_type', 'priority', 'search', 'sort_by', 'sort_order']),
            'abuseTypes' => CaseModel::getAbuseTypes(),
            'statuses' => CaseModel::getStatuses(),
            'priorities' => CaseModel::getPriorities(),
        ];

        return view('social-worker.cases.index', $viewData);
    }

    /**
     * Show create case form
     */
    public function showCreateCase(): View
    {
        $viewData = [
            'meta_title' => 'Add New Case | CAMS',
            'meta_desc' => 'Register a new abuse case.',
            'meta_image' => url('logo.png'),
            'abuseTypes' => CaseModel::getAbuseTypes(),
            'priorities' => CaseModel::getPriorities(),
            'genders' => CaseModel::getGenders(),
        ];

        return view('social-worker.cases.create', $viewData);
    }

    /**
     * Store a new case
     */
    public function storeCase(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'abuse_type' => ['required', 'in:' . implode(',', array_keys(CaseModel::getAbuseTypes()))],
            'description' => ['required', 'string', 'min:10'],
            'date_reported' => ['required', 'date', 'before_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:' . implode(',', array_keys(CaseModel::getPriorities()))],

            // Child details
            'child_name' => ['required', 'string', 'max:255'],
            'child_dob' => ['nullable', 'date', 'before:today'],
            'child_age' => ['nullable', 'integer', 'min:0', 'max:18'],
            'child_gender' => ['required', 'in:' . implode(',', array_keys(CaseModel::getGenders()))],
            'child_address' => ['required', 'string'],
            'child_school' => ['nullable', 'string', 'max:255'],
            'child_class' => ['nullable', 'string', 'max:100'],
            'medical_conditions' => ['nullable', 'string'],
            'injuries_description' => ['nullable', 'string'],

            // Reporter details
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_relationship' => ['required', 'string', 'max:255'],
            'reporter_phone' => ['required', 'string', 'max:20'],
            'reporter_address' => ['required', 'string'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
                // Offender validation
        'offender_known' => ['required', 'boolean'],
        'offender_name' => ['nullable', 'string', 'max:255'],
        'offender_relationship' => ['nullable', 'string', 'in:parent,step_parent,relative,family_friend,teacher,stranger,other'],
        'offender_description' => ['nullable', 'string'],
        ]);

        // Add social worker ID
        $validated['social_worker_id'] = $user->id;

        // Create the case
        $case = CaseModel::create($validated);

        // Log the case creation
        CaseUpdate::create([
            'case_id' => $case->id,
            'user_id' => $user->id,
            'update_type' => 'case_created',
            'description' => 'Case created and registered in the system.',
        ]);

        Log::info('New case created', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'social_worker_id' => $user->id,
            'abuse_type' => $case->abuse_type,
        ]);

        return redirect()->route('social-worker.cases.show', $case->id)
            ->with('success', 'Case has been successfully registered with case number: ' . $case->case_number);
    }

    /**
     * Show case details
     */
    public function showCase(CaseModel $case): View
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this case.');
        }

        $case->load(['socialWorker', 'policeOfficer', 'updates.user']);

        // Get available police officers for assignment
        $policeOfficers = User::policeOfficers()->active()->get();

        $viewData = [
            'meta_title' => 'Case Details | CAMS',
            'meta_desc' => 'View and manage case details.',
            'meta_image' => url('logo.png'),
            'case' => $case,
            'policeOfficers' => $policeOfficers,
            'statuses' => CaseModel::getStatuses(),
            'priorities' => CaseModel::getPriorities(),
        ];

        return view('social-worker.cases.show', $viewData);
    }

    /**
     * Print case details
     */
    public function printCase(CaseModel $case): View
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to print this case.');
        }

        $case->load(['socialWorker', 'policeOfficer', 'updates.user']);

        $viewData = [
            'case' => $case,
            'printDate' => now(),
        ];

        return view('social-worker.cases.print', $viewData);
    }

    /**
     * Show edit case form
     */
    public function editCase(CaseModel $case): View
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to edit this case.');
        }

        $viewData = [
            'meta_title' => 'Edit Case | CAMS',
            'meta_desc' => 'Edit case information.',
            'meta_image' => url('logo.png'),
            'case' => $case,
            'abuseTypes' => CaseModel::getAbuseTypes(),
            'priorities' => CaseModel::getPriorities(),
            'genders' => CaseModel::getGenders(),
        ];

        return view('social-worker.cases.edit', $viewData);
    }

    /**
     * Update case
     */
    public function updateCase(Request $request, CaseModel $case): RedirectResponse
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to update this case.');
        }

        $validated = $request->validate([
            'abuse_type' => ['required', 'in:' . implode(',', array_keys(CaseModel::getAbuseTypes()))],
            'description' => ['required', 'string', 'min:10'],
            'date_reported' => ['required', 'date', 'before_or_equal:today'],
            'location' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:' . implode(',', array_keys(CaseModel::getPriorities()))],

            // Child details
            'child_name' => ['required', 'string', 'max:255'],
            'child_dob' => ['nullable', 'date', 'before:today'],
            'child_age' => ['nullable', 'integer', 'min:0', 'max:18'],
            'child_gender' => ['required', 'in:' . implode(',', array_keys(CaseModel::getGenders()))],
            'child_address' => ['required', 'string'],
            'child_school' => ['nullable', 'string', 'max:255'],
            'child_class' => ['nullable', 'string', 'max:100'],
            'medical_conditions' => ['nullable', 'string'],
            'injuries_description' => ['nullable', 'string'],

            // Reporter details
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_relationship' => ['required', 'string', 'max:255'],
            'reporter_phone' => ['required', 'string', 'max:20'],
            'reporter_address' => ['required', 'string'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
        ]);

        // Store old values for audit
        $oldValues = $case->only(array_keys($validated));

        // Update the case
        $case->update($validated);

        // Log the update
        CaseUpdate::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'update_type' => 'information_updated',
            'description' => 'Case information has been updated.',
            'old_values' => $oldValues,
            'new_values' => $validated,
        ]);

        Log::info('Case updated', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('social-worker.cases.show', $case->id)
            ->with('success', 'Case has been successfully updated.');
    }

    /**
     * Delete case
     */
    public function deleteCase(CaseModel $case): RedirectResponse
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this case.');
        }

        // Only allow deletion if case is in reported status and has no police officer assigned
        if ($case->status !== 'reported' || $case->police_officer_id) {
            return redirect()->route('social-worker.cases.show', $case->id)
                ->with('error', 'Cannot delete case. Case must be in "Reported" status and not assigned to police.');
        }

        $caseNumber = $case->case_number;

        // Log the deletion before deleting
        Log::info('Case deleted', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'deleted_by' => Auth::id(),
        ]);

        // Delete the case (cascade will handle updates)
        $case->delete();

        return redirect()->route('social-worker.cases.index')
            ->with('success', "Case {$caseNumber} has been successfully deleted.");
    }

    /**
     * Update case status
     */
    public function updateCaseStatus(Request $request, CaseModel $case): RedirectResponse
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to update this case.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(CaseModel::getStatuses()))],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $case->status;
        $case->update(['status' => $validated['status']]);

        // If closing the case, add closure notes
        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $case->update([
                'closure_notes' => $validated['notes'] ?? 'Case marked as ' . $validated['status'],
                'closed_at' => now(),
            ]);
        }

        // Log the status change
        CaseUpdate::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'update_type' => 'status_changed',
            'description' => "Status changed from '{$oldStatus}' to '{$validated['status']}'" .
                           ($validated['notes'] ? ". Notes: {$validated['notes']}" : ''),
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $validated['status']],
        ]);

        Log::info('Case status updated', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('social-worker.cases.show', $case->id)
            ->with('success', 'Case status has been updated successfully.');
    }

    /**
     * Assign police officer to case
     */
    public function assignPoliceOfficer(Request $request, CaseModel $case): RedirectResponse
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to assign police to this case.');
        }

        $validated = $request->validate([
            'police_officer_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        // Verify the selected user is a police officer
        $policeOfficer = User::find($validated['police_officer_id']);
        if (!$policeOfficer->isPoliceOfficer() || !$policeOfficer->is_active) {
            return back()->withErrors(['police_officer_id' => 'Selected user is not an active police officer.']);
        }

        $oldOfficer = $case->policeOfficer;
        $case->update([
            'police_officer_id' => $validated['police_officer_id'],
            'status' => 'assigned_to_police',
        ]);

        // Log the assignment
        CaseUpdate::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'update_type' => 'assigned_police',
            'description' => "Case assigned to Police Officer: {$policeOfficer->name}" .
                           ($validated['notes'] ? ". Notes: {$validated['notes']}" : ''),
            'old_values' => ['police_officer_id' => $oldOfficer?->id],
            'new_values' => ['police_officer_id' => $validated['police_officer_id']],
        ]);

        Log::info('Police officer assigned to case', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'police_officer_id' => $validated['police_officer_id'],
            'assigned_by' => Auth::id(),
        ]);

        return redirect()->route('social-worker.cases.show', $case->id)
            ->with('success', 'Police officer has been assigned to this case successfully.');
    }

    /**
     * Add note to case
     */
    public function addCaseNote(Request $request, CaseModel $case): RedirectResponse
    {
        // Ensure the case belongs to the current social worker
        if ($case->social_worker_id !== Auth::id()) {
            abort(403, 'You do not have permission to add notes to this case.');
        }

        $validated = $request->validate([
            'note' => ['required', 'string', 'min:5'],
        ]);

        // Add the note as a case update
        CaseUpdate::create([
            'case_id' => $case->id,
            'user_id' => Auth::id(),
            'update_type' => 'note_added',
            'description' => $validated['note'],
        ]);

        // Update the case's last_updated timestamp
        $case->touch();

        Log::info('Note added to case', [
            'case_id' => $case->id,
            'case_number' => $case->case_number,
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('social-worker.cases.show', $case->id)
            ->with('success', 'Note has been added to the case successfully.');
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

        return view('social-worker.profile', $viewData);
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

        Log::info('Social worker profile updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'updated_fields' => array_keys($data),
        ]);

        return redirect()->route('social-worker.profile')
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

        Log::info('Social worker password updated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('social-worker.profile')
            ->with('success', 'Password updated successfully.');
    }

    public function removeAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            Log::info('Social worker avatar removed', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('social-worker.profile')
                ->with('success', 'Avatar removed successfully.');
        }

        return redirect()->route('social-worker.profile')
            ->with('error', 'No avatar to remove.');
    }
}
