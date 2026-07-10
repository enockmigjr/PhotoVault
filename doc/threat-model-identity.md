# Threat model - Identity Security Kit

Derniere mise a jour: 2026-07-09

## Scope

Ce threat model couvre `identity-security-kit`: login, inscription, profil, avatar, reset password, verification email, renvoi de verification, reglages et audit identite.

## Actifs a proteger

- Comptes utilisateurs et sessions WordPress.
- Emails, statut de verification et challenges email.
- Reset password keys et flux de changement de mot de passe.
- Avatar upload et metadonnees profil.
- Reglages de politiques d'identite.
- Audit identite.
- Futures preuves OTP, TOTP secrets et recovery codes.

## Acteurs

| Acteur | Objectif probable |
| --- | --- |
| Visiteur anonyme | Creer un compte, enumerer des emails, brute force login/reset |
| Utilisateur connecte | Modifier profil ou contourner verification email |
| Compte compromis | Elever privileges ou acceder wp-admin |
| Admin identity | Configurer les politiques et consulter audit |
| Attaquant automatise | Spam inscription, reset flood, credential stuffing |

## Frontieres de confiance

- Formulaires publics login/register/forgot vers `template_redirect`.
- Formulaire profil authentifie vers update user/avatar.
- Liens email tokenises vers `admin-post.php`.
- Admin Identity Kit vers options et audit.
- WordPress user/session APIs vers tables custom d'audit et challenges.

## Menaces principales

| Menace | Scenario | Controle actuel | Gap/test requis |
| --- | --- | --- | --- |
| Enumeration email | Forgot-password indique si email existe | Reponse anti-enumeration | Tester messages et redirections |
| Token verification vole | Lien email reutilise ou expire non respecte | Token hash, statut pending, expiration | Tester single-use/expiration |
| CSRF reglages | Modifier politiques via admin-post | Capability + nonce | Test non-admin et nonce invalide |
| Bypass verification | User non verifie telecharge media sensible | PhotoVault verifie `identity_security_kit_is_email_verified` | Tests integration media/identity |
| Upload avatar abusif | Upload non image ou trop lourd | MIME, taille, dimensions bornees | Tests extension/MIME/dimension |
| Reset password abuse | Flood mails ou brute force reset | Anti-enumeration, hooks audit | Ajouter rate limiting plus strict |
| Privilege escalation role | Inscription cree role trop privilegie via filtre | Role filtrable sanitize | Documenter allowlist cible/test filtre |
| Audit fuite secret | Logs contiennent reset key/token/IP brute | Audit nettoye/hache | Tests absence secrets logs |
| Session persistante risquee | Apres changement sensible, sessions restent actives | Non implemente | Ajouter invalidation sessions |
| MFA absent | Compte admin protege seulement par mot de passe | Non implemente | OTP/TOTP/recovery codes |

## Controles existants

- Validation serveur registration/profile/reset.
- Reglages bornes cote serveur.
- Verification email par token long hashe, statut et expiration.
- Renvoi verification avec session + nonce.
- Reset password anti-enumeration.
- Audit identite sans secrets, reset keys ni IP brute.
- Avatar limite par MIME, taille et dimensions.
- Capabilities dediees: `identity_manage_settings`, `identity_manage_security`, `identity_view_security_audit`.

## Gaps prioritaires

1. Ajouter OTP email avec expiration, tentatives, anti-replay et rate limiting.
2. Ajouter TOTP/MFA et recovery codes.
3. Ajouter grace period MFA et enforcement wp-admin privilegie.
4. Ajouter invalidation de sessions sur changement email/password/MFA.
5. Rate limiting login/register/forgot/resend verification ajoute avec seuils admin bornes.
6. Ajouter tests automatises sur tokens, replay et audit sans secrets.

## Tests minimum avant production

1. Forgot password retourne une reponse neutre pour email connu/inconnu.
2. Verification email valide marque le compte verifie.
3. Token expire est refuse et marque expire.
4. Token deja consomme est refuse.
5. Renvoi verification refuse anonyme et nonce invalide.
6. Reglages refusent utilisateur sans `identity_manage_settings`.
7. Reglages bornent les valeurs extremes.
8. Avatar refuse MIME/extension/dimension invalides.
9. Audit ne contient pas token brut, reset key, mot de passe ou IP brute.
10. Integration media refuse download sensible a user non verifie.
