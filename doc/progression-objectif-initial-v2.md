# Progression reelle de l'objectif initial

Derniere mise a jour: 2026-07-13

## Pourquoi l'ancienne estimation etait fausse

L'estimation de 78% utilisait une checklist de 125 lignes trop agregee. Plusieurs lignes marquées terminees couvraient en realite dix a quinze exigences distinctes du cahier initial, et la documentation comptait autant qu'un flux metier valide en runtime.

Le cahier initial contient 113 sections techniques ou fonctionnelles mesurables, hors consignes de mission et de format. Sa Definition of Done impose pour une fonctionnalite: code, validation, autorisation, erreurs, tests, documentation et verification de regression.

La progression est desormais publiee avec deux mesures:

- **implementation fonctionnelle: 60%**;
- **preparation production stricte: 49%**.

Le premier chiffre mesure le code et le cablage reel deja presents. Le second retire les fonctions sans tests d'integration, sans validation WordPress runtime ou sans verification de l'environnement cible. Aucun simple fichier Markdown ne fait progresser la preparation production.

## Matrice par domaine

| Domaine du cahier initial | Sections | Implementation | Production | Etat reel |
| --- | ---: | ---: | ---: | --- |
| Audit, architecture, securite de base | 1-16 | 65% | 48% | Plugins separes, inventaires et controles principaux presents; fallbacks, audit exhaustif et tests restent incomplets. |
| Identity Security Kit | 17-33 | 69% | 63% | Email initial et changement confirme, E.164, OTP, TOTP, recovery, MFA multicanal, retrait securise, rappels et changements de politique testes; phone library, QR, SMS reel, multisite et E2E navigateur restent a faire. |
| Newsletter Campaign Kit | 34-61 | 65% | 57% | Abonnes, segmentation, campagnes, programmation, one-click, preferences thematiques, suppression durable et Privacy valides; templates, imports, tracking, webhooks/provider et lifecycle des segments restent majeurs. |
| PhotoVault metier et experience | 62-76 | 57% | 43% | Home, galerie, detail, medias proteges, watermark et downloads avances; dashboard, favoris, shootings, upload complet et tests d'autorisation manquent. |
| Docker et exploitation | 77-89 | 82% | 70% | WordPress initialise, services healthy, plugins/migrations valides, cron reel et expediteur wp_mail vers Mailpit testes; sauvegardes, restauration et image de production restent a faire. |
| Tests fonctionnels et securite | 90-96 | 27% | 24% | OTP, SMS adapter, Identity runtime et changement email, scheduler, segmentation et suppression Newsletter testes; matrices REST, CSRF, IDOR et E2E navigateur restent absentes. |
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
- Desactivation TOTP/email/SMS re-authentifiee, OTP lie au canal, invalidation des autres sessions et controle reitere du dernier facteur obligatoire.
- Grace MFA completee par rappels J+1/J+7/J+12, cron horaire borne, rattrapage sans rafale et reconciliation des changements de role/politique.
- Runtime Identity valide sur vrais utilisateurs, tables et metadonnees: email lie et single-use, E.164 unique, OTP purpose/expiry/attempts/replay, TOTP, recovery, login email/SMS/TOTP et grace 15 jours.
- Douze emails Identity remis a Mailpit avec reponse SMTP 250; le template fatal de verification/reset a ete corrige.
- Changement d'email differe valide: mot de passe actuel, adresse proposee chiffree, confirmation expirable/single-use, notification ancienne adresse et revocation des preuves email.
- Abonnes newsletter, consentement, desinscription tokenisee, listes, tags, segments dynamiques, campagnes ciblees, queue batch, programmation WP-Cron idempotente et rapports.
- One-click unsubscribe RFC 8058 avec endpoint POST idempotent, en-tetes HTTPS conditionnes a la confirmation DKIM, rotation des jetons et preuve Mailpit.
- Centre public de preferences thematiques protege par token et nonce, GET non mutatif, exclusion d'audience et refus final provider valides.
- Registre `suppressed` durable par HMAC, annulation de queue, levee admin sans reabonnement et blocage apres effacement/re-import valides.
- Export et effacement Privacy WordPress avec conservation documentee du seul HMAC d'une suppression active.
- Environnement Docker Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI versionne, WordPress initialise et trois plugins actifs.
- Endpoint healthz Nginx sans redirection et expediteur WordPress global valides en runtime avec reponse SMTP 250.

## Fonctionnalites partielles

- Validation des numeros: E.164 canonique et extension disponible, mais pas encore de librairie de plans de numerotation ni de selecteur pays complet.
- MFA 15 jours: grace, rappels et changements de role/politique valides; cas multisite et modification directe des capabilities d'un role restent a couvrir.
- Changement de facteur: activation, preference et desactivation re-authentifiees; le remplacement passe par retrait/re-enrolement, mais son parcours guide et les changements concurrents restent a valider en navigateur.
- SMS: moteur, provider generique et fail-closed valides avec adapter controle; Twilio reel reste sans credential ni preuve staging.
- Docker: runtime local valide et services healthy; sauvegardes, restauration, rotation des secrets et image immutable de production restent incomplets.
- Newsletter: ciblage, cron, one-click, preferences, suppression durable et Privacy valides; templates, imports, providers/webhooks, bounces et observabilite restent incomplets.
- Interfaces publiques: la home est enrichie, mais la verification responsive et clavier n'est pas terminee.

## Restant prioritaire

### P0 - Preuves de securite

- Ajouter tests automatises de smoke Docker dans la CI.
- Tester sauvegarde/restauration de MariaDB, uploads et stockage prive.
- Ajouter la matrice automatisee REST/AJAX, CSRF, IDOR et privilege escalation.
- Tester anonymous, user non verifie, user verifie, owner, manager et admin.
- Tester les medias prives par ID guessing et acces direct serveur.

### P1 - Identity

- Valider les parcours navigateur register/verify/login/MFA et les refus wp-admin/AJAX par role.
- Ajouter une librairie reconnue pour les plans de numerotation et l'UX pays/indicatif.
- Ajouter connexion par telephone optionnelle, uniquement pour numero verifie, avec anti-enumeration.
- Ajouter QR Code TOTP accessible.
- Ajouter un parcours guide de remplacement des facteurs et le valider en navigateur.
- Couvrir le multisite et les modifications directes des capabilities d'un role.
- Completer les tests OTP restants: mauvais utilisateur, resend concurrent, changement de destination et rate-limit distribue.

### P1 - Newsletter

- Ajouter edition, duplication, archivage et estimation du volume des segments.
- Ajouter historique/snapshot d'audience pour expliquer chaque ciblage apres envoi.
- Ajouter supervision du cron, cle d'idempotence provider et configuration des tailles de lot.
- Ajouter templates reutilisables, preview HTML/texte et emails multipart.
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
