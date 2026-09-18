<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/settings/profile');

    $response->assertOk();
});

test('user can export their data in three formats', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/profile/export.json')
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="nuova-casa-export.json"')
        ->assertJsonPath('user.email', $user->email);

    $this->actingAs($user)
        ->get('/settings/profile/export-browser.json')
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="nuova-casa-bookmarks.json"')
        ->assertJsonPath('version', 1)
        ->assertJsonPath('roots.bookmark_bar.type', 'folder');

    $this->actingAs($user)
        ->get('/settings/profile/export.html')
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="nuova-casa-bookmarks.html"')
        ->assertSee('<!DOCTYPE NETSCAPE-Bookmark-file-1>', false);

    $this->actingAs($user)
        ->get('/settings/profile/export-styled.html')
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="nuova-casa-bookmarks-styled.html"')
        ->assertSee('https://chr15m.github.io/DoodleCSS/doodle.css', false)
        ->assertSee('max-width: 900px', false);
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
