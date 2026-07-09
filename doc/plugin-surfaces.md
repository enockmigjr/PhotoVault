# Inventaire tables, options et hooks

Derniere mise a jour: 2026-07-09

Objectif: documenter les surfaces techniques exposees par les plugins PhotoVault afin de faciliter les migrations, tests, audits et futures integrations.

## PhotoVault Core

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}photovault_access_requests` | `photovault_get_access_requests_table()` | Demandes d'acces aux collections protegees | Email, message, IP hash, user-agent |
| `{$wpdb->prefix}photovault_access_grants` | `photovault_get_access_grants_table()` | Grants par email hash, utilisateur et collection | Email hash, statut, dates |
| `{$wpdb->prefix}photovault_media_audit` | `photovault_get_media_audit_table()` | Audit vues, previews, downloads, refus, grants | IP hash, user-agent, contexte nettoye |

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
| `media_folder` | Taxonomie | Collections/dossiers media |
| `media_category` | Taxonomie | Categories media |
| `is_protected` | Post meta | Active preview filigranee et bloque download non privilegie |
| `_photovault_original_url` / `_photovault_private_original_path` | Post meta | Suivi original public/prive apres securisation |
| `_photovault_private_original_secured_at` | Post meta | Horodatage migration stockage prive |

### Filtres publics

| Hook | Type | Usage |
| --- | --- | --- |
| `photovault_max_upload_bytes` | Filter | Modifier la taille maximale upload image |
| `photovault_max_upload_dimension` | Filter | Modifier la dimension maximale image |
| `photovault_max_upload_files` | Filter | Modifier le nombre de fichiers par lot |
| `photovault_private_originals_dir` | Filter | Modifier le dossier de stockage prive des originaux |
| `photovault_protected_preview_cache_dir` | Filter | Modifier le dossier de cache des previews filigranees |

### Actions WordPress utilisees

| Hook | Callback principal | Usage |
| --- | --- | --- |
| `init` | CPT, taxonomies, login redirect | Enregistre types/taxonomies et garde-fous init |
| `rest_api_init` | `photovault_register_rest_routes` | Routes `/media` et `/secure-image` |
| `template_redirect` | Upload/delete/enforce login | Traitements frontend sensibles |
| `admin_menu` | Menus acces/audit/settings | Pages admin PhotoVault |
| `admin_post_photovault_update_access_request_status` | `photovault_handle_access_request_status_update` | Validation admin des demandes d'acces |
| `admin_post_photovault_secure_existing_originals` | `photovault_handle_secure_existing_originals` | Migration par lots des originaux existants |
| `upload_dir` | `photovault_custom_upload_dir` | Dossier upload media personnalise |
| `save_post` / `before_delete_post` | Cache stats | Nettoyage des transients stats |
| `wp_footer` | Protection script | Clic droit/raccourcis pour medias proteges |

## Identity Security Kit

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}identity_security_audit` | `identity_security_kit_get_audit_table()` | Audit login, register, profile, reset, verification | IP hash, user-agent, contexte nettoye |
| `{$wpdb->prefix}identity_security_email_challenges` | `identity_security_kit_get_email_verification_table()` | Challenges verification email | Email hash, token hash, expiration |

### Options

| Option | Usage | Remarque |
| --- | --- | --- |
| `identity_security_kit_settings` | Politiques de securite | Valeurs bornees serveur |
| `identity_security_kit_version` | Version installee/migration | Mise a jour a l'activation/upgrade |

### User meta

| Meta | Source | Usage |
| --- | --- | --- |
| `identity_email_verified` | Filtrable par `identity_security_kit_email_verified_meta_key` | Statut email verifie |
| `identity_email_verification_pending` | Filtrable par `identity_security_kit_email_pending_meta_key` | Statut verification en attente |
| `photovault_avatar_id` | Filtrable par `identity_security_kit_avatar_meta_key` | Avatar utilisateur |

### Filtres publics

| Hook | Type | Usage |
| --- | --- | --- |
| `identity_security_kit_routes` | Filter | Modifier les routes login/register/profile/forgot |
| `identity_security_kit_allowed_image_mimes` | Filter | Modifier les MIME autorises pour avatar |
| `identity_security_kit_max_avatar_size` | Filter | Modifier la taille avatar maximale |
| `identity_security_kit_max_avatar_dimension` | Filter | Modifier la dimension avatar maximale |
| `identity_security_kit_registration_role` | Filter | Modifier le role d'inscription par defaut |
| `identity_security_kit_avatar_meta_key` | Filter | Modifier la meta avatar |
| `identity_security_kit_email_verified_meta_key` | Filter | Modifier la meta email verifie |
| `identity_security_kit_email_pending_meta_key` | Filter | Modifier la meta email pending |

### Actions publiques/emises

| Hook | Type | Usage |
| --- | --- | --- |
| `identity_security_kit_password_reset_failed` | Action emise | Integrations audit/alerte reset echoue |
| `identity_security_kit_password_reset_mail_failed` | Action emise | Integrations audit/alerte email reset echoue |

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

## Newsletter Campaign Kit

### Tables

| Table | Fonction | Usage | Donnees sensibles |
| --- | --- | --- | --- |
| `{$wpdb->prefix}newsletter_campaign_subscribers` | `newsletter_campaign_kit_get_subscribers_table()` | Abonnes, consentement, unsubscribe | Email clair, email hash, token unsubscribe, IP hash |

### Options

| Option | Usage | Remarque |
| --- | --- | --- |
| `newsletter_campaign_kit_version` | Version installee/migration | Mise a jour a l'activation/upgrade |

### Actions WordPress utilisees

| Hook | Callback principal | Usage |
| --- | --- | --- |
| `init` | Upgrade plugin | Schema/version newsletter |
| `admin_menu` | Admin abonnes | UI abonnes et export |
| `admin_post_nopriv_newsletter_campaign_kit_subscribe` | Subscribe | Formulaire public |
| `admin_post_newsletter_campaign_kit_subscribe` | Subscribe | Formulaire authentifie |
| `admin_post_nopriv_newsletter_campaign_kit_unsubscribe` | Unsubscribe | Lien public tokenise |
| `admin_post_newsletter_campaign_kit_unsubscribe` | Unsubscribe | Lien authentifie tokenise |
| `admin_post_newsletter_campaign_kit_update_subscriber_status` | Update status | Changement statut admin |
| `admin_post_newsletter_campaign_kit_export_subscribers` | Export CSV | Export donnees abonnes |

## Points de verification runtime

1. Confirmer que `dbDelta` cree les cinq tables attendues avec les index declares.
2. Confirmer que les options de version sont presentes apres activation/upgrade.
3. Confirmer que les reglages Identity restent bornes meme si le POST envoie des valeurs extremes.
4. Confirmer que `photovault_private_originals_dir` pointe vers un dossier non servi publiquement en production.
5. Confirmer que les hooks publics documentes sont suffisants avant d'ajouter des extensions tierces.
6. Ajouter une page README par plugin qui reprend ces surfaces avec exemples d'integration.
