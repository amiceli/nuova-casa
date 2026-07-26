<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SiteMetadataFinder {
    private const FEED_TYPES = array('application/rss+xml', 'application/atom+xml', 'application/feed+json');

    private const ICON_RELS = array('apple-touch-icon', 'apple-touch-icon-precomposed', 'icon', 'shortcut icon');

    private const IMAGE_PROPERTIES = array('og:image', 'og:image:url', 'og:image:secure_url', 'twitter:image');

    /**
     * Cache the html of a site, both finders are often called on the same url.
     *
     * @var array<string, string|null>
     */
    private array $pages = array();

    /**
     * Find the rss / atom feed a site declares in its head.
     */
    public function findFeedUrl(string $url): ?string {
        $document = $this->readDocument($url);

        if (! $document) {
            return null;
        }

        foreach ($document->getElementsByTagName('link') as $link) {
            $type = mb_strtolower(trim($link->getAttribute('type')));
            $rel = mb_strtolower(trim($link->getAttribute('rel')));

            if ($rel !== 'alternate' || ! in_array($type, self::FEED_TYPES, true)) {
                continue;
            }

            $feedUrl = $this->toAbsoluteUrl(array(
                'base' => $url,
                'href' => trim($link->getAttribute('href')),
            ));

            if ($feedUrl) {
                return $feedUrl;
            }
        }

        return null;
    }

    /**
     * Find a logo for a site: its open graph image, then one of its icons.
     */
    public function findLogo(string $url): ?string {
        $image = $this->findOpenGraphImage($url);

        if ($image) {
            return $image;
        }

        return $this->findIcon($url);
    }

    private function findOpenGraphImage(string $url): ?string {
        $document = $this->readDocument($url);

        if (! $document) {
            return null;
        }

        foreach ($document->getElementsByTagName('meta') as $meta) {
            $property = mb_strtolower(trim($meta->getAttribute('property')));

            if (! in_array($property, self::IMAGE_PROPERTIES, true)) {
                continue;
            }

            $image = $this->toAbsoluteUrl(array(
                'base' => $url,
                'href' => trim($meta->getAttribute('content')),
            ));

            if ($image) {
                return $image;
            }
        }

        return null;
    }

    private function findIcon(string $url): ?string {
        $document = $this->readDocument($url);

        if (! $document) {
            return null;
        }

        foreach ($document->getElementsByTagName('link') as $link) {
            $rel = mb_strtolower(trim($link->getAttribute('rel')));

            if (! in_array($rel, self::ICON_RELS, true)) {
                continue;
            }

            $icon = $this->toAbsoluteUrl(array(
                'base' => $url,
                'href' => trim($link->getAttribute('href')),
            ));

            if ($icon) {
                return $icon;
            }
        }

        return $this->defaultFavicon($url);
    }

    private function defaultFavicon(string $url): ?string {
        $parts = parse_url($url);

        if (! isset($parts['host'])) {
            return null;
        }

        $scheme = $parts['scheme'] ?? 'https';
        $favicon = "{$scheme}://{$parts['host']}/favicon.ico";

        try {
            $response = Http::timeout(10)->head($favicon);
        } catch (\Exception $e) {
            return null;
        }

        return $response->successful() ? $favicon : null;
    }

    private function readDocument(string $url): ?\DOMDocument {
        $html = $this->readPage($url);

        if (! $html) {
            return null;
        }

        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);

        $loaded = $document->loadHTML($html);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $loaded ? $document : null;
    }

    private function readPage(string $url): ?string {
        if (array_key_exists($url, $this->pages)) {
            return $this->pages[$url];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(array('User-Agent' => 'nuova-casa'))
                ->get($url);

            $this->pages[$url] = $response->successful() ? $response->body() : null;
        } catch (\Exception $e) {
            $this->pages[$url] = null;
        }

        return $this->pages[$url];
    }

    /**
     * @param  array{base: string, href: string}  $params
     */
    private function toAbsoluteUrl(array $params): ?string {
        $href = $params['href'];

        if ($href === '') {
            return null;
        }

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        $base = parse_url($params['base']);

        if (! isset($base['host'])) {
            return null;
        }

        $scheme = $base['scheme'] ?? 'https';
        $origin = "{$scheme}://{$base['host']}";

        if (str_starts_with($href, '//')) {
            return "{$scheme}:{$href}";
        }

        if (str_starts_with($href, '/')) {
            return "{$origin}{$href}";
        }

        $path = rtrim(dirname($base['path'] ?? '/'), '/');

        return "{$origin}{$path}/{$href}";
    }
}
