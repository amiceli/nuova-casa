# Nuova casa

<img src="resources/assets/casa-logo.webp" alt="Nuova casa logo" width="120" />

An opinionated tool for managing bookmarks and newsletters built with Laravel and Vue 3.

## Technology

- Laravel 12
- Vue 3
- [NordHealth design system](https://nordhealth.design/)

## Features

- [x] Sign in with GitHub
- [x] Profile and appearance settings
- [x] Create and delete tags
- [x] Create and delete bookmarks
- [x] Auto fill bookmarks from Open Graph metadata
- [x] Search an icon with SearXNG
- [x] Mark a bookmark as favorite
- [x] Dashboard of favorite bookmarks
- [x] Search bookmarks by title or URL
- [x] Add RSS feeds and read their latest article
- [x] Search a newsletter in a catalog filled from [awesome-newsletters](https://github.com/zudochkin/awesome-newsletters)
- [x] Know if a newsletter is read or unread
- [x] Onboarding
- [x] Import the bookmarks exported from a browser

## Run project

Easy with just: `just init`

## Environment variables

Copy `.env.example` to `.env`, defaults are fine except for these:

| Variable | Description |
| --- | --- |
| `GITHUB_CLIENT_ID` | [GitHub OAuth app](https://github.com/settings/developers) client id |
| `GITHUB_CLIENT_SECRET` | Client secret of the same app |
| `GITHUB_REDIRECT` | OAuth callback, `http://localhost/auth/callback` in local |
| `SEARXNG_URL` | [SearXNG](https://docs.searxng.org/) instance, searches bookmark icons |
| `BROADCAST_CONNECTION` | `reverb`, anything else drops the import progress |
| `REVERB_APP_ID` | Websocket app id, any value |
| `REVERB_APP_KEY` | Websocket key, shared with the browser |
| `REVERB_APP_SECRET` | Websocket secret, server side only |
| `REVERB_HOST` | Host the browser connects to |
| `REVERB_PORT` | Port the browser connects to |
| `REVERB_SCHEME` | `http` in local, `https` behind TLS |
| `REVERB_SERVER_HOST` | Host the websocket binds on, `0.0.0.0` in a container |
| `REVERB_SERVER_PORT` | Port the websocket listens on |
| `REVERB_PUBLISH_HOST` | Where the server pushes events, the websocket container. Falls back on `REVERB_HOST` |
| `REVERB_PUBLISH_PORT` | Same, falls back on `REVERB_PORT` |
| `REVERB_PUBLISH_SCHEME` | Same, `http` inside the network even when the browser is on `https` |
| `VITE_REVERB_APP_KEY` | `REVERB_APP_KEY`, baked into the front at build time |
| `VITE_REVERB_HOST` | `REVERB_HOST`, baked into the front at build time |
| `VITE_REVERB_PORT` | `REVERB_PORT`, baked into the front at build time |
| `VITE_REVERB_SCHEME` | `REVERB_SCHEME`, baked into the front at build time |

## Websocket

The bookmark import sends its progress over a websocket, with Reverb.

1. Fill the `REVERB_APP_*` credentials, they only have to match between the server and the client.
2. Keep `BROADCAST_CONNECTION=reverb` and the `VITE_REVERB_*` variables mapped on the `REVERB_*` ones.
   The websocket runs in its own container, so the server pushes to `REVERB_PUBLISH_HOST`
   while the browser connects to `REVERB_HOST`. Both composes set it already.
3. `just run` starts the websocket and the worker as containers, `just reverb` and `just queue` follow their logs.
