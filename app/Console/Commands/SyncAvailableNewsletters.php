<?php

namespace App\Console\Commands;

use App\Models\AvailableNewsletter;
use App\Services\AwesomeNewslettersParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncAvailableNewsletters extends Command {
    protected $signature = 'newsletters:sync-catalog';

    protected $description = 'Fill the newsletter catalog from the awesome-newsletters GitHub repository';

    public function handle(AwesomeNewslettersParser $parser): int {
        $url = config('services.awesome_newsletters.readme_url');

        try {
            $response = Http::timeout(30)->get($url);
        } catch (\Exception $e) {
            $this->error("Could not reach {$url}: {$e->getMessage()}");

            return self::FAILURE;
        }

        if ($response->failed()) {
            $this->error("Could not read {$url}, status {$response->status()}");

            return self::FAILURE;
        }

        $newsletters = $parser->parse($response->body());

        if (count($newsletters) === 0) {
            $this->error('No newsletter found, the README format may have changed');

            return self::FAILURE;
        }

        $created = 0;

        foreach ($newsletters as $newsletter) {
            $model = AvailableNewsletter::updateOrCreate(
                array('url' => $newsletter['url']),
                array(
                    'name' => $newsletter['name'],
                    'description' => $newsletter['description'],
                    'author' => $newsletter['author'],
                    'author_url' => $newsletter['author_url'],
                    'category' => $newsletter['category'],
                )
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            }
        }

        Log::info('action=sync_newsletter_catalog, status=success', array(
            'parsed' => count($newsletters),
            'created' => $created,
        ));

        $this->info(sprintf('%d newsletter(s) parsed, %d added to the catalog', count($newsletters), $created));

        return self::SUCCESS;
    }
}
