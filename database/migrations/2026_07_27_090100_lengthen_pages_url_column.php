<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Imported bookmarks carry tracking parameters, 255 is not enough.
     */
    public function up(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('url', 1024)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('url')->change();
        });
    }
};
