# Threat model - Media PhotoVault

Derniere mise a jour: 2026-07-09

## Scope

Ce threat model couvre `photovault-core`: medias, collections, previews, downloads, originaux prives, demandes d'acces, grants, audit media et endpoints REST associes.

## Actifs a proteger

- Originaux HD et fichiers 4K.
- Medias prives, collections protegees et grants d'acces.
- Metadonnees media: titre, auteur, collection, statut, protection, compteurs.
- Donnees des demandes d'acces: email, message, IP hash, user-agent.
- Journal d'audit media.
- Capabilities `photovault_manage_media` et `photovault_manage_platform`.

## Acteurs

| Acteur | Objectif probable |
| --- | --- |
| Visiteur anonyme | Voir ou telecharger une image sans compte |
| Utilisateur connecte non verifie | Contourner la verification email ou acceder a une collection |
| Utilisateur verifie sans grant | Deviner des IDs ou recuperer un media prive |
| Client avec grant | Acceder hors du perimetre autorise |
| Owner media | Telecharger ou gerer ses propres medias |
| Media manager/admin | Gerer, approuver, migrer et auditer |
| Attaquant automatise | Enumeration, scraping, brute force, abus endpoint image |

## Frontieres de confiance

- Navigateur public vers REST `/wp-json/photovault/v1/*`.
- Formulaires frontend vers `template_redirect` upload/delete.
- `admin-post.php` vers actions de gestion des demandes et originaux.
- WordPress DB vers stockage fichier public `uploads` et stockage prive `wp-content/photovault-private`.
- Role/capability WordPress vers grants applicatifs par email hash + folder.

## Menaces principales

| Menace | Scenario | Controle actuel | Gap/test requis |
| --- | --- | --- | --- |
| IDOR media prive | Changer `id` dans `secure-image` pour obtenir un media prive | `photovault_user_can_access_media`, owner/admin/grant | Test ID guessing obligatoire |
| Enumeration liste | Filtrer `/media` pour decouvrir des medias prives | REST login + filtrage private | Tester user sans grant, folder/category/search |
| Download protege | Ajouter `download=1` sur un media protege | Nonce REST, login, email verifie, refus non owner/non admin | Tester tous roles et nonce invalide |
| Exposition original | Acces direct a un fichier original par URL | Migration vers stockage prive, docs Nginx | Verifier Apache/Nginx et migrer existants |
| Scraping previews | Aspirer toutes les previews publiques | Variantes card/preview, watermark protege | Ajouter rate limiting/cache des previews |
| CSRF admin | Approuver une demande ou migrer originaux via lien piege | Capabilities + `check_admin_referer` | Tests CSRF admin-post |
| Privilege escalation | Client appelle action admin-post | `photovault_manage_media` | Tests non-admin sur chaque action |
| Watermark bypass | Recuperer preview non filigranee via taille/original | `secure-image` force preview protegee | Tests display variants et fichiers directs |
| Denial of service | Recalcul GD du filigrane sur gros volumes | Limite fichier 20 MB pour GD | Ajouter cache derivatives/rate limits |
| Audit evasion | Evenements sensibles non traces | Audit previews/downloads/refus/grants | Tester presence audit sur refus/download |

## Controles existants

- Listes media authentifiees.
- Previews/downloads via `secure-image`.
- Downloads sensibles avec nonce REST.
- Verification email requise pour non privilegies quand Identity Kit est actif.
- Grants par email hash + collection.
- Stockage prive des originaux proteges/prives apres migration.
- Filigrane serveur plus visible sur medias proteges.
- Audit media avec IP hash et user-agent tronque.
- Admin/media manager conservent acces complet.

## Gaps prioritaires

1. Verifier runtime des tables, migrations et pages admin.
2. Executer la migration des originaux existants.
3. Verifier le blocage serveur de `wp-content/photovault-private/`.
4. Ajouter tests IDOR et authorization matrix.
5. Ajouter cache/derivatives pour previews filigranees.
6. Ajouter rate limiting sur endpoints couteux.
7. Ajouter options admin pour opacite, densite et texte/image du watermark.

## Tests minimum avant production

1. Anonyme ne peut pas lister les medias.
2. Anonyme ne peut pas lire un media prive par ID.
3. User verifie sans grant ne peut pas lire un media prive d'une collection protegee.
4. User avec grant lit uniquement la collection accordee.
5. Nonce download absent/invalide refuse l'original.
6. User non verifie ne telecharge pas un original sensible.
7. Media protege non owner recoit uniquement preview filigranee.
8. Admin/media manager accede a tout et l'action est auditee.
9. Acces direct au stockage prive retourne 403/404 cote serveur.
10. Admin-post demandes/originaux refuse non-admin et nonce invalide.
