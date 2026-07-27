<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// the progress of a bookmark import only concerns the user who started it
Broadcast::channel('bookmarks-import.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
