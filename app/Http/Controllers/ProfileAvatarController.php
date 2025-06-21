<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileAvatarController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);
        $user = Auth::user();
        // Видаляємо старий аватар, якщо є
        if ($user->avatar && file_exists(public_path('avatars/' . $user->avatar))) {
            unlink(public_path('avatars/' . $user->avatar));
        }
        $file = $request->file('avatar');
        $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('avatars'), $filename);
        $user->avatar = $filename;
        $user->save();
        return back()->with('success', 'Аватар оновлено!');
    }
} 