<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vedmant\FeedReader\Facades\FeedReader;

class Newsletter extends Model {
    protected $fillable = array(
        'title',
        'url',
        'user_id',
        'available_newsletter_id',
        'last_read_at',
        'last_read_link',
        'last_read_title',
    );

    protected function casts(): array {
        return array(
            'last_read_at' => 'datetime',
        );
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function availableNewsletter() {
        return $this->belongsTo(AvailableNewsletter::class);
    }

    public function getLastLink() {
        try {
            $f = FeedReader::read($this->url);

            return array(
                'title' => $f->get_items()[0]->get_title(),
                'link' => $f->get_items()[0]->get_link(),
                'date' => $f->get_items()[0]->get_date('c'),
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * A newsletter is read when its last item is the one the user read last.
     */
    public function hasRead(?array $lastLink): bool {
        if (! $lastLink || ! $this->last_read_link) {
            return false;
        }

        return $this->last_read_link === $lastLink['link'];
    }

    public function markAsRead(array $item): void {
        $this->last_read_at = now();
        $this->last_read_link = $item['link'];
        $this->last_read_title = $item['title'];

        $this->save();
    }
}
