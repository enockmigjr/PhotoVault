# Inventaire tables, options et hooks

Derniere mise a jour: 2026-07-15

Objectif: documenter les surfaces techniques exposees par les plugins PhotoVault afin de faciliter les migrations, tests, audits et futures integrations.

## PhotoVault Core

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}photovault_access_requests` | `photovault_get_access_requests_table()` | Demandes d'acces aux collections protegees | Email, message, IP hash, user-agent |
| `{$wpdb->prefix}photovault_access_grants` | `photovault_get_access_grants_table()` | Grants par email hash, utilisateur et collection | Email hash, statut, dates |
| `{$wpdb->prefix}photovault_media_audit` | `photovault_get_media_audit_table()` | Audit vues, previews, downloads, refus, grants | IP hash, user-agent, contexte nettoye |
| `{$wpdb->prefix}photovault_favorites` | `photovault_get_favorites_table()` | Bibliotheque personnelle, unique par utilisateur/media | IDs utilisateur/media, date |

### Options

| Option | Usage | Remarque |
| --- | --- | --- |
| `photovault_core_version` | Version installee/migration | Mise a jour a l'activation et pendant les upgrades |
| `photovault_watermark_text` | Texte du filigrane serveur | Defaut `PHOTOVAULT`, longueur bornee a 48 caracteres |
| `photovault_watermark_opacity` | Opacite du filigrane | Valeur bornee 10-100 |
| `photovault_watermark_spacing` | Densite/espacement du filigrane | Valeur bornee 35-180 px |
| `photovault_watermark_quality` | Qualite JPEG du cache filigrane | Valeur bornee 60-95 |
| `photovault_watermark_image_id` | Image de filigrane serveur | Attachment JPEG, PNG ou WebP valide, fallback texte si absent |

### Post types, taxonomies et meta

| Element | Type | Usage |
| --- | --- | --- |
| `media_item` | CPT | Oeuvres/media PhotoVault |
| `photovault_shooting` | CPT prive | Reservations possedees par un compte, non publiques et absentes de REST |
| `media_folder` | Taxonomie | Collections/dossiers media |
| `media_category` | Taxonomie | Categories media |
| `media_tag` | Taxonomie | Tags libres bornes utilises par l'edition rapide apres import |
| `is_protected` | Post meta | Active preview filigranee et bloque download non privilegie |
| `_photovault_original_url` / `_photovault_private_original_path` | Post meta | Suivi original public/prive apres securisation |
| `_photovault_private_original_secured_at` | Post meta | Horodatage migration stockage prive |
| `_photovault_shooting_*` | Post meta | Type, date, lieu, message, contact, statut et date de transition d'une reservation |

### Filtres publics

| Hook | Type | Usage |
| --- | --- | --- |
| `photovault_max_upload_bytes` | Filter | Modifier la taille maximale upload image |
| `photovault_max_upload_dimension` | Filter | Modifier la dimension maximale image |
| `photovault_max_upload_files` | Filter | Modifier le nombre de fichiers par lot |
| `photovault_private_originals_dir` | Filter | Modifier le dossier de stockage prive des originaux |
| `photovault_protected_preview_cache_dir` | Filter | Modifier le dossier de cache des previews filigranees |
| `photovault_shooting_types` | Filter | Etendre les types de shootings proposes sans modifier le lifecycle |


### Actions WordPress utilisees

| Hook | Callback principal | Usage |
| --- | --- | --- |
| `init` | CPT, taxonomies, login redirect | Enregistre types/taxonomies et garde-fous init |
| `rest_api_init` | `photovault_register_rest_routes`, `photovault_register_user_library_routes`, `photovault_register_media_management_routes` | Routes de consultation, import, edition, image securisee et favoris |
| `template_redirect` | Upload/delete/enforce login | Traitements frontend sensibles |
| `admin_menu` | Menus acces/audit/settings/shootings/import | Pages admin PhotoVault et espace d'import progressif |
| `admin_post_photovault_update_access_request_status` | `photovault_handle_access_request_status_update` | Validation admin des demandes d'acces |
| `admin_post_photovault_secure_existing_originals` | `photovault_handle_secure_existing_originals` | Migration par lots des originaux existants |
| `admin_post_photovault_create_shooting` | `photovault_handle_create_shooting` | Creation owner-only avec nonce, identite, validation et rate limit |
| `admin_post_photovault_shooting_transition` | `photovault_handle_shooting_transition` | Annulation owner ou transitions reservees a `photovault_manage_shootings` |
| `upload_dir` | `photovault_custom_upload_dir` | Dossier upload media personnalise |
| `save_post` / `before_delete_post` | Cache stats | Nettoyage des transients stats |
| `wp_footer` | Protection script | Clic droit/raccourcis pour medias proteges |

## Identity Security Kit

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}identity_security_audit` | `identity_security_kit_get_audit_table()` | Audit login, register, profile, reset, verification | IP hash, user-agent, contexte nettoye |
| `{$wpdb->prefix}identity_security_email_challenges` | `identity_security_kit_get_email_verification_table()` | Challenges verification email | Email hash, token hash, expiration |
| `{$wpdb->prefix}identity_security_email_otp` | `identity_security_kit_get_email_otp_table()` | Table legacy des challenges email avant schema 0.4 | Destination HMAC, code hash, essais, expiration |
| `{$wpdb->prefix}identity_security_otp_challenges` | `identity_security_kit_get_otp_table()` | OTP generiques email/SMS par user, purpose et canal | Destination HMAC, code hash, essais, correlation et idempotence |

