<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    protected $hidden = array('user_id', 'pages');

    protected $appends = array('children');

    public function getChildrenAttribute() {
        return $this->pages->map(function ($map) {
            return array(
                'id' => $map->id,
                'title' => $map->title,
                'icon' => $map->icon,
                'url' => $map->url,
                'favorite' => $map->favorite,
                'created_at' => $map->created_at,
            );
        });
    }

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public static function nameAlreadyUsed(string $name) {
        return self::where('user_id', auth()->user()->id)
            ->whereRaw('LOWER(name) = ?', array(mb_strtolower(trim($name))))
            ->exists();
    }
}
