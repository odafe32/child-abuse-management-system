<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin(): View
    {
        $viewData = [
            'meta_title' => 'Login | CAMS',
            'meta_desc' => 'CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.',
            'meta_image' => url('logo.png'),
        ];

        return view('auth.login', $viewData);
    }

    /**
     * Handle login request
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Add remember me if checked
        $remember = $request->boolean('remember');

        // Attempt to authenticate
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact administrator.',
                ]);
            }

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            // Update last login information
            $user->updateLastLogin($request->ip());

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect to appropriate dashboard based on role
            return redirect()->intended($this->getRedirectPath($user));
        }

        // Authentication failed
        Log::warning('Failed login attempt', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Log logout
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(): View
    {
        $viewData = [
            'meta_title' => 'Forgot Password | CAMS',
            'meta_desc' => 'Reset your CAMS account password securely.',
            'meta_image' => url('logo.png'),
        ];

        return view('auth.forgot-password', $viewData);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'We could not find a user with that email address.',
            ]);
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact administrator.',
            ]);
        }

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Log password reset request
        Log::info('Password reset requested', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'status' => $status,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show password reset form
     */
    public function showResetForm(Request $request, string $token): View
    {
        $viewData = [
            'meta_title' => 'Reset Password | CAMS',
            'meta_desc' => 'Create a new password for your CAMS account.',
            'meta_image' => url('logo.png'),
            'token' => $token,
            'email' => $request->email,
        ];

        return view('auth.reset-password', $viewData);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Log password reset
        Log::info('Password reset completed', [
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'status' => $status,
        ]);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset successfully. Please login with your new password.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Get redirect path based on user role
     */
    private function getRedirectPath(User $user): string
    {
        return match($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_SOCIAL_WORKER => route('social-worker.dashboard'),
            User::ROLE_POLICE_OFFICER => route('police.dashboard'),
            default => route('dashboard')
        };
    }
}
