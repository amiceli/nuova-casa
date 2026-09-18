<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller {
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response {
        return Inertia::render('settings/Profile', array(
            'status' => $request->session()->get('status'),
        ));
    }

    public function exportJson(Request $request): HttpResponse {
        return $this->download(
            json_encode($this->exportUser($request), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'nuova-casa-export.json',
            'application/json',
        );
    }

    public function exportBrowser(Request $request): HttpResponse {
        $user = $request->user()->load(array('tags.pages', 'newsletters'));
        $timestamp = now()->timestamp * 1000000;
        $root = fn (string $name, string $id, array $children) => array(
            'children' => $children,
            'date_added' => (string) $timestamp,
            'date_modified' => (string) $timestamp,
            'guid' => 'nuova-casa-'.$id,
            'id' => $id,
            'name' => $name,
            'type' => 'folder',
        );

        return $this->download(
            json_encode(array(
                'checksum' => '',
                'roots' => array(
                    'bookmark_bar' => $root('Bookmarks bar', '1', $this->bookmarkChildren($user)),
                    'other' => $root('Other bookmarks', '2', array()),
                    'synced' => $root('Synced bookmarks', '3', array()),
                ),
                'version' => 1,
            ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'nuova-casa-bookmarks.json',
            'application/json',
        );
    }

    public function exportHtml(Request $request): HttpResponse {
        $user = $request->user()->load(array('tags.pages', 'newsletters'));

        return $this->download($this->bookmarkHtml($user), 'nuova-casa-bookmarks.html', 'text/html; charset=UTF-8');
    }

    public function exportStyledHtml(Request $request): HttpResponse {
        $user = $request->user()->load(array('tags.pages', 'newsletters'));

        return $this->download($this->bookmarkHtml($user, true), 'nuova-casa-bookmarks-styled.html', 'text/html; charset=UTF-8');
    }

    private function bookmarkHtml($user, bool $styled = false): string {
        if ($styled) {
            return $this->styledBookmarkHtml($user);
        }

        $html = array(
            '<!DOCTYPE NETSCAPE-Bookmark-file-1>',
            '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">',
            '<TITLE>Nuova casa bookmarks</TITLE>',
            '<H1>Nuova casa bookmarks</H1>',
            '<DL><p>',
        );

        foreach ($user->tags as $tag) {
            $html[] = '    <DT><H3>'.e($tag->name).'</H3>';
            $html[] = '    <DL><p>';
            foreach ($tag->pages as $page) {
                $html[] = '        <DT><A HREF="'.e($page->url).'">'.e($page->title).'</A>';
            }
            $html[] = '    </DL><p>';
        }

        if ($user->newsletters->isNotEmpty()) {
            $html[] = '    <DT><H3>Newsletters</H3>';
            $html[] = '    <DL><p>';
            foreach ($user->newsletters as $newsletter) {
                $html[] = '        <DT><A HREF="'.e($newsletter->url).'">'.e($newsletter->title).'</A>';
            }
            $html[] = '    </DL><p>';
        }

        $html[] = '</DL><p>';

        return implode("\n", $html);
    }

    private function styledBookmarkHtml($user): string {
        $html = array(
            '<!DOCTYPE html>',
            '<html lang="fr">',
            '<head>',
            '    <meta charset="UTF-8">',
            '    <meta name="viewport" content="width=device-width, initial-scale=1">',
            '    <title>Nuova casa bookmarks</title>',
            '    <link rel="stylesheet" href="https://chr15m.github.io/DoodleCSS/doodle.css">',
            '    <style>@import url("https://fonts.googleapis.com/css2?family=Short+Stack&display=swap"); body { margin: 0; font-family: "Short Stack", cursive; } main { max-width: 800px; margin: auto; padding: 1em; } fieldset { margin: 2em 0; } ul { padding-left: 1.5em; }</style>',
            '</head>',
            '<body class="doodle">',
            '    <main>',
            '        <h1>Nuova casa bookmarks</h1>',
        );

        foreach ($user->tags as $tag) {
            $html[] = '        <fieldset>';
            $html[] = '            <legend>'.e($tag->name).'</legend>';
            $html[] = '            <ul>';
            foreach ($tag->pages as $page) {
                $favorite = $page->favorite
                    ? '⭐️ '
                    : '';
                $html[] = '                <li><a href="'.e($page->url).'">'.$favorite.e($page->title).'</a></li>';
            }
            $html[] = '            </ul>';
            $html[] = '        </fieldset>';
        }

        if ($user->newsletters->isNotEmpty()) {
            $html[] = '        <fieldset>';
            $html[] = '            <legend>Newsletters</legend>';
            $html[] = '            <ul>';
            foreach ($user->newsletters as $newsletter) {
                $html[] = '                <li><a href="'.e($newsletter->url).'">'.e($newsletter->title).'</a></li>';
            }
            $html[] = '            </ul>';
            $html[] = '        </fieldset>';
        }

        $html[] = '    </main>';
        $html[] = '</body>';
        $html[] = '</html>';

        return implode("\n", $html);
    }

    private function download(string|false $content, string $filename, string $contentType): HttpResponse {
        abort_if($content === false, 500, 'Could not generate export');

        return response($content)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function exportUser(Request $request): array {
        $user = $request->user()->load(array('tags.pages', 'newsletters'));

        return array(
            'exported_at' => now()->toIso8601String(),
            'user' => array('name' => $user->name, 'email' => $user->email),
            'tags' => $user->tags->map(fn ($tag) => array(
                'id' => $tag->id,
                'name' => $tag->name,
                'icon' => $tag->icon,
                'color' => $tag->color,
                'pages' => $tag->pages->map(fn ($page) => array(
                    'id' => $page->id,
                    'title' => $page->title,
                    'url' => $page->url,
                    'icon' => $page->icon,
                    'favorite' => (bool) $page->favorite,
                    'created_at' => $page->created_at?->toIso8601String(),
                ))->values()->all(),
            ))->values()->all(),
            'newsletters' => $user->newsletters->map(fn ($newsletter) => array(
                'id' => $newsletter->id,
                'title' => $newsletter->title,
                'url' => $newsletter->url,
                'created_at' => $newsletter->created_at?->toIso8601String(),
                'last_read_at' => $newsletter->last_read_at?->toIso8601String(),
                'last_read_link' => $newsletter->last_read_link,
                'last_read_title' => $newsletter->last_read_title,
            ))->values()->all(),
        );
    }

    private function bookmarkChildren($user): array {
        $children = $user->tags->map(fn ($tag) => array(
            'children' => $tag->pages->map(fn ($page) => array(
                'date_added' => (string) (($page->created_at?->timestamp ?? now()->timestamp) * 1000000),
                'guid' => 'nuova-casa-page-'.$page->id,
                'id' => (string) ($page->id + 10),
                'name' => $page->title,
                'type' => 'url',
                'url' => $page->url,
            ))->values()->all(),
            'date_added' => (string) (($tag->created_at?->timestamp ?? now()->timestamp) * 1000000),
            'date_modified' => (string) (($tag->updated_at?->timestamp ?? now()->timestamp) * 1000000),
            'guid' => 'nuova-casa-tag-'.$tag->id,
            'id' => (string) ($tag->id + 100000),
            'name' => $tag->name,
            'type' => 'folder',
        ))->values()->all();

        if ($user->newsletters->isNotEmpty()) {
            $timestamp = now()->timestamp * 1000000;
            $children[] = array(
                'children' => $user->newsletters->map(fn ($newsletter) => array(
                    'date_added' => (string) (($newsletter->created_at?->timestamp ?? now()->timestamp) * 1000000),
                    'guid' => 'nuova-casa-newsletter-'.$newsletter->id,
                    'id' => (string) ($newsletter->id + 200000),
                    'name' => $newsletter->title,
                    'type' => 'url',
                    'url' => $newsletter->url,
                ))->values()->all(),
                'date_added' => (string) $timestamp,
                'date_modified' => (string) $timestamp,
                'guid' => 'nuova-casa-newsletters',
                'id' => '200000',
                'name' => 'Newsletters',
                'type' => 'folder',
            );
        }

        return $children;
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validate(array(
            'confirmation' => array('required', function ($attribute, $value, $fail) {
                $expected = auth()->user()->name.'/'.auth()->user()->email;
                if (trim($value) !== $expected) {
                    $fail('profile_confirmation_invalid');
                }
            }),
        ), array(
            'confirmation.required' => 'profile_confirmation_required',
        ));

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
