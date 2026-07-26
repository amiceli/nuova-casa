<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vedmant\FeedReader\Facades\FeedReader;

class Newsletter extends Model {
    public function user() {
        return $this->belongsTo(User::class);
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
}
