<?php

namespace App\Http\Controllers;

use App\Enums\Theme;
use App\Http\Requests\UpdateThemeRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OnboardingController extends Controller {
    /**
     * The onboarding runs once, right after the first login.
     */
    public function show(Request $request) {
        if ($request->user()->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding');
    }

    /**
     * The theme lives on the user, so it follows them across browsers.
     */
    public function updateTheme(UpdateThemeRequest $request) {
        $data = (object) $request->validated();
        $user = $request->user();

        $user->theme = Theme::from($data->theme);

        $user->save();

        return response()->json(array(
            'theme' => $user->theme->value,
        ));
    }

    public function complete(Request $request) {
        $request->user()->completeOnboarding();

        return redirect()->route('dashboard');
    }
}
