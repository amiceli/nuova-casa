<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TagController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// app

Route::middleware(array('auth'))
    ->controller(AppController::class)
    ->group(function () {
        Route::get('dashboard', 'dashboard')
            ->name('dashboard');
        Route::get('/api/search', 'searXng')
            ->name('sear-xng');
        Route::get('proxy', 'proxy')
            ->name('proxy');
        Route::get('/search', 'search')
            ->name('search');
    });

// onboarding

Route::middleware(array('auth'))
    ->controller(OnboardingController::class)
    ->group(function () {
        Route::get('/onboarding', 'show')
            ->name('onboarding');
        Route::post('/onboarding/complete', 'complete')
            ->name('complete-onboarding');
        Route::post('/api/theme', 'updateTheme')
            ->name('update-theme');
    });

// bookmarks import

Route::middleware(array('auth'))
    ->controller(ImportController::class)
    ->group(function () {
        Route::post('/api/bookmarks/import', 'store')
            ->name('import-bookmarks');
    });

// tags

Route::middleware(array('auth'))
    ->controller(TagController::class)
    ->group(function () {
        Route::get('/tags', 'index')
            ->name('tags');
        Route::delete('/api/tag/{tag}', 'destroy')
            ->name('delete-tag');
        Route::post('/api/tags', 'store')
            ->name('create-tag');
        Route::get('/api/tags/name-used', 'nameAlreadyUsed')
            ->name('tag-name-used');
        Route::get('/tags/{tag}', 'show')
            ->name('tag');
    });

// pages

Route::middleware(array('auth'))
    ->controller(PageController::class)
    ->group(function () {
        Route::post('/api/pages', 'store')
            ->name('create-page');
        Route::delete('/api/pages/{page}', 'destroy')
            ->name('delete-page');
        Route::put('/api/pages/{page}', 'update')
            ->name('update-page');
        Route::get('/api/page/graph', 'openGraph')
            ->name('open-graph');
    });

// rss

Route::middleware(array('auth'))
    ->controller(NewsletterController::class)
    ->group(function () {
        Route::get('/newsletters', 'index')
            ->name('rss-list');
        Route::post('/newsletters', 'store')
            ->name('create-rss');
        Route::get('/api/newsletters/available', 'available')
            ->name('available-newsletters');
        Route::get('/api/newsletters/available/{availableNewsletter}/feed', 'feed')
            ->name('available-newsletter-feed');
        Route::post('/api/newsletters/follow', 'follow')
            ->name('follow-newsletters');
        Route::post('/api/newsletters/{newsletter}/read', 'markAsRead')
            ->name('read-newsletter');
    });

Route::get('/auth/redirect', function () {
    return Socialite::driver('github')
        ->scopes(array('read:user', 'public_repo'))
        ->redirect();
})->name('auth-redirect');

Route::get('/auth/callback', function (Request $request) {
    try {
        $githubUser = Socialite::driver('github')->user();
    } catch (InvalidStateException $e) {
        Log::warning('Socialite InvalidStateException', array(
            'session_state' => $request->session()->get('state'),
            'request_state' => $request->input('state'),
            'has_session_cookie' => $request->hasCookie(config('session.cookie')),
            'session_id' => $request->session()->getId(),
            'url' => $request->fullUrl(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'forwarded_proto' => $request->headers->get('x-forwarded-proto'),
            'forwarded_host' => $request->headers->get('x-forwarded-host'),
            'referer' => $request->headers->get('referer'),
            'user_agent' => $request->userAgent(),
        ));

        abort(419, 'Session expirée pendant l\'authentification GitHub. Merci de réessayer.');
    }

    $user = User::updateOrCreate(array(
        'email' => $githubUser->email,
    ), array(
        'github_id' => $githubUser->id,
        'name' => $githubUser->name,
        'avatar' => $githubUser->avatar,
        'github_token' => $githubUser->token,
        'github_refresh_token' => $githubUser->refreshToken,
    ));

    Auth::login($user);

    return redirect('/dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
