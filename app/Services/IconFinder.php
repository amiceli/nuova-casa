<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IconFinder {
    private const MAX_RESULTS = 25;

    /**
     * The logos SearXNG knows about a word, for the user to pick one.
     *
     * @return array<int, string>
     */
    public function search(string $word): array {
        $url = config('services.searxng.url');

        if (! $url) {
            return array();
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(array('User-Agent' => 'Mozilla/5.0'))
                ->get(rtrim($url, '/').'/search', array(
                    'q' => "$word logo",
                    'categories' => 'images',
                    'format' => 'json',
                ));
        } catch (\Exception $e) {
            Log::warning('action=search_icon, status=failed', array('word' => $word, 'reason' => $e->getMessage()));

            return array();
        }

        $results = $response->json('results');

        if (! is_array($results)) {
            return array();
        }

        return array_values(
            array_filter(
                array_map(
                    function ($item) {
                        return $item['img_src'] ?? null;
                    },
                    array_slice($results, 0, self::MAX_RESULTS)
                )
            )
        );
    }

    /**
     * The best guess, when nobody is there to pick.
     */
    public function first(string $word): ?string {
        return $this->search($word)[0] ?? null;
    }
}
