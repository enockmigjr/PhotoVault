# SECURITY - PhotoVault Platform

## Security Posture
PhotoVault is being migrated from a theme-heavy implementation to a modular WordPress platform. The current direction is deny by default, least privilege and server-side enforcement.

## Current Protections

### Authentication and Identity
- Front-office login, registration, profile update and forgot-password flows are handled by `identity-security-kit` when active.
- Registration and profile updates validate inputs server-side.
- Password length is configurable but bounded with a minimum of 8 characters.
- Forgot-password responses are anti-enumeration: unknown accounts receive the same user-facing response as known accounts.
- Sensitive identity events are logged in `wp_identity_security_audit` without passwords, reset keys, raw IP addresses or secrets.

### Media Access
- Sensitive media events are logged in `wp_photovault_media_audit`: views, previews, protected previews, downloads, denied access and access grants.
- `photovault-core` owns media REST routes, upload validation, capabilities and protected image serving.
- Gallery list endpoints require authentication.
- Admins and users with PhotoVault capabilities can see all managed media.
- Private media are denied to non-admin/non-owner users in REST and image proxy code.
- Protected media downloads require a logged-in user and REST nonce, and are denied to unauthorized viewers.
- List/card views use thumbnail/preview variants instead of original HD files.
- For protected or private media controlled by PhotoVault, original files are moved to `wp-content/photovault-private/originals` and served only through the checked download endpoint.

### Newsletter
- Newsletter subscriptions use a real POST handler, nonce validation and explicit consent.
- Subscriber metadata stores hashed IP data instead of raw IP addresses.
- Public unsubscribe uses a server-side token and does not expose the email address in the URL.
- Admin subscriber management requires dedicated newsletter capabilities.
- Campaign delivery supports `wp_mail` and a generic HTTPS API with server-side-only credentials and stable idempotency keys.
- Bounce and complaint events require a timestamped HMAC signature, use a durable replay guard and never store the raw email in the event registry.

## Known Gaps
- Existing originals uploaded before the private-storage migration must be processed from the PhotoVault admin workspace. Nginx deployments must still deny direct access to `wp-content/photovault-private/` explicitly.
- Identity browser E2E, a real SMS provider, international phone-plan validation and multisite policy coverage remain incomplete.
- Newsletter still needs a real provider staging validation, delivery monitoring, anti-abuse/double opt-in, tracking and advanced exports.
- Automated authorization coverage is complete for Core media but remains partial for Identity, Newsletter and admin-post routes.

## Operational Rules
- Do not commit secrets, SMTP credentials, provider tokens or database passwords.
- Do not log passwords, OTP values, TOTP secrets, reset keys or full provider tokens.
- Keep all mutating admin actions behind nonces and capabilities.
- Use `$wpdb->prepare()` for custom SQL reads with user input.
- Prefer WordPress APIs for user/session/password flows.
- Keep active plugin copies synchronized with source plugin copies until release packaging is finalized.

## Reporting
Security issues should be handled privately with the site owner/development team. Do not disclose exploitable details in public issues or comments.
## Identity verification gates

- When Identity Security Kit is active, non-admin users must have a verified email address before opening private media previews or downloading original files.
- Administrators and PhotoVault media managers keep full access for support and moderation workflows.
