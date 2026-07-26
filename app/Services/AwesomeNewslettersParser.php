<?php

namespace App\Services;

class AwesomeNewslettersParser {
    /**
     * Link labels that never describe the author of a newsletter.
     */
    private const IGNORED_LINK_LABELS = array('archive', 'archives', 'issues', 'rss', 'feed', 'twitter', 'x');

    private const ENTRY_PATTERN = '/^\s*[-*]\s+\[([^\]]+)\]\(([^)\s]+)\)\s*\.?\s*(.*)$/u';

    private const HEADING_PATTERN = '/^(#{2,6})\s+(.+)$/u';

    private const AUTHOR_PATTERN = '/^\[([^\]]+)\]\(([^)\s]+)\)\s*\.?$/u';

    private const MARKDOWN_LINK_PATTERN = '/\[([^\]]*)\]\(([^)\s]*)\)/u';

    /**
     * Turn the awesome-newsletters README into a list of newsletters.
     *
     * @return array<int, array<string, string|null>>
     */
    public function parse(string $markdown): array {
        $newsletters = array();
        $category = null;

        foreach (preg_split('/\R/', $markdown) as $line) {
            if (preg_match(self::HEADING_PATTERN, $line, $heading)) {
                $category = $this->cleanCategory($heading[2]);

                continue;
            }

            $newsletter = $this->parseEntry($line);

            if (! $newsletter) {
                continue;
            }

            $newsletter['category'] = $category;
            $newsletters[] = $newsletter;
        }

        return $this->removeDuplicates($newsletters);
    }

    /**
     * @return array<string, string|null>|null
     */
    private function parseEntry(string $line): ?array {
        if (! preg_match(self::ENTRY_PATTERN, $line, $matches)) {
            return null;
        }

        $url = trim($matches[2]);

        // Table of contents entries only point to an anchor of the README.
        if (! str_starts_with($url, 'http')) {
            return null;
        }

        $rest = trim($matches[3]);
        $author = $this->parseAuthor($rest);

        return array(
            'name' => $this->cleanText($matches[1]),
            'url' => $url,
            'description' => $author ? null : $this->parseDescription($rest),
            'author' => $author ? $author['name'] : null,
            'author_url' => $author ? $author['url'] : null,
        );
    }

    /**
     * The author is only known when the entry ends with a single lonely link.
     *
     * @return array<string, string>|null
     */
    private function parseAuthor(string $rest): ?array {
        if (! preg_match(self::AUTHOR_PATTERN, $rest, $matches)) {
            return null;
        }

        $name = $this->cleanText($matches[1]);

        if (in_array(mb_strtolower($name), self::IGNORED_LINK_LABELS, true)) {
            return null;
        }

        return array(
            'name' => $name,
            'url' => trim($matches[2]),
        );
    }

    private function parseDescription(string $rest): ?string {
        // Inline links are kept as plain text, images are dropped.
        $description = preg_replace('/!\[[^\]]*\]\([^)\s]*\)/u', '', $rest);
        $description = preg_replace(self::MARKDOWN_LINK_PATTERN, '$1', $description);
        $description = $this->cleanText((string) $description);

        return $description === '' ? null : $description;
    }

    private function cleanCategory(string $category): string {
        return $this->cleanText(preg_replace('/#+\s*$/u', '', $category));
    }

    private function cleanText(string $text): string {
        $text = str_replace(array('\\#', '\\_', '\\*'), array('#', '_', '*'), $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim((string) $text);
    }

    /**
     * The README lists a few newsletters in several categories.
     *
     * @param  array<int, array<string, string|null>>  $newsletters
     * @return array<int, array<string, string|null>>
     */
    private function removeDuplicates(array $newsletters): array {
        $unique = array();

        foreach ($newsletters as $newsletter) {
            // the first category a newsletter belongs to is the most precise one
            if (array_key_exists($newsletter['url'], $unique)) {
                continue;
            }

            $unique[$newsletter['url']] = $newsletter;
        }

        return array_values($unique);
    }
}
