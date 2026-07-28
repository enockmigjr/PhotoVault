# PhotoVault asynchronous platform redesign

Date: 2026-07-28
Status: approved

## Objective

Move PhotoVault from redirect-driven WordPress workflows to a professional
hybrid application. WordPress keeps server rendering, SEO, permissions and
non-JavaScript fallbacks. Mutations and workflow transitions use domain REST
routes with consistent loading, success and error states.

## Boundaries

- WordPress Core is not modified.
- Business rules remain in the three active plugins.
- The theme owns presentation and the shared browser runtime.
- Public URLs and email calls-to-action do not expose `wp-admin` or
  `admin-post.php`.
- Native `wp-admin` page-to-page navigation remains WordPress navigation.
  Forms, row actions and workflow dialogs become asynchronous.
- Every plugin change is made in the active repository, synchronized to its
  theme mirror and verified by checksum.

## Architecture

### Shared application runtime

The theme provides a small vanilla JavaScript runtime for REST requests,
form serialization, field-level errors, accessible dialogs, toasts, busy
states, request cancellation and History API navigation. Responses use one
envelope:

```json
{
  "success": true,
  "message": "Human-readable status",
  "data": {},
  "errors": {},
  "meta": {}
}
```

Server-rendered forms remain valid fallbacks. JavaScript intercepts only forms
that declare a REST endpoint.

### Identity and MFA

The MFA login is a state machine:

1. Show the preferred method and masked destination.
2. Let the user confirm that method.
3. Send a remote code when required.
4. Show the code field only after method preparation.
5. Offer "Use another method" as a separate choice screen.
6. Verify and complete the original redirect without exposing `wp-login.php`.

Profile security actions use the same interaction model. Recovery codes can be
copied as one bounded operation. Administrator user rows expose capability-
checked quick actions for factor reset, grace restart, verification resend and
session revocation. Every action is nonced, audited and notified.

### PhotoVault frontend

Contact, protected-access requests, shootings, cancellation, profile,
preferences, favorites and newsletter subscription use REST mutations. The
dashboard updates only the affected region and preserves focus, URL and
browser history. Gallery previews continue to load bounded image variants.

### Newsletter

Subscription becomes a two-step flow: email and consent first, then an
accessible topic-selection dialog. Audience, topics, lists, tags, segments,
templates, blocks, campaigns, queue and settings receive REST controllers and
modal or inline editing.

Campaigns support one-time and recurring schedules. Content sources include
manual content, selected posts, newest posts, posts from a rolling period,
categories, tags and post types. Audience snapshots remain immutable per
delivery occurrence.

### Tracking and reports

Signed public endpoints record privacy-bounded open, click, conversion,
unsubscribe, bounce and complaint events. Links are rewritten through a
validated redirect endpoint; only permitted HTTP(S) destinations are accepted.
Reports expose campaign funnels, audience growth, delivery health, engagement,
links, devices when available, topics and time series. Open tracking is clearly
identified as approximate because mail clients can preload pixels.

### Scheduling

Docker keeps its dedicated WP-CLI cron container with opportunistic WP-Cron
disabled. Non-Docker deployment documentation provides system cron commands,
health checks and missed-run warnings. Recurrence generation and queue
processing use locks and idempotency keys.

### Settings

Settings are grouped by domain, sanitized, bounded and versioned. Additions
cover theme identity, contact/studio details, dashboard presentation, media
defaults, authentication policy, newsletter defaults, tracking retention,
recurrence limits and operational health.

## Security

- REST nonces for authenticated browser mutations.
- Public rate limits, neutral messages and anti-abuse controls.
- Capability checks and ownership checks remain server-side.
- No raw OTP, token, API secret, phone number or email in logs.
- Signed expiring email actions and tracking identifiers.
- Safe redirect allowlists and same-origin return URLs.
- Idempotency for submissions, campaign occurrences and provider webhooks.

## Delivery order

1. Shared REST/browser foundation and frontend PhotoVault mutations.
2. MFA login, profile security and user quick actions.
3. Newsletter subscription and administration.
4. Recurring campaigns, dynamic post content and tracking schema.
5. Reports, settings, cron operations and documentation.
6. Mirror synchronization, Docker build and delegated acceptance checklist.

## Completion evidence

Completion requires current code and runtime evidence for every requested
flow, mirror equality for all three plugins, updated READMEs and operations
documentation, a successful Docker build, and a requirement-by-requirement
review. Syntax checks alone are not sufficient evidence.
