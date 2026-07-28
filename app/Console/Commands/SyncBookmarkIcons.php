<?php

namespace App\Console\Commands;

use App\Jobs\FindPageIcon;
use App\Jobs\FindTagIcon;
use App\Models\Page;
use App\Models\Tag;
use App\Services\BookmarkImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncBookmarkIcons extends Command {
    protected $signature = 'bookmarks:sync-icons {--limit=50 : How many tags and pages to check in this run}';

    protected $description = 'Look for an icon for the imported tags and pages still wearing the fallback one';

    /**
     * An import leaves the fallback icon behind, this is what replaces it.
     */
    public function handle(): int {
        $limit = (int) $this->option('limit');

        $tags = Tag::where('icon', BookmarkImporter::FALLBACK_ICON)
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        $pages = Page::where('icon', BookmarkImporter::FALLBACK_ICON)
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        foreach ($tags as $tagId) {
            FindTagIcon::dispatch($tagId);
        }

        foreach ($pages as $pageId) {
            FindPageIcon::dispatch($pageId);
        }

        Log::info('action=sync_bookmark_icons, status=success', array(
            'tags' => $tags->count(),
            'pages' => $pages->count(),
        ));

        $this->info(sprintf('%d tag(s) and %d page(s) queued', $tags->count(), $pages->count()));

        return self::SUCCESS;
    }
}
