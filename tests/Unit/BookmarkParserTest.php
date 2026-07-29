<?php

use App\Services\BookmarkParser;

function parsedBookmarks(): array {
    return (new BookmarkParser())->parse(array(
        'html' => file_get_contents(dirname(__DIR__).'/Fixtures/bookmarks.html'),
        'defaultTag' => 'Favoris',
    ));
}

test('a folder becomes a tag, and its whole path is kept', function () {
    $bookmarks = collect(parsedBookmarks())->keyBy('url');

    expect($bookmarks->get('https://laravel.com')['tag'])->toBe('Dev')
        ->and($bookmarks->get('https://vitest.dev')['tag'])->toBe('Dev / Front')
        ->and($bookmarks->get('https://symfony.com')['tag'])->toBe('Dev');
});

test('the roots a browser adds by itself never become tags', function () {
    $bookmarks = collect(parsedBookmarks())->keyBy('url');

    expect($bookmarks->get('https://vuejs.org')['tag'])->toBe('Favoris')
        ->and($bookmarks->get('https://deno.com')['tag'])->toBe('Favoris');
});

test('a folder nested three levels deep keeps its whole path', function () {
    $bookmarks = collect(parsedBookmarks())->keyBy('url');

    expect($bookmarks->get('https://github.com/vuejs/pinia')['tag'])->toBe('Dev / Front / Vue')
        ->and($bookmarks->get('https://github.com/sveltejs/kit')['tag'])->toBe('Dev / Front / Svelte')
        ->and($bookmarks->get('https://pestphp.com')['tag'])->toBe('Dev / Back / PHP')
        ->and($bookmarks->get('https://astro.build')['tag'])->toBe('Docs / Frameworks / Astro')
        ->and($bookmarks->get('https://github.com/tmux/tmux')['tag'])->toBe('Perso / Lecture');
});

test('a folder closing does not leak into the next one', function () {
    $bookmarks = collect(parsedBookmarks())->keyBy('url');

    expect($bookmarks->get('https://rollupjs.org')['tag'])->toBe('Dev / Build')
        ->and($bookmarks->get('https://nodejs.org')['tag'])->toBe('Docs')
        ->and($bookmarks->get('https://git-scm.com')['tag'])->toBe('Outils')
        ->and($bookmarks->get('https://biomejs.dev')['tag'])->toBe('Veille');
});

test('only the links a browser can open again are imported', function () {
    $urls = collect(parsedBookmarks())->pluck('url');

    expect($urls)->toHaveCount(55)
        ->and($urls)->not->toContain('javascript:void(0)')
        ->and($urls)->not->toContain('chrome://bookmarks')
        ->and($urls->filter(fn ($url) => str_starts_with($url, 'place:')))->toBeEmpty();
});

test('every folder of the export becomes a tag', function () {
    $tags = collect(parsedBookmarks())->pluck('tag')->unique();

    expect($tags)->toHaveCount(17);
});

test('titles are decoded, and a link without one falls back on its host', function () {
    $bookmarks = collect(parsedBookmarks())->keyBy('url');

    expect($bookmarks->get('https://laravel.com')['title'])->toBe('Laravel & co')
        ->and($bookmarks->get('https://vite.dev')['title'])->toBe('vite.dev');
});

test('an empty file gives no bookmark', function () {
    $bookmarks = (new BookmarkParser())->parse(array(
        'html' => '',
        'defaultTag' => 'Favoris',
    ));

    expect($bookmarks)->toBe(array());
});
