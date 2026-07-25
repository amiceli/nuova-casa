<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder {
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
                'icon' => 'https://www.google.com/s2/favicons?domain=github.com&sz=64',
            ),
            array(
                'name' => 'Plante pour tous',
                'icon' => 'https://www.google.com/s2/favicons?domain=plantespourtous.co&sz=64',
            ),
            array(
                'name' => 'front-end',
                'icon' => 'https://www.google.com/s2/favicons?domain=vuejs.org&sz=64',
            ),
            array(
                'name' => 'back-end',
                'icon' => 'https://www.google.com/s2/favicons?domain=laravel.com&sz=64',
            ),
        );
    }
}
