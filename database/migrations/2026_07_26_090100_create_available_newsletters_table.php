<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('available_newsletters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('feed_url')->nullable();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('category')->nullable();
            $table->string('icon')->nullable();
            $table->timestamp('icon_checked_at')->nullable();
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table
                ->foreignId('available_newsletter_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropConstrainedForeignId('available_newsletter_id');
        });

        Schema::dropIfExists('available_newsletters');
    }
};
