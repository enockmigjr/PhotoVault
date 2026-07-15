# Progression reelle de l'objectif initial

Derniere mise a jour: 2026-07-15

## Pourquoi l'ancienne estimation etait fausse

L'estimation de 78% utilisait une checklist de 125 lignes trop agregee. Plusieurs lignes marquées terminees couvraient en realite dix a quinze exigences distinctes du cahier initial, et la documentation comptait autant qu'un flux metier valide en runtime.

Le cahier initial contient 113 sections techniques ou fonctionnelles mesurables, hors consignes de mission et de format. Sa Definition of Done impose pour une fonctionnalite: code, validation, autorisation, erreurs, tests, documentation et verification de regression.

La progression est desormais publiee avec deux mesures:

- **implementation fonctionnelle: 70%**;
- **preparation production stricte: 60%**.

Le premier chiffre mesure le code et le cablage reel deja presents. Le second retire les fonctions sans tests d'integration, sans validation WordPress runtime ou sans verification de l'environnement cible. Aucun simple fichier Markdown ne fait progresser la preparation production.

## Matrice par domaine

| Domaine du cahier initial | Sections | Implementation | Production | Etat reel |
| --- | ---: | ---: | ---: | --- |
| Audit, architecture, securite de base | 1-16 | 66% | 50% | Plugins separes, inventaires et matrice media Core presents; fallbacks, admin-post et audit transversal restent incomplets. |
| Identity Security Kit | 17-33 | 72% | 67% | Verification, reset, changement email, OTP, TOTP, recovery, MFA multicanal, retrait, rappels et templates multipart testes; phone library, QR, SMS reel, multisite et E2E navigateur restent a faire. |
| Newsletter Campaign Kit | 34-61 | 78% | 70% | Abonnes, segmentation et lifecycle des segments, campagnes editables/duplicables, programmation, templates multipart, one-click, preferences, suppression durable et Privacy valides; imports, snapshots, tracking et webhooks/provider restent majeurs. |
| PhotoVault metier et experience | 62-76 | 79% | 67% | Home, galerie, medias proteges, favoris, dashboard, reservations, import et autorisations media sont verifies en runtime; preuve multipart post-correction et validation navigateur restent incompletes. |
| Docker et exploitation | 77-89 | 92% | 85% | Runtime, cron, mail, backup et restauration reelle avec rollback sont valides; chiffrement hors site, retention et image immutable restent a faire. |
| Tests fonctionnels et securite | 90-96 | 45% | 40% | La matrice Core couvre roles, grants, ID guessing, pagination, nonces et refus download; Identity, Newsletter, admin-post, CSRF HTTP et E2E restent incomplets. |
| Qualite, migrations, UI, a11y, i18n | 97-105 | 29% | 22% | Migrations versionnees, premieres UI et emails Identity/Newsletter multipart; PHPCS, analyse statique, lifecycle complet, accessibilite et i18n restent incomplets. |
| Threat models et durcissement transversal | 106-111 | 48% | 35% | Trois threat models, rate limits et preuve IDOR media; correlation, alertes, autres plugins et tests anti-abus restent incomplets. |
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
- Les flux email Identity et les notices natives de changement email/mot de passe utilisent un layout professionnel responsive et `AltBody`; les regressions et remises SMTP 250 sont validees.
- Changement d'email differe valide: mot de passe actuel, adresse proposee chiffree, confirmation expirable/single-use, notification ancienne adresse et revocation des preuves email.
- Abonnes newsletter, consentement, desinscription tokenisee, listes, tags, segments dynamiques, campagnes ciblees, queue batch, programmation WP-Cron idempotente et rapports.
- One-click unsubscribe RFC 8058 avec endpoint POST idempotent, en-tetes HTTPS conditionnes a la confirmation DKIM, rotation des jetons et preuve Mailpit.
- Centre public de preferences thematiques protege par token et nonce, GET non mutatif, exclusion d'audience et refus final provider valides.
- Registre `suppressed` durable par HMAC, annulation de queue, levee admin sans reabonnement et blocage apres effacement/re-import valides.
- Export et effacement Privacy WordPress avec conservation documentee du seul HMAC d'une suppression active.
- Templates newsletter reutilisables: creation, edition, duplication, archivage/restauration, heritage campagne et preview admin protegee.
- Emails newsletter multipart HTML/texte valides dans PHPMailer et remis a Mailpit avec reponse SMTP 250.
- Campagnes editables uniquement en brouillon, duplication sans etat de livraison, segments editables/duplicables/archivables, volumes exacts et garde d'archivage valides en runtime.
- Environnement Docker Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI versionne, WordPress initialise et trois plugins actifs.
- Endpoint healthz Nginx sans redirection et expediteur WordPress global valides en runtime avec reponse SMTP 250.
- Matrice media Core validee sur vrais comptes: anonyme, non verifie, verifie, owner, grant par collection, media manager et admin; enumeration d'ID et fuite de pagination privee fermees.
- Snapshot Docker MariaDB/uploads/originaux prives valide par checksums, restauration temporaire de 33 tables puis restauration reelle avec maintenance et rollback prealable.