### Options

| Option | Usage | Remarque |
| --- | --- | --- |
| `identity_security_kit_settings` | Politiques de securite | Mot de passe, avatar, email/OTP, telephone E.164, rate limiting et politique MFA par capabilities |
| `identity_security_kit_version` | Version installee/migration | Mise a jour a l'activation/upgrade |

### User meta

| Meta | Source | Usage |
| --- | --- | --- |
| `identity_email_verified` | Filtrable par `identity_security_kit_email_verified_meta_key` | Statut email verifie |
| `identity_email_verification_pending` | Filtrable par `identity_security_kit_email_pending_meta_key` | Statut verification en attente |
| `photovault_avatar_id` | Filtrable par `identity_security_kit_avatar_meta_key` | Avatar utilisateur |
| identity_email_otp_verified_at | Identity Kit | Derniere verification OTP email reussie |
| `identity_phone_e164` / `identity_phone_verified` / `identity_phone_verified_hash` | Identity Kit | Telephone canonique, preuve liee au numero courant et date de verification |
| `identity_mfa_email_enabled` / `identity_mfa_sms_enabled` | Identity Kit | Enrollment explicite des facteurs distants |
| `identity_mfa_preferred_method` | Identity Kit | Methode preferee parmi les facteurs encore utilisables |
| identity_mfa_totp_secret / identity_mfa_totp_pending | Identity Kit | Secret TOTP chiffre authentifie; pending expire apres 15 minutes |
| identity_mfa_totp_last_counter / identity_mfa_enabled_at | Identity Kit | Anti-rejeu et date activation |
| identity_mfa_recovery_codes | Identity Kit | Recovery codes hashes et consommes individuellement |
| identity_mfa_grace_started_at | Identity Kit | Debut stable de la grace MFA |
| identity_mfa_login_challenge | Identity Kit | Hash du challenge login temporaire actif |

### Filtres publics

| Hook | Type | Usage |
| --- | --- | --- |
| `identity_security_kit_routes` | Filter | Modifier les routes login/register/profile/forgot |
| `identity_security_kit_rate_limit()` | Helper | Transient rate limit par empreinte HMAC IP/user-agent ou user ID |
| `identity_security_kit_allowed_image_mimes` | Filter | Modifier les MIME autorises pour avatar |
| `identity_security_kit_max_avatar_size` | Filter | Modifier la taille avatar maximale |
| `identity_security_kit_max_avatar_dimension` | Filter | Modifier la dimension avatar maximale |
| `identity_security_kit_registration_role` | Filter | Modifier le role d'inscription par defaut |
| `identity_security_kit_avatar_meta_key` | Filter | Modifier la meta avatar |
| `identity_security_kit_email_verified_meta_key` | Filter | Modifier la meta email verifie |
| identity_security_kit_email_pending_meta_key | Filter | Modifier la meta email pending |
| `identity_security_kit_phone_meta_key` | Filter | Modifier la meta du telephone E.164 |
| `identity_security_kit_normalized_phone` | Filter | Brancher une librairie de plans de numerotation reconnue |
| `identity_security_kit_sms_provider` | Filter | Selectionner un provider SMS |
| `identity_security_kit_sms_provider_available` | Filter | Declarer un adapter custom disponible |
| `identity_security_kit_sms_delivery` | Filter | Adapter provider; retourne `true` ou `WP_Error` |
| `identity_security_kit_allowed_mfa_methods` | Filter | Borner les facteurs autorises par utilisateur/politique |
| `identity_security_kit_user_mfa_methods` | Filter | Etendre les facteurs effectivement enrolles |
| identity_security_kit_mfa_required_capabilities | Filter | Etendre les capabilities qui imposent MFA |
| identity_security_kit_user_requires_mfa | Filter | Adapter la politique par utilisateur |
| identity_security_kit_user_has_mfa | Filter | Declarer une methode MFA fournie par une integration |

### Actions publiques/emises

