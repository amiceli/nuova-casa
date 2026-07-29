<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Tag;
use App\Models\User;

class BookmarkImporter {
    /**
     * Placeholder until the weekly command finds something better with SearXNG.
     */
    public const FALLBACK_ICON = 'resources/assets/404_retro.png';

    private const MAX_TITLE_LENGTH = 255;

    private const MAX_URL_LENGTH = 1024;

    /**
     * Bookmarks become tags and links, what the user already has is untouched.
     *
     * @param  array{bookmarks: array<int, array{title: string, url: string, tag: string}>, user: User}  $params
     * @return array{tagIds: array<int, int>, pageIds: array<int, int>, skipped: int}
     */
    public function import(array $params): array {
        $user = $params['user'];
        $tagIds = array();
        $pageIds = array();
        $skipped = 0;

        foreach ($this->groupByTag($params['bookmarks']) as $name => $bookmarks) {
            $tag = $this->tagFor(array(
                'user' => $user,
                'name' => (string) $name,
            ));

            if ($tag->wasRecentlyCreated) {
                $tagIds[] = $tag->id;
            }

            foreach ($bookmarks as $bookmark) {
                if (mb_strlen($bookmark['url']) > self::MAX_URL_LENGTH) {
                    $skipped++;

                    continue;
                }

                $page = $this->pageFor(array(
                    'user' => $user,
                    'tag' => $tag,
                    'bookmark' => $bookmark,
                ));

                if ($page) {
                    $pageIds[] = $page->id;
                }
            }
        }

        return array(
            'tagIds' => $tagIds,
            'pageIds' => $pageIds,
            'skipped' => $skipped,
        );
    }

    /**
     * @param  array<int, array{title: string, url: string, tag: string}>  $bookmarks
     * @return array<string, array<int, array{title: string, url: string, tag: string}>>
     */
    private function groupByTag(array $bookmarks): array {
        $groups = array();

        foreach ($bookmarks as $bookmark) {
            $groups[$bookmark['tag']][] = $bookmark;
        }

        return $groups;
    }

    /**
     * @param  array{user: User, name: string}  $params
     */
    private function tagFor(array $params): Tag {
        $tag = Tag::where('user_id', $params['user']->id)
            ->where('name', $params['name'])
            ->first();

        if ($tag) {
            return $tag;
        }

        $tag = new Tag();
        $tag->name = $params['name'];
        $tag->icon = self::FALLBACK_ICON;
        $tag->user_id = $params['user']->id;

        $tag->save();

        return $tag;
    }

    /**
     * @param  array{user: User, tag: Tag, bookmark: array{title: string, url: string, tag: string}}  $params
     */
    private function pageFor(array $params): ?Page {
        $url = $params['bookmark']['url'];

        $alreadyThere = Page::where('tag_id', $params['tag']->id)
            ->where('url', $url)
            ->exists();

        if ($alreadyThere) {
            return null;
        }

        $page = new Page();
        $page->title = mb_substr($params['bookmark']['title'], 0, self::MAX_TITLE_LENGTH);
        $page->url = $url;
        $page->icon = self::FALLBACK_ICON;
        $page->favorite = false;
        $page->tag_id = $params['tag']->id;
        $page->user_id = $params['user']->id;

        $page->save();

        return $page;
    }
}
