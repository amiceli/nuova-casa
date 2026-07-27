<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder {
    /**
     * Placeholder until the jobs find something better with SearXNG.
     */
    private const FALLBACK_ICON = 'resources/assets/404_retro.png';

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $userId = DB::table('users')->min('id');

        foreach ($this->tags() as $tag) {
            DB::table('tags')->updateOrInsert(
                array(
                    'name' => $tag['name'],
                    'user_id' => $userId,
                ),
                array(
                    'created_at' => date('Y-m-d H:i:s'),
                    'icon' => $tag['icon'],
                )
            );
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function tags(): array {
        return array(
            array(
                'name' => 'Github',
                'icon' => self::FALLBACK_ICON,
            ),
            array(
                'name' => 'Plante pour tous',
                'icon' => self::FALLBACK_ICON,
            ),
            array(
                'name' => 'front-end',
                'icon' => self::FALLBACK_ICON,
            ),
            array(
                'name' => 'back-end',
                'icon' => self::FALLBACK_ICON,
            ),
        );
    }
}
