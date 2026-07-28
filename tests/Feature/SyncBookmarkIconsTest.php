<?php

use App\Jobs\FindPageIcon;
use App\Jobs\FindTagIcon;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use App\Services\BookmarkImporter;
use Illuminate\Support\Facades\Queue;

function tagWithIcon(User $user, string $icon): Tag {
    $tag = new Tag();
    $tag->name = 'Tag '.uniqid();
    $tag->icon = $icon;
    $tag->user_id = $user->id;

    $tag->save();

    return $tag;
}

function pageWithIcon(Tag $tag, string $icon): Page {
    $page = new Page();
    $page->title = 'Page '.uniqid();
    $page->url = 'https://example.com/'.uniqid();
    $page->icon = $icon;
    $page->favorite = false;
    $page->tag_id = $tag->id;
    $page->user_id = $tag->user_id;

    $page->save();

    return $page;
}

test('only the tags and the pages still wearing the fallback icon are looked up', function () {
    Queue::fake();

    $user = User::factory()->create();

    $waiting = tagWithIcon($user, BookmarkImporter::FALLBACK_ICON);
    $done = tagWithIcon($user, 'https://example.com/logo.png');

    pageWithIcon($waiting, BookmarkImporter::FALLBACK_ICON);
    pageWithIcon($done, 'https://example.com/page.png');

    $this->artisan('bookmarks:sync-icons')->assertSuccessful();

    Queue::assertPushed(FindTagIcon::class, 1);
    Queue::assertPushed(FindPageIcon::class, 1);
    Queue::assertPushed(FindTagIcon::class, fn (FindTagIcon $job) => $job->tagId === $waiting->id);
});

test('the limit caps what a single run queues', function () {
    Queue::fake();

    $user = User::factory()->create();

    foreach (range(1, 4) as $ignored) {
        tagWithIcon($user, BookmarkImporter::FALLBACK_ICON);
    }

    $this->artisan('bookmarks:sync-icons --limit=2')->assertSuccessful();

    Queue::assertPushed(FindTagIcon::class, 2);
});
