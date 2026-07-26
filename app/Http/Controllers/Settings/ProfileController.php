<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller {
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response {
        return Inertia::render('settings/Profile', array(
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ));
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validate(array(
            'confirmation' => array('required', function ($attribute, $value, $fail) {
                $expected = auth()->user()->name.'/'.auth()->user()->email;
                if (trim($value) !== $expected) {
                    $fail('profile_confirmation_invalid');
                }
            }),
        ), array(
            'confirmation.required' => 'profile_confirmation_required',
        ));

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
