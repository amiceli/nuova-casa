<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookmarksImportProgressed implements ShouldBroadcastNow {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  array{userId: int, importId: string, done: int, total: int}  $import
     */
    public function __construct(public readonly array $import) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array {
        return array(
            new PrivateChannel('bookmarks-import.'.$this->import['userId']),
        );
    }

    public function broadcastAs(): string {
        return 'progressed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array {
        return array(
            'importId' => $this->import['importId'],
            'done' => $this->import['done'],
            'total' => $this->import['total'],
        );
    }
}
