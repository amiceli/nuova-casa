<?php

namespace Database\Seeders;

use App\Enums\Theme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FreshUserSeeder extends Seeder {
    /**
     * A first login : nothing of their own, the onboarding still ahead.
     */
    public function run(): void {
        $this->call(array(
            ResetUserDataSeeder::class,
        ));

        $userId = DB::table('users')->min('id');

        if ($userId === null) {
            $this->command->warn('No user yet, log in with github first.');

            return;
        }

        DB::table('users')
            ->where('id', $userId)
            ->update(array(
                'onboarding_completed_at' => null,
                'theme' => Theme::System->value,
            ));

        $this->command->info('User is back to their first login, the onboarding is waiting for them.');
    }
}
