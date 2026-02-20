<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller; // <--- ADD THIS LINE (Line 11)

class MicrosoftController extends Controller
{
    /**
     * Redirect to Microsoft for authentication.
     */
    public function redirect()
    {
        $email = Session::get('login_email', null);
        $allowedDomain = env('ALLOWED_EMAIL_DOMAIN', null);

        $with = [];
        if ($email) {
            $with['login_hint'] = $email;
        }
        if ($allowedDomain) {
            $with['domain_hint'] = $allowedDomain;
        }

        return Socialite::driver('microsoft')
            ->with($with)
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Handle callback from Microsoft.
     */
    public function callback(Request $request)
    {
        try {
            $msUser = Socialite::driver('microsoft')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Microsoft login failed: ' . $e->getMessage());
        }

        $email = strtolower($msUser->getEmail() ?? $msUser->email ?? '');
        $sessionEmail = strtolower(Session::get('login_email', ''));

        if (empty($sessionEmail)) {
            return redirect()->route('login')->with('error', 'Invalid login flow. Please enter your work email and continue.');
        }

        if ($email !== $sessionEmail) {
            Session::forget('login_email');
            return redirect()->route('login')->with('error', 'Email mismatch. Please try again with the same email.');
        }

        $allowedDomain = strtolower(env('ALLOWED_EMAIL_DOMAIN', ''));
        if (! empty($allowedDomain) && ! str_ends_with($email, '@' . $allowedDomain)) {
            Session::forget('login_email');
            return redirect()->route('login')->with('error', "Only @$allowedDomain accounts can sign in.");
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            Session::forget('login_email');
            return redirect()->route('login')->with('error', 'Your account is not registered. Contact administrator.');
        }

        Auth::login($user, true);
        Session::forget('login_email');

        return redirect()->intended('/dashboard');
    }
}
