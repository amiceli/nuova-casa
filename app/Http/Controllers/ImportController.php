<?php

namespace App\Http\Controllers;

use App\Events\BookmarksImportFinished;
use App\Http\Requests\ImportBookmarksRequest;
use App\Jobs\FindPageIcon;
use App\Jobs\FindTagIcon;
use App\Services\BookmarkImporter;
use App\Services\BookmarkParser;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class ImportController extends Controller {
    /**
     * Folders become tags, links land in theirs. Jobs look for the icons and
     * report their progress over the websocket.
     */
    public function store(ImportBookmarksRequest $request, BookmarkParser $parser, BookmarkImporter $importer) {
        $data = (object) $request->validated();
        $user = auth()->user();

        $bookmarks = $parser->parse(array(
            'html' => (string) $request->file('bookmarks')->get(),
            'defaultTag' => $data->default_tag,
        ));

        if ($bookmarks === array()) {
            return response()->json(array(
                'message' => 'import_no_bookmark_found',
                'errors' => array('bookmarks' => array('import_no_bookmark_found')),
            ), 422);
        }

        $imported = $importer->import(array(
            'bookmarks' => $bookmarks,
            'user' => $user,
        ));

        $import = array(
            'userId' => $user->id,
            'importId' => (string) Str::uuid(),
            'tags' => count($imported['tagIds']),
            'pages' => count($imported['pageIds']),
        );

        $jobs = $this->iconJobs(array(
            'import' => $import,
            'imported' => $imported,
        ));

        $this->dispatchIconJobs(array(
            'import' => $import,
            'jobs' => $jobs,
        ));

        return response()->json(array(
            'importId' => $import['importId'],
            'tags' => $import['tags'],
            'pages' => $import['pages'],
            'skipped' => $imported['skipped'],
            'total' => count($jobs),
        ));
    }

    /**
     * @param  array{import: array{userId: int, importId: string}, imported: array{tagIds: array<int, int>, pageIds: array<int, int>}}  $params
     * @return array<int, FindTagIcon|FindPageIcon>
     */
    private function iconJobs(array $params): array {
        $jobs = array();

        foreach ($params['imported']['tagIds'] as $tagId) {
            $jobs[] = new FindTagIcon(array(
                'tagId' => $tagId,
                'userId' => $params['import']['userId'],
                'importId' => $params['import']['importId'],
            ));
        }

        foreach ($params['imported']['pageIds'] as $pageId) {
            $jobs[] = new FindPageIcon(array(
                'pageId' => $pageId,
                'userId' => $params['import']['userId'],
                'importId' => $params['import']['importId'],
            ));
        }

        return $jobs;
    }

    /**
     * An unreachable site must not hold the whole import back.
     *
     * @param  array{import: array{userId: int, importId: string, tags: int, pages: int}, jobs: array<int, FindTagIcon|FindPageIcon>}  $params
     */
    private function dispatchIconJobs(array $params): void {
        $import = $params['import'];

        if ($params['jobs'] === array()) {
            BookmarksImportFinished::dispatch($import);

            return;
        }

        Bus::batch($params['jobs'])
            ->name('bookmarks-import:'.$import['userId'])
            ->allowFailures()
            ->finally(function (Batch $batch) use ($import) {
                BookmarksImportFinished::dispatch($import);
            })
            ->dispatch();
    }
}