| Hook | Type | Usage |
| --- | --- | --- |
| `identity_security_kit_password_reset_failed` | Action emise | Integrations audit/alerte reset echoue |
| `identity_security_kit_password_reset_mail_failed` | Action emise | Integrations audit/alerte email reset echoue |
| `identity_security_kit_email_otp_created` | Action emise | Challenge cree sans exposer le code |
| `identity_security_kit_otp_created` | Action emise | Challenge generique cree sans exposer le code |
| `identity_security_kit_otp_verified` | Action emise | Challenge generique consomme avec purpose et canal |
| `identity_security_kit_email_otp_verified` | Action emise | OTP email consomme pour un user et un purpose |
| `identity_security_kit_phone_verified` | Action emise | Telephone courant confirme par SMS |
| identity_security_kit_mfa_enabled | Action emise | Methode MFA activee sans exposer le secret |
| identity_security_kit_mfa_disabled | Action emise | Methode MFA desactivee |


### Actions WordPress utilisees

| Hook | Callback principal | Usage |
| --- | --- | --- |
| `admin_init` | Upgrade plugin | Schema et version |
| `template_redirect` | Login, forgot, register, profile | Formulaires frontend |
| `admin_menu` | Admin Identity Kit | Reglages et audit |
| `admin_post_identity_security_kit_save_settings` | Save settings | Reglages securite |
| `admin_post_nopriv_identity_security_kit_verify_email` | Verify email | Lien public email |
| `admin_post_identity_security_kit_verify_email` | Verify email | Lien email authentifie |
| `admin_post_identity_security_kit_resend_email_verification` | Resend verification | Renvoi authentifie |
| `admin_post_identity_security_kit_email_otp_request` | Request OTP | Session, nonce lie au purpose, cooldown |
| dmin_post_identity_security_kit_email_otp_verify | Verify OTP | Session, nonce lie au purpose, ownership, expiration et essais |
| dmin_post_identity_security_kit_totp_start | Start TOTP | Session, nonce, mot de passe courant et secret pending chiffre |
| dmin_post_identity_security_kit_totp_confirm | Confirm TOTP | Session, nonce, rate limit, fenetre temporelle et anti-rejeu |
| dmin_post_identity_security_kit_totp_cancel | Cancel TOTP | Session et nonce |
| dmin_post_identity_security_kit_recovery_regenerate | Replace recovery | Session, nonce et facteur courant |
| dmin_post_identity_security_kit_totp_disable | Disable TOTP | Session, nonce, mot de passe et facteur courant |
| `admin_post_identity_security_kit_phone_otp_request` / `verify` | Verification telephone | Session, nonce, destination courante, cooldown et essais |
| `admin_post_identity_security_kit_channel_mfa_start` / `confirm` | Enrollment email/SMS | Mot de passe courant, destination verifiee et OTP purpose-bound |
| `admin_post_identity_security_kit_mfa_preference` | Preference MFA | Mot de passe courant et methode deja active |
| `wp_authenticate_user` / `login_form_identity_security_mfa` | Challenge login | Challenge chiffre lie au navigateur, choix de facteur et consommation unique |

