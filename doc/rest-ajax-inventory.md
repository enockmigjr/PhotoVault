# Inventaire REST/AJAX et admin-post

Derniere mise a jour: 2026-07-15

Objectif: classer les points d'entree publics, authentifies et privilegies afin de preparer les tests de securite REST/AJAX, CSRF, IDOR et privilege escalation.

## Synthese

| Zone | Endpoint/action | Exposition | Controle principal | Etat |
| --- | --- | --- | --- | --- |
| PhotoVault Core | `GET /wp-json/photovault/v1/media` | Authentifie | `is_user_logged_in`, sanitizers REST, filtrage `photovault_user_can_access_media` | A tester |
| PhotoVault Core | `GET /wp-json/photovault/v1/secure-image` | Public transport | Validation ID/display/download, controles internes private/protected/download, nonce download, audit | A tester |
| PhotoVault Core | `GET /wp-json/photovault/v1/favorites` | Authentifie | Cookie WordPress + nonce REST, utilisateur courant uniquement | Runtime isolation valide |
| PhotoVault Core | `POST/DELETE /wp-json/photovault/v1/favorites/{id}` | Authentifie | Cookie WordPress + nonce REST, ownership strict, media accessible, mutation idempotente | Runtime isolation valide |
| PhotoVault Core | `admin_post_photovault_update_access_request_status` | Admin | `photovault_manage_media`, nonce par demande | A tester |
| PhotoVault Core | `admin_post_photovault_secure_existing_originals` | Admin | `photovault_manage_media`, nonce global | A tester |
| PhotoVault Core | `admin_post_photovault_create_shooting` | Authentifie | Nonce, identite verifiee, ownership, e-mail du compte, validation, anti-doublon et rate limit | Runtime service valide |
| PhotoVault Core | `admin_post_photovault_shooting_transition` | Authentifie/Admin | Nonce par reservation, owner limite a annulation, capability pour confirmation/completion | Runtime lifecycle valide |
| Identity Security Kit | `admin_post_identity_security_kit_save_settings` | Admin | `identity_manage_settings`, nonce reglages | A tester |
| Identity Security Kit | `admin_post_nopriv_identity_security_kit_verify_email` | Public lien email | Token long, hash serveur, statut pending, expiration | A tester |
| Identity Security Kit | `admin_post_identity_security_kit_verify_email` | Authentifie lien email | Token long, hash serveur, statut pending, expiration | A tester |
| Identity Security Kit | `admin_post_identity_security_kit_resend_email_verification` | Authentifie | Session, nonce, politique de renvoi | A tester |
| Identity Security Kit | `admin_post_identity_security_kit_email_otp_request` | Authentifie | Session, nonce lie au purpose, cooldown DB | A tester |
| Identity Security Kit | `admin_post_identity_security_kit_email_otp_verify` | Authentifie | Session, nonce lie au purpose, ownership, expiration, essais, consommation atomique | A tester |
| Newsletter Campaign Kit | `admin_post_nopriv_newsletter_campaign_kit_subscribe` | Public formulaire | Nonce, consentement, email valide, IP hash | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_subscribe` | Authentifie formulaire | Nonce, consentement, email valide, IP hash | A tester |
| Newsletter Campaign Kit | `admin_post_nopriv_newsletter_campaign_kit_unsubscribe` | Public lien email | Token 64 hex, pas d'email brut dans l'URL | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_unsubscribe` | Authentifie lien email | Token 64 hex, pas d'email brut dans l'URL | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_update_subscriber_status` | Admin | `newsletter_manage_subscribers`, nonce par abonne, whitelist status | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_export_subscribers` | Admin | `newsletter_view_reports`, nonce export | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_create_campaign` | Admin | `newsletter_create_campaigns`, nonce creation | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_update_campaign` | Admin | `newsletter_create_campaigns`, nonce par campagne, brouillon uniquement | Runtime lifecycle valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_duplicate_campaign` | Admin | `newsletter_create_campaigns`, nonce par campagne, copie forcee en brouillon | Runtime lifecycle valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_transition_campaign` | Admin | `newsletter_create_campaigns`, `newsletter_send_campaigns` pour transitions envoi, nonce par campagne | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_schedule_campaign` | Admin | `newsletter_send_campaigns`, nonce par campagne, date future valide | Runtime scheduler valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_create_segment` | Admin | `newsletter_manage_lists`, nonce, champs et IDs bornes | Moteur/runtime valides; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_update_segment` | Admin | `newsletter_manage_lists`, nonce par segment, segment actif | Runtime lifecycle valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_duplicate_segment` | Admin | `newsletter_manage_lists`, nonce par segment, copie active | Runtime lifecycle valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_segment_status` | Admin | `newsletter_manage_lists`, nonce par segment, archivage bloque si campagne non terminale | Runtime lifecycle valide; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_create_topic` | Admin | `newsletter_manage_lists`, nonce, nom/couleur nettoyes | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_update_assignment` | Admin | `newsletter_manage_lists`, nonce, abonne et audience existants | Helper/runtime valides; refus de role a automatiser |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_process_queue` | Admin | `newsletter_send_campaigns`, nonce traitement queue | A tester |
| Newsletter Campaign Kit | `admin_post_newsletter_campaign_kit_save_provider_settings` | Admin | `newsletter_manage_settings`, nonce reglages provider | A tester |