## Fonctionnalites partielles

- Validation des numeros: E.164 canonique et extension disponible, mais pas encore de librairie de plans de numerotation ni de selecteur pays complet.
- MFA 15 jours: grace, rappels et changements de role/politique valides; cas multisite et modification directe des capabilities d'un role restent a couvrir.
- Changement de facteur: activation, preference et desactivation re-authentifiees; le remplacement passe par retrait/re-enrolement, mais son parcours guide et les changements concurrents restent a valider en navigateur.
- SMS: moteur, provider generique et fail-closed valides avec adapter controle; Twilio reel reste sans credential ni preuve staging.
- Docker: runtime, sauvegarde et restauration locale valides; chiffrement/copie hors site, retention, rotation des secrets et image immutable restent incomplets.
- Newsletter: ciblage, lifecycles campagne/segment, cron, templates multipart, one-click, preferences, suppression durable et Privacy valides; imports, snapshots, providers/webhooks, bounces et observabilite restent incomplets.
- Interfaces publiques: la home est enrichie, mais la verification responsive et clavier n'est pas terminee.
- Import media: progression XHR, statuts, metadonnees, tags et permissions sont implementes et testes en runtime; le test multipart a revele puis fait corriger le controle `test_form`, sans qu'une quatrieme tentative HTTP soit lancee.

## Restant prioritaire

### P0 - Preuves de securite

- Ajouter tests automatises de smoke Docker dans la CI.
- Etendre la matrice automatisee aux routes Identity, Newsletter et actions admin-post privilegiees.
- Ajouter les preuves HTTP de nonce absent/invalide, CSRF et privilege escalation.
- Tester l'acces direct serveur aux originaux et caches prives.

### P1 - Identity

- Valider les parcours navigateur register/verify/login/MFA et les refus wp-admin/AJAX par role.
- Ajouter une librairie reconnue pour les plans de numerotation et l'UX pays/indicatif.
- Ajouter connexion par telephone optionnelle, uniquement pour numero verifie, avec anti-enumeration.
- Ajouter QR Code TOTP accessible.
- Ajouter un parcours guide de remplacement des facteurs et le valider en navigateur.
- Couvrir le multisite et les modifications directes des capabilities d'un role.
- Completer les tests OTP restants: mauvais utilisateur, resend concurrent, changement de destination et rate-limit distribue.

### P1 - Newsletter

- Ajouter historique/snapshot d'audience pour expliquer chaque ciblage apres envoi.
- Ajouter supervision du cron, cle d'idempotence provider et configuration des tailles de lot.
- Ajouter une bibliotheque de blocs editoriaux au-dela des templates complets.
- Ajouter bounces, complaints, webhooks signes et providers API.
- Ajouter tracking configurable ouvertures/clics, statistiques et exports avances.
- Ajouter imports CSV avec dry-run, mapping, validation et rapport d'erreurs.

### P1 - PhotoVault

- Rejouer la preuve multipart HTTP de l'upload corrige et valider l'espace d'import en navigateur.
- Valider le dashboard et le formulaire de reservation en responsive navigateur.
- Regenerer les thumbnails existants et securiser les originaux historiques en runtime.
- Terminer mobile, clavier, focus, overlay et fermeture ESC.

### P2 - Exploitation et qualite

- Externaliser et chiffrer les snapshots avec retention et alertes adaptees a l'hebergeur final.
- Ajouter PHPCS WordPress Coding Standards et analyse statique adaptee.
- Ajouter tests d'integration WordPress et parcours E2E critiques.
- Verifier visuellement les templates email dans plusieurs clients et completer les notifications internes du theme.
- Completer i18n, compatibilite PHP/WordPress et politique multisite.

## Regle pour les prochaines mises a jour

Une fonctionnalite ne passe a 100% que si son flux principal, ses refus serveur, ses erreurs, ses tests et sa documentation sont valides. Une implementation non testee reste partielle. Une documentation seule reste a 0% d'implementation.
