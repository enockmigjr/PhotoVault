# ADR-002 - Protected media original storage

## Status
Accepted - incremental rollout in progress.

## Context
PhotoVault protects media through WordPress authorization, REST proxy previews, nonces and download checks. That protects normal application flows, but an original file stored only under `wp-content/uploads` can still be exposed if its public URL is known or guessed.

The platform needs a stronger default for protected and private originals while preserving fast thumbnail/preview rendering for galleries.

## Decision
For media controlled by `photovault-core`, originals for private or protected `media_item` posts are moved to:

```text
wp-content/photovault-private/originals/YYYY/MM/
```

The attachment keeps its generated metadata and image sizes, so gallery cards and detail previews can continue to use WordPress image derivatives. The original path is updated through `update_attached_file()` and is served only through the checked PhotoVault download endpoint.

The storage directories are hardened with `index.php` and Apache `.htaccess` denial files. Nginx deployments must add an explicit deny rule for `wp-content/photovault-private/` because Nginx does not read `.htaccess`.

## Security Rules
- Protected/private originals are not intentionally exposed through public URLs.
- Downloads still require login, REST nonce, verified identity for non-admin users and PhotoVault access policy checks.
- Existing sensitive media are migrated by an admin action in batches of 25.
- Failures are logged through the PhotoVault media audit trail without exposing file paths to visitors.

## Trade-offs
- Existing uploads must be processed after deployment; the migration is intentionally explicit to avoid moving large libraries silently.
- Public thumbnails/previews may still exist under uploads for display performance. They are not treated as originals.
- Server configuration remains required in production, especially for Nginx.

## Follow-up
- Add automated tests for ID guessing and unauthorized download attempts.
- Add Docker/Nginx deny rules for `wp-content/photovault-private/`.
- Use `wp photovault secure-originals --limit=100` for bulk migration once WP-CLI is installed in the environment.