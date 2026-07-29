<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnboardedUserSeeder extends Seeder {
    /**
     * The onboarding is over : tags, links and newsletters are already there.
     */
    public function run(): void {
        $userId = DB::table('users')->min('id');

        // accounts only come from a github login, nothing to seed without one
        if ($userId === null) {
            $this->command->warn('No user yet, log in with github first.');

            return;
        }

        $this->call(array(
            ResetUserDataSeeder::class,
            TagSeeder::class,
            PageSeeder::class,
            NewsletterSeeder::class,
        ));

        DB::table('users')
            ->where('id', $userId)
            ->update(array(
                'onboarding_completed_at' => now(),
            ));

        $this->command->info('User is set up, the onboarding is behind them.');
    }
}
