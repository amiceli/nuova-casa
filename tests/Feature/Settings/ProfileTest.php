<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/profile');

    $response->assertOk();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/settings/profile', array(
            'confirmation' => $user->name.'/'.$user->email,
        ));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('confirmation is required to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/profile')
        ->delete('/settings/profile');

    $response
        ->assertSessionHasErrors(array('confirmation' => 'profile_confirmation_required'))
        ->assertRedirect('/settings/profile');

    expect($user->fresh())->not->toBeNull();
});

test('correct confirmation must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/profile')
        ->delete('/settings/profile', array(
            'confirmation' => 'wrong/confirmation',
        ));

    $response
        ->assertSessionHasErrors(array('confirmation' => 'profile_confirmation_invalid'))
        ->assertRedirect('/settings/profile');

    expect($user->fresh())->not->toBeNull();
});
