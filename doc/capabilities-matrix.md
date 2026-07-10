# Matrice capabilities PhotoVault

Derniere mise a jour: 2026-07-10

Objectif: rendre visibles les roles, capabilities et controles d'acces deja presents afin de guider les tests et la future delegation fine des permissions.

## Roles et principes

| Role | Source | Etat actuel | Usage attendu |
| --- | --- | --- | --- |
| `administrator` | WordPress | Recoit toutes les capabilities PhotoVault Core, Identity Security Kit et Newsletter Campaign Kit a l'activation/upgrade | Supervision complete, reglages, exports, media, identite, newsletter |
| `client` | PhotoVault Core/theme legacy | Cree a l'activation du theme avec `read` uniquement | Compte public connecte, acces a ses medias, grants et preferences |
| Media manager | Cible par capability | Pas encore role separe cree automatiquement | Delegation future de `photovault_manage_media` sans donner tout WordPress |
| Identity manager | Cible par capability | Pas encore role separe cree automatiquement | Delegation future des reglages et audits identity |
| Newsletter manager | Cible par capability | Pas encore role separe cree automatiquement | Delegation future abonnements, campagnes, rapports |

Note: `photovault_current_user_can()` et `photovault_user_can()` acceptent aussi `manage_options` comme fallback admin. Les plugins Identity et Newsletter utilisent surtout `current_user_can()` sur leurs capabilities dediees.

## PhotoVault Core

| Capability | Attribuee actuellement | Zones protegees | Commentaire |
| --- | --- | --- | --- |
| `photovault_manage_platform` | `administrator` | Acces wp-admin PhotoVault, restriction admin, statistiques globales | Capability de supervision generale |
| `photovault_manage_media` | `administrator` | Admin media, demandes d'acces, grants, audit media, secure originals, bypass email verification | Capability la plus sensible cote media |
| `photovault_view_private_media` | `administrator` | Cible prevue pour lecture private media | Peu utilisee directement pour l'instant; les grants/ownership portent l'acces fin |
| `photovault_manage_settings` | `administrator` | Reglages PhotoVault Core | A renforcer avec pages settings dediees |
| `upload_files` | Role WordPress standard | Upload media frontend/admin | Controle combine avec ownership et validation serveur |

### Decisions actuelles

- Les utilisateurs non privilegies sont rediriges hors `wp-admin`.
- Les medias prives ne sont visibles que si l'utilisateur est owner, admin/media manager ou beneficie d'un grant applicable.
- Les admins/media managers gardent acces complet aux previews, downloads et outils de securisation des originaux.
- Les downloads sensibles exigent une session, un nonce REST et une identite verifiee pour les non privilegies.

## Identity Security Kit

| Capability | Attribuee actuellement | Zones protegees | Commentaire |
| --- | --- | --- | --- |
| `identity_manage_settings` | `administrator` | Menu Identity Kit, sauvegarde reglages | Controle principal des politiques exposees |
| `identity_manage_security` | `administrator` | Cible securite avancee | Reserve aux futurs OTP/MFA/recovery codes |
| `identity_view_security_audit` | `administrator` | Onglet/journal audit identite | Lecture d'evenements sensibles sans secrets |

### Decisions actuelles

- Les liens de verification email sont publics par design, proteges par token long hashe, statut pending et expiration.
- Le renvoi de verification email exige une session et un nonce.
- Les reglages bornent les valeurs serveur: longueur mot de passe, avatar, TTL verification et delai de renvoi.

## Newsletter Campaign Kit

| Capability | Attribuee actuellement | Zones protegees | Commentaire |
| --- | --- | --- | --- |
| `newsletter_manage_subscribers` | `administrator` | Menu abonnes, changement de statut | Capability operationnelle actuelle |
| `newsletter_manage_lists` | `administrator` | Listes, tags et segments | Operationnelle pour segmentation de base |
| `newsletter_create_campaigns` | `administrator` | Creation brouillons et transitions non-envoi | Operationnelle pour base campagnes |
| `newsletter_send_campaigns` | `administrator` | Transitions scheduled/sending/sent, traitement queue | Tres sensible: provider reel encore non branche |
| `newsletter_view_reports` | `administrator` | Export CSV abonnes, futurs rapports | Donne acces a des emails en clair |
| `newsletter_manage_settings` | `administrator` | Reglages provider wp_mail/adaptateur externe | Sensible: ne doit pas stocker de secret API |

### Decisions actuelles

- L'inscription newsletter est publique par design, mais protegee par nonce, consentement et validation email.
- La desinscription est publique par token, sans email brut dans l'URL.
- L'export CSV contient des emails en clair et doit rester limite aux roles de confiance.
- Les campagnes utilisent des transitions serveur: creation via newsletter_create_campaigns, passage vers scheduled/sending/sent via newsletter_send_campaigns.
- La queue batch exige newsletter_send_campaigns et applique retry/backoff avant provider reel.
- Les reglages provider exigent newsletter_manage_settings et ne stockent pas de secret API.

## Matrice cible de delegation

| Profil futur | Capabilities minimales | A ne pas donner par defaut |
| --- | --- | --- |
| Media manager | `read`, `upload_files`, `photovault_manage_media`, `photovault_view_private_media` | `manage_options`, `identity_manage_settings`, `newsletter_view_reports` |
| Curator/Archiviste | `read`, `upload_files`, `photovault_view_private_media` | `photovault_manage_platform`, `photovault_manage_settings` |
| Identity manager | `read`, `identity_manage_settings`, `identity_view_security_audit` | `manage_options`, `newsletter_send_campaigns` |
| Security officer | `read`, `identity_manage_security`, `identity_view_security_audit` | Reglages plateforme non lies a la securite |
| Newsletter operator | `read`, `newsletter_manage_subscribers`, `newsletter_manage_lists`, `newsletter_create_campaigns` | `newsletter_send_campaigns`, `newsletter_manage_settings` |
| Newsletter publisher | `read`, `newsletter_send_campaigns`, `newsletter_view_reports` | `manage_options`, identity capabilities |

## Tests a ajouter

1. Verifier qu'un `client` ne peut pas acceder a `wp-admin` ni aux admin-post privilegies.
2. Verifier qu'un utilisateur connecte sans grant ne peut pas lire un media prive par REST ou secure-image.
3. Verifier qu'un media manager peut approuver une demande d'acces et securiser les originaux existants.
4. Verifier qu'un utilisateur avec `newsletter_manage_subscribers` mais sans `newsletter_view_reports` ne voit pas l'export CSV.
5. Verifier qu'un utilisateur avec `identity_view_security_audit` mais sans `identity_manage_settings` ne peut pas sauvegarder les reglages.
6. Verifier que `manage_options` garde le role de super-admin applicatif dans PhotoVault Core.
7. Verifier qu'un utilisateur avec newsletter_create_campaigns mais sans newsletter_send_campaigns ne peut pas passer une campagne en scheduled, sending ou sent.

## Gaps

- Creer des roles dedies n'est pas encore implemente automatiquement hors `client`.
- Les capabilities Identity/Newsletter sont toutes attribuees aux administrateurs, mais pas encore proposees dans une UI de delegation.
- La matrice doit etre transformee en tests automatises avant de declarer la plateforme production-ready.
