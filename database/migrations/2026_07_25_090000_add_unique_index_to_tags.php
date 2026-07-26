<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        $this->renameDuplicatedNames();

        Schema::table('tags', function (Blueprint $table) {
            $table->unique(array('user_id', 'name'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique(array('user_id', 'name'));
        });
    }

    /**
     * Existing tags may share a name : the oldest one keeps it,
     * the next ones are suffixed so nothing is lost.
     */
    private function renameDuplicatedNames(): void {
        $used = array();

        foreach (DB::table('tags')->orderBy('id')->get() as $tag) {
            $key = $this->nameKey($tag->user_id, $tag->name);

            if (! in_array($key, $used, true)) {
                $used[] = $key;

                continue;
            }

            $suffix = 2;

            while (in_array($this->nameKey($tag->user_id, "$tag->name ($suffix)"), $used, true)) {
                $suffix++;
            }

            $name = "$tag->name ($suffix)";
            $used[] = $this->nameKey($tag->user_id, $name);

            DB::table('tags')
                ->where('id', $tag->id)
                ->update(array('name' => $name));
        }
    }

    private function nameKey(int $userId, string $name): string {
        return $userId.'|'.mb_strtolower(trim($name));
    }
};