## Newsletter Campaign Kit

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}newsletter_campaign_subscribers` | `newsletter_campaign_kit_get_subscribers_table()` | Abonnes, consentement, unsubscribe | Email clair, email hash, token unsubscribe, IP hash |
| `{$wpdb->prefix}newsletter_campaign_lists` | `newsletter_campaign_kit_get_lists_table()` | Listes editoriales | Nom, slug, description, statut |
| `{$wpdb->prefix}newsletter_campaign_tags` | `newsletter_campaign_kit_get_tags_table()` | Tags de segmentation | Nom, slug, couleur |
| `{$wpdb->prefix}newsletter_campaign_subscriber_lists` | `newsletter_campaign_kit_get_subscriber_lists_table()` | Liaison abonne/liste | Cle composee abonne + liste |
| `{$wpdb->prefix}newsletter_campaign_subscriber_tags` | `newsletter_campaign_kit_get_subscriber_tags_table()` | Liaison abonne/tag | Cle composee abonne + tag |
| `{$wpdb->prefix}newsletter_campaign_segments` | `newsletter_campaign_kit_get_segments_table()` | Audiences dynamiques all/any | Regles JSON normalisees, statut |
| `{$wpdb->prefix}newsletter_campaign_topics` | `newsletter_campaign_kit_get_topics_table()` | Thematiques de campagne | Nom, slug, couleur, statut |
| `{$wpdb->prefix}newsletter_campaign_audit` | `newsletter_campaign_kit_get_audit_table()` | Audit inscriptions, desinscriptions, statuts, exports et segments | IP hash, user-agent tronque, contexte nettoye |
| `{$wpdb->prefix}newsletter_campaign_campaigns` | `newsletter_campaign_kit_get_campaigns_table()` | Brouillons, ciblage, statuts et transitions campagne | Sujet, contenu, cible liste, auteurs |
| `{$wpdb->prefix}newsletter_campaign_queue` | `newsletter_campaign_kit_get_queue_table()` | File de livraison batch, tentatives et backoff | IDs campagne/abonne, statut, erreurs provider |

### Options

| Option | Usage | Remarque |
| --- | --- | --- |
| `newsletter_campaign_kit_version` | Version installee/migration | Mise a jour a l'activation/upgrade |
| `newsletter_campaign_kit_provider_settings` | Provider livraison | `wp_mail` ou adaptateur externe, from name/from email, sans secret |

### Filtres publics

| Hook | Type | Usage |
| --- | --- | --- |
| `newsletter_campaign_kit_send_email` | Filter | Brancher/remplacer le provider de livraison; retourne `true` en succes ou `WP_Error` en echec |

### Actions WordPress utilisees

| Hook | Callback principal | Usage |
| --- | --- | --- |
| `init` | Upgrade et scheduler | Schema/version newsletter et enregistrement unique du cron minute |
| `admin_menu` | Admin newsletter | UI abonnes, segments, campagnes, queue, settings, reports, audit et export |
| `admin_post_nopriv_newsletter_campaign_kit_subscribe` | Subscribe | Formulaire public |
| `admin_post_newsletter_campaign_kit_subscribe` | Subscribe | Formulaire authentifie |
| `admin_post_nopriv_newsletter_campaign_kit_unsubscribe` | Unsubscribe | Lien public tokenise |
| `admin_post_newsletter_campaign_kit_unsubscribe` | Unsubscribe | Lien authentifie tokenise |
| `admin_post_newsletter_campaign_kit_update_subscriber_status` | Update status | Changement statut admin |
| `admin_post_newsletter_campaign_kit_export_subscribers` | Export CSV | Export donnees abonnes |
| `admin_post_newsletter_campaign_kit_create_list` | Creation liste | Capability `newsletter_manage_lists`, nonce |
| `admin_post_newsletter_campaign_kit_create_tag` | Creation tag | Capability `newsletter_manage_lists`, nonce |
| `admin_post_newsletter_campaign_kit_create_segment` | Creation segment | Capability `newsletter_manage_lists`, nonce, regles validees |
| `admin_post_newsletter_campaign_kit_update_segment` | Edition segment | Capability `newsletter_manage_lists`, nonce, segment actif et regles validees |
| `admin_post_newsletter_campaign_kit_duplicate_segment` | Duplication segment | Capability `newsletter_manage_lists`, nonce, nouvelle copie active |
| `admin_post_newsletter_campaign_kit_segment_status` | Archivage/restauration segment | Capability `newsletter_manage_lists`, nonce, verrou si campagne non terminale |
| `admin_post_newsletter_campaign_kit_create_topic` | Creation thematique | Capability `newsletter_manage_lists`, nonce |
| `admin_post_newsletter_campaign_kit_update_assignment` | Affectation audience | Capability `newsletter_manage_lists`, nonce, IDs controles |
| `admin_post_newsletter_campaign_kit_create_campaign` | Creation campagne | Capability `newsletter_create_campaigns`, nonce |
| `admin_post_newsletter_campaign_kit_update_campaign` | Edition campagne | Capability `newsletter_create_campaigns`, nonce, brouillon uniquement |
| `admin_post_newsletter_campaign_kit_duplicate_campaign` | Duplication campagne | Capability `newsletter_create_campaigns`, nonce, nouvelle copie sans etat de livraison |
| `admin_post_newsletter_campaign_kit_transition_campaign` | Transition campagne | Capability `newsletter_create_campaigns`, `newsletter_send_campaigns` pour envoi, nonce |
| `admin_post_newsletter_campaign_kit_schedule_campaign` | Programmer campagne | Capability `newsletter_send_campaigns`, nonce par campagne, date future convertie en UTC |
| `admin_post_newsletter_campaign_kit_process_queue` | Traitement queue | Capability `newsletter_send_campaigns`, nonce |
| `admin_post_newsletter_campaign_kit_save_provider_settings` | Reglages provider | Capability `newsletter_manage_settings`, nonce |
| `newsletter_campaign_kit_run_scheduled` | Scheduler campagne | Reprise des verrous expires, campagnes echues, batch borne et finalisation |

## Points de verification runtime

1. Confirmer que `dbDelta` cree les tables attendues avec les index declares.
2. Confirmer que les options de version sont presentes apres activation/upgrade.
3. Confirmer que les reglages Identity restent bornes meme si le POST envoie des valeurs extremes.
4. Confirmer que `photovault_private_originals_dir` pointe vers un dossier non servi publiquement en production.
5. Confirmer que les hooks publics documentes sont suffisants avant d'ajouter des extensions tierces.
6. Ajouter une page README par plugin qui reprend ces surfaces avec exemples d'integration.
