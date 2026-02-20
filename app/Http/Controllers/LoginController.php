<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the email-only login page.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle POST /login - receive email, validate and redirect to Microsoft.
     */
    public function submitEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower($request->input('email'));

        // Allowed domain from .env (set ALLOWED_EMAIL_DOMAIN)
        $allowedDomain = strtolower(env('ALLOWED_EMAIL_DOMAIN', ''));

        if (empty($allowedDomain)) {
            // Safety: if not set, deny and tell dev/admin to configure
            return back()->with('error', 'Login domain is not configured. Contact admin.');
        }

        // Ensure email has allowed domain
        if (! str_ends_with($email, '@' . $allowedDomain)) {
            return back()->with('error', "Please use your company email (@$allowedDomain).");
        }

        // Ensure user exists in database (admin-created)
        $userExists = User::where('email', $email)->exists();
        if (! $userExists) {
            return back()->with('error', 'Your account is not registered. Contact administrator.');
        }

        // Save the provided email to session to compare after Microsoft callback
        Session::put('login_email', $email);

        // Redirect to Microsoft OAuth (this route will call Socialite)
        return redirect()->route('microsoft.redirect');
    }

    /**
     * Logout handler
     */
    public function logout()
    {
        auth()->logout();
        Session::forget('login_email');
        return redirect()->route('login');
    }
}
