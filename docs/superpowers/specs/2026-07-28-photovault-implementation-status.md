# PhotoVault implementation status

Date: 2026-07-28

## Progress

- Functional implementation: 14/14 workstreams complete.
- Automated acceptance: 12/14 validation gates complete.
- Deployment: local Docker stack built, started and healthy.

## Completed workstreams

1. Shared asynchronous browser runtime, REST envelope, busy states, errors,
   dialogs, focus handling and dismissible notifications.
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

- All PHP and JavaScript syntax checks passed.
- PhotoVault Core WordPress runtime suite: 6/6 passed.
- Identity standalone tests passed; Identity WordPress runtime scenarios passed
  after aligning the TOTP test with the prepared-method state machine.
- Newsletter standalone and WordPress runtime scenarios passed after aligning
  confirmation and unsubscribe tests with the clean public routes.
- Plugin mirror verification passed: Identity 46 files, Newsletter 53 files,
  Core 30 files.
- Docker Compose configuration and image builds passed.
- WordPress, MariaDB, Nginx, Mailpit and cron containers are healthy.
- The public site returns HTTP 200 and recent runtime logs contain no fatal PHP
  error or uncaught exception.
- A due newsletter cron event ran successfully through WP-CLI.

## Remaining validation gates

1. Authenticated Playwright acceptance remains to run with
   `PHOTOVAULT_TEST_USERNAME` and `PHOTOVAULT_TEST_PASSWORD`. This covers final
   visual, keyboard, gallery fullscreen, provider-admin and accessibility
   evidence; it does not block the implemented server flows.
2. The PHPCS baseline must be reviewed and regenerated in a dedicated quality
   pass. It predates the delivered files and currently reports formatting,
   mixed line endings and WordPress sniff false positives alongside the known
   historical debt. It was deliberately not rewritten silently.

## Architectural note

Native WordPress administration keeps its existing capability-checked
handlers. The Identity and Newsletter admin runtime submits them
asynchronously and replaces only the affected application surface. Public and
account mutations use domain REST routes. This preserves WordPress fallback
behavior while delivering the approved no-reload user experience.
