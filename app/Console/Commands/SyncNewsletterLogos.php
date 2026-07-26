<?php

namespace App\Console\Commands;

use App\Models\AvailableNewsletter;
use App\Services\SiteMetadataFinder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncNewsletterLogos extends Command {
    protected $signature = 'newsletters:sync-logos {--limit=25 : How many newsletters to check in this run}';

    protected $description = 'Look for a logo and a feed url for the newsletters of the catalog';

    public function handle(SiteMetadataFinder $finder): int {
        $limit = (int) $this->option('limit');

        $newsletters = AvailableNewsletter::whereNull('icon_checked_at')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($newsletters->isEmpty()) {
            $this->info('Every newsletter of the catalog has already been checked');

            return self::SUCCESS;
        }

        $found = 0;

        foreach ($newsletters as $newsletter) {
            $newsletter->icon = $finder->findLogo($newsletter->url);
            $newsletter->feed_url = $finder->findFeedUrl($newsletter->url);
            $newsletter->icon_checked_at = now();

            $newsletter->save();

            if ($newsletter->icon) {
                $found++;
            }
        }

        Log::info('action=sync_newsletter_logos, status=success', array(
            'checked' => $newsletters->count(),
            'found' => $found,
        ));

        $this->info(sprintf('%d newsletter(s) checked, %d logo(s) found', $newsletters->count(), $found));

        return self::SUCCESS;
    }
}