## PhotoVault Core

### `GET /wp-json/photovault/v1/media`

- Exposition: utilisateurs connectes.
- Donnees: liste paginee de medias avec filtres page, folder, category, author, protected, search, orderby.
- Controle actuel: `permission_callback => is_user_logged_in`.
- Validation actuelle: callbacks REST sur entiers positifs, chaine de recherche et filtres controles.
- Protection media: les medias `private` sont retires du resultat si `photovault_user_can_access_media()` refuse l'utilisateur courant.
- Risque residuel: verifier qu'un utilisateur connecte sans grant ne peut pas enumerrer les medias prives par filtres, pages ou recherche.
- Tests a ajouter: anonyme 401/403, user sans grant, user avec grant, owner, media manager, admin, filtre protected, tentative ID guessing indirecte.

### `GET /wp-json/photovault/v1/secure-image`

- Exposition: public par necessite technique, car les balises `img` doivent pouvoir charger une image via URL.
- Controle actuel: `permission_callback => __return_true`, puis controles internes dans le callback.
- Validation actuelle: `id` requis positif, `display` limite aux variantes attendues, `download` filtre binaire.
- Protection private: un media prive renvoie `403` si `photovault_user_can_access_media()` refuse l'utilisateur courant.
- Protection download: `download=1` exige nonce REST, utilisateur connecte, email verifie pour non privilegies, et refuse le download d'un media protege a un non-admin/non-owner.
- Protection preview: les medias proteges non admin/non owner recoivent une preview filigranee cote serveur.
- Audit: refus, previews, previews protegees et downloads sont journalises quand l'audit media est disponible.
- Risque residuel: endpoint volontairement public a documenter dans les tests afin d'eviter une regression vers un simple `__return_true` sans garde-fous.
- Tests a ajouter: image publique preview, media prive anonyme, media prive user sans grant, media prive user avec grant, download nonce invalide, download user non verifie, download protege non owner, download admin.

### `/wp-json/photovault/v1/favorites`

- Exposition: utilisateurs connectes avec authentification cookie WordPress et nonce `wp_rest` cote navigateur.
- Lecture: retourne uniquement les IDs favoris encore accessibles a l'utilisateur courant.
- Mutation: `POST` ajoute et `DELETE` retire un media pour l'utilisateur courant; aucune cible utilisateur n'est acceptee depuis la requete.
- Validation: ID positif, CPT `media_item`, controle `photovault_user_can_access_media()` et unicite DB `(user_id, media_id)`.
- Verification actuelle: migration, idempotence, isolation entre deux comptes, media prive owner, refus anonyme et suppression REST valides par `runtime-user-library.php`.
- Reste: matrice HTTP complete nonce absent/invalide et tentative d'ID guessing sur media prive non owner.

