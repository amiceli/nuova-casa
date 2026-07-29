<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetUserDataSeeder extends Seeder {
    /**
     * Empty what belongs to the users. Accounts and the newsletter catalog
     * stay, both are expensive to get back.
     */
    public function run(): void {
        DB::table('pages')->delete();
        DB::table('tags')->delete();
        DB::table('newsletters')->delete();
    }
}
