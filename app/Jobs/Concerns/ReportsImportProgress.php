<?php

namespace App\Jobs\Concerns;

use App\Events\BookmarksImportProgressed;

trait ReportsImportProgress {
    /**
     * Tells the user how far the import went, after each finished job.
     *
     * @param  array{userId: int, importId: string}  $import
     */
    private function reportProgress(array $import): void {
        $batch = $this->batch();

        if (! $batch) {
            return;
        }

        // this job is still counted as pending while it runs
        $done = $batch->totalJobs - $batch->pendingJobs + 1;

        BookmarksImportProgressed::dispatch(array(
            'userId' => $import['userId'],
            'importId' => $import['importId'],
            'done' => min($done, $batch->totalJobs),
            'total' => $batch->totalJobs,
        ));
    }
}