### `admin_post_photovault_update_access_request_status`

- Exposition: admin-post authentifie.
- Controle actuel: capability `photovault_manage_media`.
- CSRF: nonce `photovault_update_access_request_status_{request_id}`.
- Effet: approuve ou rejette une demande d'acces, cree un grant lorsque la demande est approuvee.
- Tests a ajouter: anonyme, utilisateur standard, media manager/admin, nonce invalide, request inexistante, status invalide.

### `admin_post_photovault_secure_existing_originals`

- Exposition: admin-post authentifie.
- Controle actuel: capability `photovault_manage_media`.
- CSRF: nonce `photovault_secure_existing_originals`.
- Effet: traite un lot d'originaux proteges/prives existants vers le stockage prive.
- Tests a ajouter: anonyme, utilisateur standard, media manager/admin, nonce invalide, lot vide, chemin original manquant, idempotence.

### Actions Shootings

- Creation: session obligatoire, nonce, e-mail verifie du compte, date aujourd'hui a deux ans, type whitelist, champs bornes, telephone E.164 optionnel, anti-doublon et cinq demandes par heure.
- Lecture: le dashboard interroge seulement les posts prives appartenant au compte; `photovault_manage_shootings` peut lire toutes les demandes.
- Transitions: `pending` vers `confirmed/cancelled`, `confirmed` vers `completed/cancelled`; les statuts terminaux sont immuables.
- Client: peut uniquement annuler sa propre demande encore active.
- Administration: confirme, annule ou termine depuis l'espace Shootings; chaque transition est auditee et notifiee en HTML/texte.
- Verification actuelle: validation, e-mail lie au compte, isolation de deux clients, transitions, page admin et notifications couvertes par `runtime-shootings.php`.

## Identity Security Kit

### `admin_post_identity_security_kit_save_settings`

- Exposition: admin-post authentifie.
- Controle actuel: capability `identity_manage_settings`.
- CSRF: nonce de sauvegarde des reglages.
- Risque residuel: les politiques exposees doivent rester bornees cote serveur.
- Tests a ajouter: non-admin, nonce invalide, valeurs hors bornes, sauvegarde valide.

### `identity_security_kit_verify_email`

- Exposition: public et authentifie via lien d'email.
- Controle actuel: `uid` entier, `token` sanitize, recherche par hash serveur et statut `pending`.
- Expiration: challenge marque expire si `expires_at` est depasse.
- Justification CSRF: pas de nonce WordPress car le lien email est le facteur de possession; le token doit rester long, non reutilisable et expire.
- Risque residuel: confirmer single-use et absence d'enumeration visible dans les messages.
- Tests a ajouter: token valide, token invalide, token expire, token deja consomme, uid invalide.

### `identity_security_kit_resend_email_verification`

- Exposition: utilisateur connecte.
- Controle actuel: session obligatoire + nonce `identity_security_kit_resend_email_verification`.
- Risque residuel: ajouter ou verifier un rate limiting strict lorsque le module OTP/MFA sera ajoute.
- Tests a ajouter: anonyme, nonce invalide, email deja verifie, renvoi valide, limite de frequence.

### identity_security_kit_email_otp_request / identity_security_kit_email_otp_verify

- Exposition: utilisateur connecte, pour son propre compte.
- CSRF: nonce lie au purpose afin qu'une modification du champ cache invalide la requete.
- Protection: destination HMAC, code hashe, expiration 2-30 minutes, 3-10 essais, cooldown 1-30 minutes, remplacement des anciens challenges et effacement du hash apres fin.
- Anti-rejeu: consommation conditionnelle atomique du challenge pending.
- Tests a ajouter: code correct, incorrect, expire, rejoue, verrouille, mauvais user, mauvais purpose, email modifie, resend trop rapide et nonce invalide.

## Newsletter Campaign Kit

