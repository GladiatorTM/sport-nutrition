<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::firstOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'password' => bcrypt(str()->random(16)),
            ]);
            Auth::login($user, true);
            return redirect('/')->with('success', 'Ви успішно увійшли через Google!');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Не вдалося авторизуватися через Google.');
        }
    }
} 