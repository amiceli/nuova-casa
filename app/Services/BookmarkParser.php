<?php

namespace App\Services;

class BookmarkParser {
    /**
     * A browser root says nothing about sorting, it never becomes a tag.
     *
     * @var array<int, string>
     */
    private const BROWSER_ROOT_FOLDERS = array(
        'bookmarks',
        'bookmarks bar',
        'bookmarks menu',
        'bookmarks toolbar',
        'other bookmarks',
        'mobile bookmarks',
        'favorites',
        'favourites',
        'favorites bar',
        'favourites bar',
        'favoris',
        'autres favoris',
        'barre de favoris',
        'barre des favoris',
        'barre personnelle',
        'marque-pages',
        'autres marque-pages',
        'menu des marque-pages',
    );

    private const SEPARATOR = ' / ';

    private const MAX_TAG_NAME_LENGTH = 255;

    /**
     * The export is malformed html, list items are never closed : folders are
     * followed with a stack instead of a document walk.
     */
    private const TOKENS = '#<h3[^>]*>(?<folder>.*?)</h3>|(?<open><dl[^>]*>)|(?<close></dl\s*>)|<a\s[^>]*?href\s*=\s*["\'](?<href>[^"\']*)["\'][^>]*>(?<title>.*?)</a>#is';

    /**
     * Each bookmark gets the flattened path of its folders as tag.
     *
     * @param  array{html: string, defaultTag: string}  $params
     * @return array<int, array{title: string, url: string, tag: string}>
     */
    public function parse(array $params): array {
        preg_match_all(self::TOKENS, $params['html'], $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $bookmarks = array();
        $folders = array();
        $pending = null;

        foreach ($matches as $match) {
            if ($this->matched(array('match' => $match, 'group' => 'folder'))) {
                $pending = $this->readText($match['folder'][0]);

                continue;
            }

            if ($this->matched(array('match' => $match, 'group' => 'open'))) {
                $folders[] = $pending;
                $pending = null;

                continue;
            }

            if ($this->matched(array('match' => $match, 'group' => 'close'))) {
                array_pop($folders);

                continue;
            }

            if (! $this->matched(array('match' => $match, 'group' => 'href'))) {
                continue;
            }

            $bookmark = $this->toBookmark(array(
                'url' => $this->readText($match['href'][0]),
                'title' => $this->readText($match['title'][0]),
                'folders' => $folders,
                'defaultTag' => $params['defaultTag'],
            ));

            if ($bookmark) {
                $bookmarks[] = $bookmark;
            }
        }

        return $bookmarks;
    }

    /**
     * @param  array{url: string, title: string, folders: array<int, string|null>, defaultTag: string}  $params
     * @return array{title: string, url: string, tag: string}|null
     */
    private function toBookmark(array $params): ?array {
        $url = $params['url'];

        // browsers also export their own entries : place:, javascript:, chrome://…
        if (! preg_match('#^https?://#i', $url)) {
            return null;
        }

        return array(
            'title' => $params['title'] !== '' ? $params['title'] : $this->hostOf($url),
            'url' => $url,
            'tag' => $this->tagName(array(
                'folders' => $params['folders'],
                'defaultTag' => $params['defaultTag'],
            )),
        );
    }

    /**
     * @param  array{folders: array<int, string|null>, defaultTag: string}  $params
     */
    private function tagName(array $params): string {
        $folders = array_filter(
            $params['folders'],
            function ($folder) {
                return $folder !== null
                    && $folder !== ''
                    && ! in_array(mb_strtolower($folder), self::BROWSER_ROOT_FOLDERS, true);
            }
        );

        if ($folders === array()) {
            return $params['defaultTag'];
        }

        return mb_substr(implode(self::SEPARATOR, $folders), 0, self::MAX_TAG_NAME_LENGTH);
    }

    private function hostOf(string $url): string {
        return parse_url($url, PHP_URL_HOST) ?: $url;
    }

    private function readText(string $raw): string {
        $text = html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('#\s+#u', ' ', $text) ?? '');
    }

    /**
     * A group that did not take part in the match has no offset.
     *
     * @param  array{match: array<string, array{0: string, 1: int}>, group: string}  $params
     */
    private function matched(array $params): bool {
        $group = $params['match'][$params['group']] ?? null;

        return $group !== null && $group[1] !== -1;
    }
}
