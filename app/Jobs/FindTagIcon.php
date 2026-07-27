<?php

namespace App\Jobs;

use App\Jobs\Concerns\ReportsImportProgress;
use App\Models\Tag;
use App\Services\IconFinder;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FindTagIcon implements ShouldQueue {
    use Batchable, Queueable, ReportsImportProgress;

    public int $tries = 2;

    public int $timeout = 60;

    /**
     * @param  array{tagId: int, userId: int, importId: string}  $import
     */
    public function __construct(public readonly array $import) {}

    /**
     * A tag has no site of its own, only SearXNG can name its icon.
     */
    public function handle(IconFinder $finder): void {
        $tag = Tag::find($this->import['tagId']);

        if (! $tag) {
            return;
        }

        $icon = $finder->first($tag->name);

        if ($icon) {
            $tag->icon = $icon;

            $tag->save();
        }

        $this->reportProgress($this->import);
    }
}
