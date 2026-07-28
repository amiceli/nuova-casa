<?php

namespace App\Jobs;

use App\Models\Page;
use App\Services\IconFinder;
use App\Services\SiteMetadataFinder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FindPageIcon implements ShouldQueue {
    use Queueable;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(public readonly int $pageId) {}

    /**
     * The site describes itself best, SearXNG only steps in when it says nothing.
     */
    public function handle(SiteMetadataFinder $finder, IconFinder $icons): void {
        $page = Page::find($this->pageId);

        if (! $page) {
            return;
        }

        $icon = $finder->findLogo($page->url) ?? $icons->first($page->title);

        if (! $icon) {
            return;
        }

        $page->icon = $icon;

        $page->save();
    }
}
