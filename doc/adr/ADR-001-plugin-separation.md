# ADR-001 - Separation theme, PhotoVault Core, Identity Kit and Newsletter Kit

## Status
Accepted - migration complete.

## Context
PhotoVault started as a WordPress theme carrying presentation, media rules, auth handlers, upload validation, REST routes and newsletter placeholders in the same codebase. The target platform must be modular, reusable and secure enough to be maintained by another team.

## Decision
Responsibilities are split into four layers:

- `PhotoVault` theme: presentation, templates, editorial experience and frontend assets.
- `photovault-core`: product-specific media, gallery, thumbnails, protected image proxy, downloads, roles, capabilities, media admin workspaces and access reporting.
- `identity-security-kit`: reusable identity handlers, profile validation, forgot-password flow, security settings and audit trail.
- `newsletter-campaign-kit`: reusable subscriber storage, consent capture, subscriber admin workspace and campaign foundation.

The theme no longer loads business fallbacks. Missing required plugins are reported to administrators instead of silently activating duplicate implementations.

## Security Rules
- Authorization is enforced server-side, not by hiding buttons.
- Sensitive actions use nonces plus capability checks where relevant.
- Media downloads require a logged-in session, REST nonce and access policy checks.
- Protected media previews are served through the PhotoVault proxy and watermarked for unauthorized viewers.
- Identity audit events do not store passwords, reset keys, raw IP addresses or complete secrets.
- Newsletter consent is stored server-side with hashed IP metadata.

## Consequences
- Plugins can evolve independently and be reused outside the PhotoVault theme.
- Active plugin copies under `wp-content/plugins` must stay synchronized with the source copies under `themes/PhotoVault/plugins` until packaging is finalized.
- Runtime validation still depends on an available WordPress database. MySQL/XAMPP must be running before activation, schema upgrades and admin screens can be verified end-to-end.

## Operational rule
The active plugin repositories under `wp-content/plugins` are the runtime source of truth. Theme copies are synchronized distribution mirrors only.
