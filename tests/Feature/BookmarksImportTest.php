<?php

use App\Jobs\FindPageIcon;
use App\Jobs\FindTagIcon;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;

function bookmarksFile(): UploadedFile {
    return UploadedFile::fake()->createWithContent(
        'bookmarks.html',
        file_get_contents(base_path('tests/Fixtures/bookmarks.html'))
    );
}

function importBookmarks(User $user) {
    return test()->actingAs($user)->postJson(route('import-bookmarks'), array(
        'bookmarks' => bookmarksFile(),
        'default_tag' => 'Favoris',
    ));
}

test('the folders of an export become tags, and the links land in them', function () {
    Bus::fake();

    $user = User::factory()->create();

    importBookmarks($user)->assertStatus(200);

    $tags = Tag::where('user_id', $user->id)->pluck('name');

    expect($tags)->toContain('Dev')
        ->and($tags)->toContain('Dev / Front')
        ->and($tags)->toContain('Dev / Front / Vue')
        ->and($tags)->toContain('Docs / Frameworks / Astro')
        ->and($tags)->toContain('Favoris');

    $front = Tag::where('user_id', $user->id)->where('name', 'Dev / Front')->first();

    expect(Page::where('tag_id', $front->id)->pluck('url'))
        ->toContain('https://vitest.dev')
        ->toContain('https://vite.dev');
});

test('an import answers with what it created before looking for any icon', function () {
    Bus::fake();

    $user = User::factory()->create();

    $response = importBookmarks($user);

    $response->assertStatus(200);
    $response->assertJson(array(
        'tags' => 17,
        'pages' => 55,
        'skipped' => 0,
        'total' => 72,
    ));
});

test('every imported tag and link gets a job looking for its icon', function () {
    Bus::fake();

    $user = User::factory()->create();

    importBookmarks($user);

    Bus::assertBatched(function ($batch) {
        $tagJobs = $batch->jobs->filter(fn ($job) => $job instanceof FindTagIcon);
        $pageJobs = $batch->jobs->filter(fn ($job) => $job instanceof FindPageIcon);

        return $tagJobs->count() === 17 && $pageJobs->count() === 55;
    });
});

test('an import can be replayed without doubling anything', function () {
    Bus::fake();

    $user = User::factory()->create();

    importBookmarks($user);
    $response = importBookmarks($user);

    $response->assertJson(array('tags' => 0, 'pages' => 0, 'total' => 0));

    expect(Tag::where('user_id', $user->id)->count())->toBe(17)
        ->and(Page::where('user_id', $user->id)->count())->toBe(55);
});

test('two users importing the same bookmarks keep their own tags', function () {
    Bus::fake();

    $first = User::factory()->create();
    $second = User::factory()->create();

    importBookmarks($first);
    importBookmarks($second);

    expect(Tag::where('user_id', $first->id)->count())->toBe(17)
        ->and(Tag::where('user_id', $second->id)->count())->toBe(17);
});

test('a file holding no bookmark is turned down', function () {
    Bus::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('import-bookmarks'), array(
        'bookmarks' => UploadedFile::fake()->createWithContent('empty.html', '<html><body>nothing</body></html>'),
        'default_tag' => 'Favoris',
    ));

    $response->assertStatus(422);
    $response->assertJsonPath('errors.bookmarks.0', 'import_no_bookmark_found');

    Bus::assertNothingBatched();
});

test('a file that is not an export is turned down', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('import-bookmarks'), array(
        'bookmarks' => UploadedFile::fake()->create('bookmarks.pdf', 10),
        'default_tag' => 'Favoris',
    ));

    $response->assertStatus(422);
    $response->assertJsonPath('errors.bookmarks.0', 'import_file_invalid');
});

test('guests cannot import bookmarks', function () {
    $this->post(route('import-bookmarks'))->assertRedirect('/login');
});
