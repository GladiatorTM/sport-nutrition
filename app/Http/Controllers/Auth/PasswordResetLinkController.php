<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Notifications\OneTimePasswordNotification;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $throttleKey = 'password_reset_first_try_' . md5($request->email);
        $waitSeconds = 60;
        $now = now()->timestamp;
        $firstTry = session($throttleKey);
        if ($firstTry && ($now - $firstTry < $waitSeconds)) {
            $secondsLeft = $waitSeconds - ($now - $firstTry);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Будь ласка, зачекайте ' . $secondsLeft . ' сек. перед повторною спробою.']);
        }
        if (!$firstTry) {
            session([$throttleKey => $now]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
