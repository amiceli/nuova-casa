<?php

namespace App\Jobs;

use App\Models\Tag;
use App\Services\IconFinder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FindTagIcon implements ShouldQueue {
    use Queueable;

    public int $tries = 2;

    public int $timeout = 60;

    public function __construct(public readonly int $tagId) {}

    /**
     * A tag has no site of its own, only SearXNG can name its icon.
     */
    public function handle(IconFinder $finder): void {
        $tag = Tag::find($this->tagId);

        if (! $tag) {
            return;
        }

        $icon = $finder->first($tag->name);

        if (! $icon) {
            return;
        }

        $tag->icon = $icon;

        $tag->save();
    }
}
