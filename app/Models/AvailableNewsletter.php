<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableNewsletter extends Model {
    protected $fillable = array(
        'name',
        'url',
        'feed_url',
        'description',
        'author',
        'author_url',
        'category',
        'icon',
        'icon_checked_at',
    );

    protected function casts(): array {
        return array(
            'icon_checked_at' => 'datetime',
        );
    }

    public function newsletters() {
        return $this->hasMany(Newsletter::class);
    }

    public function toOption(): array {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'feedUrl' => $this->feed_url,
            'description' => $this->description,
            'author' => $this->author,
            'authorUrl' => $this->author_url,
            'category' => $this->category,
            'icon' => $this->icon,
        );
    }
}
