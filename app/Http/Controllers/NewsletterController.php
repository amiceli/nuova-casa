<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkNewsletterAsReadRequest;
use App\Http\Requests\StoreNewsletterRequest;
use App\Models\AvailableNewsletter;
use App\Models\Newsletter;
use App\Services\SiteMetadataFinder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Vedmant\FeedReader\Facades\FeedReader;

class NewsletterController extends Controller {
    private function getNewsletters() {
        return Newsletter::where('user_id', auth()->user()->id)
            ->with('availableNewsletter')
            ->get()
            ->map(function ($map) {
                $lastLink = $map->getLastLink();

                return array(
                    'id' => $map->id,
                    'title' => $map->title,
                    'url' => $map->url,
                    'icon' => $map->availableNewsletter?->icon,
                    'lastLink' => $lastLink,
                    'isRead' => $map->hasRead($lastLink),
                    'lastReadAt' => $map->last_read_at?->toIso8601String(),
                );
            });
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        return Inertia::render('newsletter/NewsletterList', array(
            'news' => Inertia::optional(
                function () {
                    return $this->getNewsletters();
                }
            ),
        ));
    }

    /**
     * List the newsletters of the catalog, and flag the ones already followed.
     */
    public function available() {
        $followed = Newsletter::where('user_id', auth()->user()->id)->get();

        return response()->json(array(
            'newsletters' => AvailableNewsletter::orderBy('name')
                ->get()
                ->map(function ($map) use ($followed) {
                    return array(
                        ...$map->toOption(),
                        'followed' => $this->isFollowed(array(
                            'available' => $map,
                            'followed' => $followed,
                        )),
                    );
                }),
        ));
    }

    /**
     * Find the feed of a newsletter of the catalog, on demand.
     */
    public function feed(AvailableNewsletter $availableNewsletter, SiteMetadataFinder $finder) {
        if (! $availableNewsletter->feed_url) {
            $availableNewsletter->feed_url = $finder->findFeedUrl($availableNewsletter->url);
            $availableNewsletter->save();
        }

        return response()->json(array(
            'feedUrl' => $availableNewsletter->feed_url,
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsletterRequest $request) {
        try {
            $data = (object) $request->validated();
            $url = $data->url;
            $f = FeedReader::read($url);

            if ($f->error()) {
                return back()->withErrors(array('url' => $f->error()));
            }

            $news = new Newsletter();
            $news->url = $url;
            $news->title = $f->get_title() ?: ($data->title ?? $url);
            $news->available_newsletter_id = $data->available_newsletter_id ?? null;
            $news->user_id = auth()->user()->id;

            $news->save();

            return Inertia::render('newsletter/NewsletterList', array(
                'news' => $this->getNewsletters(),
            ));
        } catch (\Exception $e) {
            return Inertia::render('newsletter/NewsletterList', array(
                'news' => Inertia::optional(
                    function () {
                        return $this->getNewsletters();
                    }
                ),
                'errors' => array('url' => $e->getMessage()),
            ));
        }
    }

    /**
     * Keep track of the last item the user read.
     */
    public function markAsRead(MarkNewsletterAsReadRequest $request, Newsletter $newsletter) {
        abort_if($newsletter->user_id !== auth()->user()->id, 403);

        $data = (object) $request->validated();

        $newsletter->markAsRead(array(
            'link' => $data->link,
            'title' => $data->title ?? null,
        ));

        return response()->json(array(
            'isRead' => true,
            'lastReadAt' => $newsletter->last_read_at?->toIso8601String(),
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Newsletter $newsletter) {
        //
    }

    /**
     * @param  array{available: AvailableNewsletter, followed: Collection}  $params
     */
    private function isFollowed(array $params): bool {
        return $params['followed']->contains(function ($newsletter) use ($params) {
            return $newsletter->available_newsletter_id === $params['available']->id
                || $newsletter->url === $params['available']->feed_url
                || $newsletter->url === $params['available']->url;
        });
    }
}
