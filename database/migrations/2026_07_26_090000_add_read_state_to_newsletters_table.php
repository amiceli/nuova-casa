<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->timestamp('last_read_at')->nullable();
            $table->string('last_read_link')->nullable();
            $table->string('last_read_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(array('last_read_at', 'last_read_link', 'last_read_title'));
        });
    }
};
