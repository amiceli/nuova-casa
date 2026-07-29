<?php

use App\Enums\Theme;
use App\Models\User;

test('a user who has never been through the onboarding is taken to it', function () {
    $user = User::factory()->onboarding()->create();

    $this->actingAs($user);

    $this->get(route('dashboard'))->assertRedirect(route('onboarding'));
});

test('a user who is done with the onboarding browses the app', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('dashboard'))->assertStatus(200);
});

test('the onboarding is not played again once it is over', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('onboarding'))->assertRedirect(route('dashboard'));
});

test('finishing the onboarding remembers when it happened', function () {
    $user = User::factory()->onboarding()->create();

    $this->actingAs($user);

    $this->post(route('complete-onboarding'))->assertRedirect(route('dashboard'));

    expect($user->refresh()->onboarding_completed_at)->not->toBeNull();
});

test('the theme a user picks is kept on their account', function () {
    $user = User::factory()->onboarding()->create();

    $this->actingAs($user);

    $response = $this->postJson(route('update-theme'), array('theme' => 'dark'));

    $response->assertStatus(200);
    $response->assertJson(array('theme' => 'dark'));

    expect($user->refresh()->theme)->toBe(Theme::Dark);
});

test('a theme the app does not know is refused', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->postJson(route('update-theme'), array('theme' => 'neon'))
        ->assertStatus(422);

    expect($user->refresh()->theme)->toBe(Theme::System);
});

test('the onboarding never shows up for a guest', function () {
    $this->get(route('onboarding'))->assertRedirect('/login');
});
