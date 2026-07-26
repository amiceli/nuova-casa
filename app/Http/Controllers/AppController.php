<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class AppController extends Controller {
    public function dashboard() {
        $pages = Page::where('user_id', auth()->user()->id)
            ->where('favorite', true)
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Dashboard', array(
            'pages' => $pages,
        ));
    }

    private function searchWord(string $word) {
        $url = env('SEARXNG_URL');

        $response = Http::withHeaders(array(
            'User-Agent' => 'Mozilla/5.0',
        ))->get("$url/search", array(
            'q' => "$word logo",
            'categories' => 'images',
            'format' => 'json',
        ));

        $imageUrls = array_map(
            function ($item) {
                return $item['img_src'];
            },
            array_slice($response['results'], 0, 25)
        );

        return $imageUrls;
    }

    public function searXng(FormRequest $request) {
        $data = $request->validate(array(
            'name' => array('required', 'string'),
        ));
        $imageUrls = $this->searchWord($data['name']);

        return response()->json(array(
            'images' => $imageUrls,
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
