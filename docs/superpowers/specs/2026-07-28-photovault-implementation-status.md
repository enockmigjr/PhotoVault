# PhotoVault implementation status

Date: 2026-07-28

## Progress

- Functional implementation: 14/14 workstreams complete.
- Functional acceptance: 14/14 validation gates complete.
- Deployment: local Docker stack built, started and healthy.

## Completed workstreams

1. Shared asynchronous browser runtime, REST envelope, busy states, errors,
   dialogs, focus handling and dismissible notifications.
   Login, registration, forgotten-password and reset forms preserve their
   native fallback while returning structured AJAX responses.
2. Clean public fallback URLs without exposing `wp-admin` or
   `admin-post.php` in customer-facing actions.
3. Asynchronous contact, protected-access, shooting and cancellation flows.
4. Asynchronous profile, avatar, phone, email, password and preferences.
5. Guided MFA login and account setup for TOTP, email, SMS and recovery codes.
6. Administrator identity actions for MFA reset, grace restart, verification
   resend and session revocation.
7. Two-step newsletter subscription, topic selection, confirmation,
   preferences and unsubscribe flows.
8. Progressive asynchronous Newsletter and Identity administration, including
   forms, filters, pagination and workflow actions.
   PhotoVault Core settings, access requests and media audits now use the same
   progressive model, while dashboard section navigation uses History API.
9. One-time and recurring campaigns with dynamic WordPress content and
   immutable delivery audiences.
10. Signed first-party open, click and conversion tracking with privacy export
    and erasure support.
11. Campaign funnel, timeline, links, device approximation and topic reports.
12. Studio, provider, retention, queue, authentication and operational
    settings with bounded sanitization.
13. Dedicated Docker cron runner, health checks and deployment operations
    documentation.
14. Active plugin repositories committed and synchronized byte-for-byte with
    the three theme mirrors.

## Validation evidence

- All touched PHP and JavaScript syntax checks passed.
- PhotoVault Core WordPress runtime suite: 6/6 passed.
- Identity standalone tests passed; Identity WordPress runtime scenarios passed,
  including the asynchronous authentication envelope.
- Newsletter standalone and WordPress runtime scenarios passed, including
  clean preference/unsubscribe routes, signed open-click-conversion tracking
  and recurring occurrence creation.
- Runtime acceptance totals 23/23 scenarios: Identity 5, Core 6, Newsletter 12.
- Browser acceptance confirms asynchronous invalid/valid login, dashboard
  History API with back/forward, PhotoVault settings, access filters, audit
  pagination and asynchronous avatar upload without document navigation.
- Playwright accessibility, admin consistency, keyboard/fullscreen and provider
  suites passed. No serious or critical WCAG violation was reported.
- Plugin mirror verification passed: Identity 47 files, Newsletter 55 files,
  Core 30 files.
- Docker Compose configuration and image builds passed.
- WordPress, MariaDB, Nginx, Mailpit and cron containers are healthy.
- The public site returns HTTP 200 and recent runtime logs contain no fatal PHP
  error or uncaught exception.
- A due newsletter cron event ran successfully through WP-CLI.

## Remaining maintenance debt

The PHPCS baseline must be reviewed and regenerated in a dedicated formatting
pass. It predates this delivery and currently mixes line-ending issues,
historical formatting debt and WordPress sniff false positives. It was not
rewritten silently because that would create a large unrelated diff. This does
not leave a known functional workflow incomplete.

## Architectural note

Native WordPress administration keeps its existing capability-checked
handlers. The Identity and Newsletter admin runtime submits them
asynchronously and replaces only the affected application surface. Public and
account mutations use domain REST routes. This preserves WordPress fallback
behavior while delivering the approved no-reload user experience.
