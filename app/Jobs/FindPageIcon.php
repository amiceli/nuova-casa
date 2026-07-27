<?php

namespace App\Jobs;

use App\Jobs\Concerns\ReportsImportProgress;
use App\Models\Page;
use App\Services\IconFinder;
use App\Services\SiteMetadataFinder;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FindPageIcon implements ShouldQueue {
    use Batchable, Queueable, ReportsImportProgress;

    public int $tries = 2;

    public int $timeout = 60;

    /**
     * @param  array{pageId: int, userId: int, importId: string}  $import
     */
    public function __construct(public readonly array $import) {}

    /**
     * The site describes itself best, SearXNG only steps in when it says nothing.
     */
    public function handle(SiteMetadataFinder $finder, IconFinder $icons): void {
        $page = Page::find($this->import['pageId']);

        if (! $page) {
            return;
        }

        $icon = $finder->findLogo($page->url) ?? $icons->first($page->title);

        if ($icon) {
            $page->icon = $icon;

            $page->save();
        }

        $this->reportProgress($this->import);
    }
}
