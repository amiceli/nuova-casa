<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $userId = DB::table('users')->min('id');

        foreach ($this->links() as $link) {
            $tagId = DB::table('tags')
                ->where('name', $link['tag'])
                ->where('user_id', $userId)
                ->value('id');

            if ($tagId === null) {
                continue;
            }

            DB::table('pages')->updateOrInsert(
                array(
                    'tag_id' => $tagId,
                    'url' => $link['url'],
                ),
                array(
                    'title' => $link['title'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'favorite' => $link['favorite'] ?? false,
                    'icon' => 'https://www.google.com/s2/favicons?domain='.parse_url($link['url'], PHP_URL_HOST).'&sz=64',
                    'user_id' => $userId,
                )
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function links(): array {
        return array_merge(
            $this->githubLinks(),
            $this->plantLinks(),
            $this->frontEndLinks(),
            $this->backEndLinks()
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function githubLinks(): array {
        return array(
            array(
                'tag' => 'Github',
                'title' => 'preums',
                'url' => 'https://github.com/amiceli/preums',
            ),
            array(
                'tag' => 'Github',
                'title' => 'vitest-cucumber',
                'url' => 'https://github.com/amiceli/vitest-cucumber',
                'favorite' => true,
            ),
            array(
                'tag' => 'Github',
                'title' => 'nuova-casa',
                'url' => 'https://github.com/amiceli/nuova-casa',
                'favorite' => true,
            ),
            array(
                'tag' => 'Github',
                'title' => 'souflette',
                'url' => 'https://github.com/amiceli/souflette',
            ),
            array(
                'tag' => 'Github',
                'title' => 'tv-fr-api',
                'url' => 'https://github.com/amiceli/tv-fr-api',
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function plantLinks(): array {
        return array(
            array(
                'tag' => 'Plante pour tous',
                'title' => 'Plantes Pour Tous Marseille',
                'url' => 'https://plantespourtous.co/pages/marseille',
                'favorite' => true,
            ),
            array(
                'tag' => 'Plante pour tous',
                'title' => 'Le Jardin de Marie',
                'url' => 'https://www.lejardindemarie.com',
            ),
            array(
                'tag' => 'Plante pour tous',
                'title' => 'Plant and Story',
                'url' => 'https://plantandstory.com',
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function frontEndLinks(): array {
        return array(
            array(
                'tag' => 'front-end',
                'title' => 'Vue.js',
                'url' => 'https://vuejs.org',
                'favorite' => true,
            ),
            array(
                'tag' => 'front-end',
                'title' => 'Svelte',
                'url' => 'https://svelte.dev',
            ),
            array(
                'tag' => 'front-end',
                'title' => 'Vitest',
                'url' => 'https://vitest.dev',
                'favorite' => true,
            ),
            array(
                'tag' => 'front-end',
                'title' => 'Vite',
                'url' => 'https://vite.dev',
            ),
            array(
                'tag' => 'front-end',
                'title' => 'tsdown',
                'url' => 'https://tsdown.dev',
            ),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function backEndLinks(): array {
        return array(
            array(
                'tag' => 'back-end',
                'title' => 'Laravel',
                'url' => 'https://laravel.com',
                'favorite' => true,
            ),
            array(
                'tag' => 'back-end',
                'title' => 'Symfony',
                'url' => 'https://symfony.com',
            ),
            array(
                'tag' => 'back-end',
                'title' => 'NestJS',
                'url' => 'https://nestjs.com',
            ),
            array(
                'tag' => 'back-end',
                'title' => 'Deno',
                'url' => 'https://deno.com',
            ),
            array(
                'tag' => 'back-end',
                'title' => 'Phalcon',
                'url' => 'https://phalcon.io',
            ),
        );
    }
}