### `newsletter_campaign_kit_subscribe`

- Exposition: public et authentifie via formulaire.
- Controle actuel: nonce `newsletter_campaign_kit_subscribe`, consentement obligatoire, email valide.
- Donnees sensibles: email hash, IP hash, user-agent tronque.
- Risque residuel: ajouter rate limiting et anti-abuse avant production publique.
- Tests a ajouter: nonce absent, consentement absent, email invalide, email existant, double opt-in futur.

### `newsletter_campaign_kit_unsubscribe`

- Exposition: public et authentifie via lien.
- Controle actuel: token hex 64 caracteres, pas d'email dans l'URL.
- Justification CSRF: lien email de desinscription volontaire, action attendue sans login.
- Risque residuel: verifier idempotence et messages neutres pour token inconnu.
- Tests a ajouter: token invalide, token inconnu, token valide, second clic, absence de fuite email.

### `newsletter_campaign_kit_update_subscriber_status`

- Exposition: admin-post authentifie.
- Controle actuel: capability `newsletter_manage_subscribers`.
- CSRF: nonce `newsletter_campaign_kit_update_subscriber_{subscriber_id}`.
- Validation: status limite a `subscribed`, `unsubscribed`, `suppressed`.
- Tests a ajouter: non-admin, nonce invalide, status invalide, subscriber inexistant, transition valide.

### `newsletter_campaign_kit_export_subscribers`

- Exposition: admin-post authentifie.
- Controle actuel: capability `newsletter_view_reports`.
- CSRF: nonce `newsletter_campaign_kit_export_subscribers`.
- Donnees: CSV avec emails, statuts, source et dates.
- Risque residuel: export limite actuellement a 100 entrees; documenter pagination/export complet avant usage production.
- Tests a ajouter: non-admin, nonce invalide, role lecture rapports, headers CSV, absence d'acces public.


### `newsletter_campaign_kit_save_provider_settings`

- Exposition: admin-post authentifie.
- Controle actuel: capability `newsletter_manage_settings`.
- CSRF: nonce `newsletter_campaign_kit_save_provider_settings`.
- Validation: provider whitelist `wp_mail`/`external_filter`, from email valide, from name borne.
- Secrets: aucun token/API key n'est stocke par ce plugin; les providers API doivent etre branches par filtre avec secrets hors Git.
- Tests a ajouter: non-admin, nonce invalide, provider inconnu, email invalide, sauvegarde valide.

Note: les reports sont des pages admin protegees par la capability newsletter_view_reports; ils n ajoutent pas d action state-changing.
ewsletter_view_reports; ils n'ajoutent pas d'action state-changing.
## Fallbacks theme legacy

Le theme conserve des modules legacy lorsque `PHOTOVAULT_CORE_VERSION` n'est pas defini. Dans ce mode, `inc/ajax-filters.php` peut enregistrer les routes historiques.

- Raison actuelle: eviter une rupture immediate si `photovault-core` n'est pas actif.
- Direction: supprimer ces fallbacks apres verification runtime complete des trois plugins actifs.
- Tests a ajouter avant suppression: activation/desactivation plugin, absence de fatal error, routes servies uniquement par le plugin actif.

## Matrice de tests prioritaire

1. Anonyme: aucun acces aux listes media, aucune action admin, preview publique seulement si media public.
2. User non verifie: acces limite aux previews autorisees, aucun download sensible.
3. User verifie sans grant: pas d'acces aux medias prives hors ownership.
4. User verifie avec grant: acces aux collections accordees seulement.
5. Owner: previews et downloads de ses medias selon regles protected.
6. Media manager: acces complet media et actions PhotoVault.
7. Admin: acces complet, export newsletter, reglages identity.
8. Nonces invalides: refus sur toutes les actions state-changing sauf liens email/tokenises publics justifies.
9. ID guessing: impossible d'obtenir un media prive/protege par changement d'ID.
10. Regression HTML: supprimer `required` ou modifier le frontend ne doit pas contourner la validation serveur.
