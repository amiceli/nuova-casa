<?php

use App\Enums\Theme;
use App\Models\Newsletter;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\FreshUserSeeder;
use Database\Seeders\OnboardedUserSeeder;

test('the onboarded seed fills the account and leaves the onboarding behind', function () {
    $user = User::factory()->onboarding()->create();

    $this->seed(OnboardedUserSeeder::class);

    expect(Tag::where('user_id', $user->id)->count())->toBeGreaterThan(0)
        ->and(Page::where('user_id', $user->id)->count())->toBeGreaterThan(0)
        ->and(Newsletter::where('user_id', $user->id)->count())->toBeGreaterThan(0)
        ->and($user->refresh()->onboarding_completed_at)->not->toBeNull();
});

test('the fresh seed empties the account and puts the onboarding back ahead', function () {
    $user = User::factory()->create(array('theme' => Theme::Dark));

    $this->seed(OnboardedUserSeeder::class);
    $this->seed(FreshUserSeeder::class);

    expect(Tag::where('user_id', $user->id)->count())->toBe(0)
        ->and(Page::where('user_id', $user->id)->count())->toBe(0)
        ->and(Newsletter::where('user_id', $user->id)->count())->toBe(0);

    $user->refresh();

    expect($user->onboarding_completed_at)->toBeNull()
        ->and($user->theme)->toBe(Theme::System);
});

test('a seed replayed twice leaves the same thing behind', function () {
    $user = User::factory()->create();

    $this->seed(OnboardedUserSeeder::class);
    $tags = Tag::where('user_id', $user->id)->count();
    $pages = Page::where('user_id', $user->id)->count();

    $this->seed(OnboardedUserSeeder::class);

    expect(Tag::where('user_id', $user->id)->count())->toBe($tags)
        ->and(Page::where('user_id', $user->id)->count())->toBe($pages);
});

test('a seed run before anyone logged in says so instead of blowing up', function () {
    $this->seed(OnboardedUserSeeder::class);
    $this->seed(FreshUserSeeder::class);

    expect(Tag::count())->toBe(0)
        ->and(Page::count())->toBe(0);
});
