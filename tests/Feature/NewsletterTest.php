<?php

use App\Models\AvailableNewsletter;
use App\Models\Newsletter;
use App\Models\User;

function followedNewsletter(User $user): Newsletter {
    return Newsletter::create(array(
        'title' => 'Labnotes',
        'url' => 'https://labnotes.org/rss',
        'user_id' => $user->id,
    ));
}

test('a user marks a newsletter as read with the item they opened', function () {
    $user = User::factory()->create();
    $newsletter = followedNewsletter($user);

    $this->actingAs($user);

    $response = $this->postJson(route('read-newsletter', array('newsletter' => $newsletter->id)), array(
        'link' => 'https://labnotes.org/last-item',
        'title' => 'Last item',
    ));

    $response->assertStatus(200);
    $response->assertJson(array('isRead' => true));

    $newsletter->refresh();

    expect($newsletter->last_read_link)->toBe('https://labnotes.org/last-item')
        ->and($newsletter->last_read_title)->toBe('Last item')
        ->and($newsletter->last_read_at)->not->toBeNull();
});

test('a newsletter is read only while its last item is the one the user opened', function () {
    $user = User::factory()->create();
    $newsletter = followedNewsletter($user);

    $newsletter->markAsRead(array(
        'link' => 'https://labnotes.org/last-item',
        'title' => 'Last item',
    ));

    expect($newsletter->hasRead(array('link' => 'https://labnotes.org/last-item')))->toBeTrue()
        ->and($newsletter->hasRead(array('link' => 'https://labnotes.org/newer-item')))->toBeFalse()
        ->and($newsletter->hasRead(null))->toBeFalse();
});

test('a user cannot mark the newsletter of another user as read', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $newsletter = followedNewsletter($owner);

    $this->actingAs($other);

    $response = $this->postJson(route('read-newsletter', array('newsletter' => $newsletter->id)), array(
        'link' => 'https://labnotes.org/last-item',
    ));

    $response->assertStatus(403);

    expect($newsletter->refresh()->last_read_at)->toBeNull();
});

test('the catalog flags the newsletters the user already follows', function () {
    $user = User::factory()->create();

    $followed = AvailableNewsletter::create(array(
        'name' => 'Labnotes',
        'url' => 'https://labnotes.org/',
        'feed_url' => 'https://labnotes.org/rss',
    ));
    AvailableNewsletter::create(array(
        'name' => 'JavaScript Weekly',
        'url' => 'https://javascriptweekly.com/',
    ));

    Newsletter::create(array(
        'title' => 'Labnotes',
        'url' => 'https://labnotes.org/rss',
        'user_id' => $user->id,
        'available_newsletter_id' => $followed->id,
    ));

    $this->actingAs($user);

    $response = $this->getJson(route('available-newsletters'));

    $response->assertStatus(200);
    $response->assertJsonPath('newsletters.1.name', 'Labnotes');
    $response->assertJsonPath('newsletters.1.followed', true);
    $response->assertJsonPath('newsletters.0.name', 'JavaScript Weekly');
    $response->assertJsonPath('newsletters.0.followed', false);
});

test('the feed of a newsletter of the catalog is served without looking for it again', function () {
    $user = User::factory()->create();

    $available = AvailableNewsletter::create(array(
        'name' => 'Labnotes',
        'url' => 'https://labnotes.org/',
        'feed_url' => 'https://labnotes.org/rss',
    ));

    $this->actingAs($user);

    $response = $this->getJson(route('available-newsletter-feed', array('availableNewsletter' => $available->id)));

    $response->assertStatus(200);
    $response->assertJson(array('feedUrl' => 'https://labnotes.org/rss'));
});

test('two users can follow the same newsletter', function () {
    $first = User::factory()->create();
    $second = User::factory()->create();

    followedNewsletter($first);
    followedNewsletter($second);

    expect(Newsletter::where('url', 'https://labnotes.org/rss')->count())->toBe(2);
});

test('guests cannot reach the newsletter list', function () {
    $this->get(route('rss-list'))->assertRedirect('/login');
});
