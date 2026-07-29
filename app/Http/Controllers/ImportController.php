<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportBookmarksRequest;
use App\Services\BookmarkImporter;
use App\Services\BookmarkParser;

class ImportController extends Controller {
    /**
     * Folders become tags, links land in theirs. Their icons are the fallback
     * one until the weekly command looks for something better.
     */
    public function store(ImportBookmarksRequest $request, BookmarkParser $parser, BookmarkImporter $importer) {
        $data = (object) $request->validated();

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
            'user' => auth()->user(),
        ));

        return response()->json(array(
            'tags' => count($imported['tagIds']),
            'pages' => count($imported['pageIds']),
            'skipped' => $imported['skipped'],
        ));
    }
}
