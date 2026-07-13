# Progression reelle de l'objectif initial

Derniere mise a jour: 2026-07-13

## Pourquoi l'ancienne estimation etait fausse

L'estimation de 78% utilisait une checklist de 125 lignes trop agregee. Plusieurs lignes marquées terminees couvraient en realite dix a quinze exigences distinctes du cahier initial, et la documentation comptait autant qu'un flux metier valide en runtime.

Le cahier initial contient 113 sections techniques ou fonctionnelles mesurables, hors consignes de mission et de format. Sa Definition of Done impose pour une fonctionnalite: code, validation, autorisation, erreurs, tests, documentation et verification de regression.

La progression est desormais publiee avec deux mesures:

- **implementation fonctionnelle: 44%**;
- **preparation production stricte: 32%**.

Le premier chiffre mesure le code et le cablage reel deja presents. Le second retire les fonctions sans tests d'integration, sans validation WordPress runtime ou sans verification de l'environnement cible. Aucun simple fichier Markdown ne fait progresser la preparation production.

## Matrice par domaine

| Domaine du cahier initial | Sections | Implementation | Production | Etat reel |
| --- | ---: | ---: | ---: | --- |
| Audit, architecture, securite de base | 1-16 | 65% | 48% | Plugins separes, inventaires et controles principaux presents; fallbacks, audit exhaustif et tests restent incomplets. |
| Identity Security Kit | 17-33 | 50% | 34% | Email verification, OTP generique, TOTP, recovery, grace MFA et base multicanal presents; email change, phone library, QR, changement de facteurs et runtime complet restent a faire. |
| Newsletter Campaign Kit | 34-61 | 29% | 18% | Abonnes, consentement, listes, tags, campagnes simples, queue et reporting de livraison presents; segmentation, programmation, cron, templates, tracking, bounces et privacy restent majeurs. |
| PhotoVault metier et experience | 62-76 | 57% | 43% | Home, galerie, detail, medias proteges, watermark et downloads avances; dashboard, favoris, shootings, upload complet et tests d'autorisation manquent. |
| Docker et exploitation | 77-89 | 58% | 40% | Compose, Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI presents; installation WordPress Docker et procedures d'exploitation restent a valider. |
| Tests fonctionnels et securite | 90-96 | 9% | 7% | Quelques tests unitaires cibles; matrices REST, CSRF, IDOR, OTP, MFA, newsletter et E2E absentes. |
| Qualite, migrations, UI, a11y, i18n | 97-105 | 25% | 16% | Migrations versionnees et premieres UI; PHPCS, analyse statique, lifecycle complet, accessibilite, i18n et templates email uniformes incomplets. |
| Threat models et durcissement transversal | 106-111 | 46% | 33% | Trois threat models et plusieurs rate limits; correlation, alertes, changements de facteur et tests anti-abus incomplets. |
| Compatibilite et documentation | 112-113 | 55% | 43% | Documentation centrale riche; compatibilite, multisite, hooks et guides providers encore incomplets. |

## Fonctionnalites validees ou solidement implementees

- Separation du theme, de PhotoVault Core, Identity Kit et Newsletter Kit avec depots independants.
- Pages d'ensemble servies avec variantes adaptees plutot que les originaux 4K.
- Previews protegees filigranees cote serveur, cachees et configurables.
- Downloads HD controles, grants de collections et acces administrateur complet.
- Verification email, renvoi borne et reponse reset anti-enumeration.
- TOTP RFC 6238, secret chiffre, anti-rejeu et recovery codes hashes.
- Base OTP commune email/SMS avec purpose, canal, destination HMAC, expiration, tentatives et consommation atomique.
- Abstraction SMS avec adaptateur Twilio sans secret en base ou dans Git et extension par filtres.
- Enrollment MFA email/SMS explicite, destination verifiee, preference et challenge login multicanal.
- Abonnes newsletter, consentement, desinscription tokenisee, listes, tags, campagnes simples, queue batch et rapports de livraison.
- Environnement Docker Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI versionne.

