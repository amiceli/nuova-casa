<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Tag;
use App\Services\IconFinder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AppController extends Controller {
    /**
     * An empty dashboard means either an empty account or no favorite yet,
     * the front tells them apart to say the right thing.
     */
    public function dashboard() {
        $userId = auth()->user()->id;

        $pages = Page::where('user_id', $userId)
            ->where('favorite', true)
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Dashboard', array(
            'pages' => $pages,
            'hasTags' => Tag::where('user_id', $userId)->exists(),
        ));
    }

    public function searXng(FormRequest $request, IconFinder $finder) {
        $data = $request->validate(array(
            'name' => array('required', 'string'),
        ));

        return response()->json(array(
            'images' => $finder->search($data['name']),
        ));
    }

    public function proxy(Request $request) {
        $url = $request->query('url');

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'Invalid URL');
        }

        try {
            $response = Http::withOptions(array('stream' => true))->get($url);
            $contentType = $response->header('Content-Type', 'image/jpeg');

            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'max-age=3600');
        } catch (\Exception $e) {
            abort(500, "Failed to fetch image : $e");
        }
    }

    /**
     * Searches user links, on their title or their url.
     */
    public function search(Request $request) {
        $data = $request->validate(array(
            'value' => array('nullable', 'string'),
        ));
        $search = trim($data['value'] ?? '');

        if ($search === '') {
            return Inertia::render('Search', array(
                'pages' => array(),
                'search' => $search,
            ));
        }

        $pages = Page::where('user_id', auth()->user()->id)
            ->where(function ($query) use ($search) {
                return $query
                    ->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('url', 'LIKE', '%'.$search.'%');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Search', array(
            'pages' => $pages,
            'search' => $search,
        ));
    }
}
