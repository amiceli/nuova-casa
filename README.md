# Nuova casa

<img src="resources/assets/casa-logo.webp" alt="Nuova casa logo" width="120" />

An opinionated tool for managing bookmarks and newsletters built with Laravel and Vue 3.

## Technology

- Laravel 12
- Vue 3
- [NordHealth design system](https://nordhealth.design/)

## Run project

Easy with just: `just init`

## Environment variables

Copy `.env.example` to `.env`, defaults are fine except for these:

| Variable | Description |
| --- | --- |
| `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` | [GitHub OAuth app](https://github.com/settings/developers) credentials |
| `GITHUB_REDIRECT` | OAuth callback, `http://localhost/auth/callback` in local |
| `SEARXNG_URL` | [SearXNG](https://docs.searxng.org/) instance, searches bookmark icons |
| `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` | Websocket credentials, any values |
| `REVERB_HOST`, `REVERB_PORT`, `REVERB_SCHEME` | Websocket the browser connects to |
| `REVERB_SERVER_HOST`, `REVERB_SERVER_PORT` | Websocket the server listens on |
| `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, `VITE_REVERB_PORT`, `VITE_REVERB_SCHEME` | Same values, exposed to the front |

## Websocket

The bookmark import sends its progress over a websocket, with Reverb.

1. Fill the `REVERB_APP_*` credentials, they only have to match between the server and the client.
2. Keep `BROADCAST_CONNECTION=reverb` and the `VITE_REVERB_*` variables mapped on the `REVERB_*` ones.
3. Run `just reverb` for the websocket server and `just queue` for the worker — `just run` starts both.

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
- [ ] Onboarding
