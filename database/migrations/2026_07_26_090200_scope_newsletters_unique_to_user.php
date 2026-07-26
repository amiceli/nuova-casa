<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Two users must be able to follow the same newsletter.
     */
    public function up(): void {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropUnique('newsletters_title_unique');
            $table->dropUnique('newsletters_url_unique');

            $table->unique(array('user_id', 'title'));
            $table->unique(array('user_id', 'url'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropUnique(array('user_id', 'title'));
            $table->dropUnique(array('user_id', 'url'));

            $table->unique('title');
            $table->unique('url');
        });
    }
};
