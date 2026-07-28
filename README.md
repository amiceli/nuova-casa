# Nuova casa

<img src="resources/assets/casa-logo.webp" alt="Nuova casa logo" width="120" />

An opinionated tool for managing bookmarks and newsletters built with Laravel and Vue 3.

## Technology

- Laravel 12
- Vue 3
- [NordHealth design system](https://nordhealth.design/)

## Features

- Sign in with GitHub
- Profile and appearance settings
- Create and delete tags
- Create and delete bookmarks
- Auto fill bookmarks from Open Graph metadata
- Search an icon with SearXNG
- Mark a bookmark as favorite
- Dashboard of favorite bookmarks
- Search bookmarks by title or URL
- Add RSS feeds and read their latest article
- Search a newsletter in a catalog filled from [awesome-newsletters](https://github.com/zudochkin/awesome-newsletters)
- Know if a newsletter is read or unread
- Onboarding
- Import the bookmarks exported from a browser

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
| `QUEUE_CONNECTION` | `sync`, the scheduled commands run their jobs themselves |

## Scheduled commands

Everything slow happens there, nothing is queued while a user waits.

| Command | When | What |
| --- | --- | --- |
| `newsletters:sync-catalog` | 1st of the month | Fills the catalog from awesome-newsletters |
| `newsletters:sync-logos` | 2nd of the month | Looks for the logo and the feed of the newest entries |
| `bookmarks:sync-icons` | Every Monday | Looks for the icons of the imported tags and links |

In local, `just schedule` runs them. In production, either a cron on
`php artisan schedule:run` every minute, or one scheduled task per command.