## Fonctionnalites partielles

- Validation des numeros: E.164 canonique et extension disponible, mais pas encore de librairie de plans de numerotation ni de selecteur pays complet.
- MFA 15 jours: grace et blocage admin presents, mais rappels, changements de politique et cas multisite non couverts.
- Changement de facteur: activation et preference re-authentifiees; desactivation email/SMS et remplacement avec verification du facteur courant restent a faire.
- SMS: Twilio et adapter hook presents; aucune credentielle reelle ni verification de livraison runtime.
- Docker: services sains lors du dernier controle, mais WordPress Docker n'est pas initialise avec les plugins et les migrations.
- Newsletter: queue et retry de base presents, mais programmation et cron applicatif non valides.
- Interfaces publiques: la home est enrichie, mais la verification responsive et clavier n'est pas terminee.

## Restant prioritaire

### P0 - Preuves de securite

- Initialiser WordPress dans Docker et activer les trois plugins.
- Executer les migrations `dbDelta` et verifier les index/tables en base.
- Ajouter la matrice automatisee REST/AJAX, CSRF, IDOR et privilege escalation.
- Tester anonymous, user non verifie, user verifie, owner, manager et admin.
- Tester les medias prives par ID guessing et acces direct serveur.

### P1 - Identity

- Valider en runtime verification email, telephone, TOTP, email MFA, SMS MFA, recovery et grace 15 jours.
- Ajouter une librairie reconnue pour les plans de numerotation et l'UX pays/indicatif.
- Ajouter changement d'email securise avec confirmation du nouvel email et notification de l'ancien.
- Ajouter connexion par telephone optionnelle, uniquement pour numero verifie, avec anti-enumeration.
- Ajouter QR Code TOTP accessible.
- Ajouter desactivation/remplacement email et SMS avec mot de passe et facteur existant.
- Ajouter rappels J+1/J+7/J+12 et gestion des changements de capabilities/politique.
- Completer les tests OTP: expiration, purpose, mauvais utilisateur, resend, replay, lockout.

### P1 - Newsletter

- Implementer segments dynamiques et moteur de conditions testable.
- Ajouter thematiques/categories de campagne et ciblage combine listes/tags/segments.
- Ajouter programmation timezone-aware, cron idempotent et verrouillage anti-double envoi.
- Ajouter templates reutilisables, preview HTML/texte et emails multipart.
- Ajouter one-click unsubscribe standard, suppression/suppression-list et privacy export/erase.
- Ajouter bounces, complaints, webhooks signes et providers API.
- Ajouter tracking configurable ouvertures/clics, statistiques et exports avances.
- Ajouter imports CSV avec dry-run, mapping, validation et rapport d'erreurs.

### P1 - PhotoVault

- Terminer l'upload avec progression, statuts et edition des metadonnees.
- Implementer dashboard utilisateur, favoris, preferences, downloads et acces.
- Implementer le module Shootings et ses transitions serveur.
- Regenerer les thumbnails existants et securiser les originaux historiques en runtime.
- Terminer mobile, clavier, focus, overlay et fermeture ESC.

### P2 - Exploitation et qualite

- Ajouter sauvegarde/restauration DB, uploads et stockage prive.
- Ajouter PHPCS WordPress Coding Standards et analyse statique adaptee.
- Ajouter tests d'integration WordPress et parcours E2E critiques.
- Uniformiser les templates email transactionnels HTML/texte.
- Completer i18n, compatibilite PHP/WordPress et politique multisite.

## Regle pour les prochaines mises a jour

Une fonctionnalite ne passe a 100% que si son flux principal, ses refus serveur, ses erreurs, ses tests et sa documentation sont valides. Une implementation non testee reste partielle. Une documentation seule reste a 0% d'implementation.
