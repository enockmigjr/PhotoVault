# Tasks - objectif initial PhotoVault

Derniere mise a jour: 2026-07-15

Legende:

- [x] Valide / implemente et committe
- [~] Partiel / implemente mais a verifier en runtime ou a completer
- [ ] Restant

## 1. Audit, architecture et modularisation

- [x] Auditer le theme existant et identifier les responsabilites: theme, PhotoVault Core, Identity Kit, Newsletter Kit.
- [x] Extraire la logique metier principale vers `photovault-core`.
- [x] Creer `identity-security-kit` comme plugin reutilisable non specifique PhotoVault.
- [x] Creer `newsletter-campaign-kit` comme plugin reutilisable non specifique PhotoVault.
- [x] Garder les plugins actifs synchronises avec les sources dans le theme pendant la phase de migration.
- [x] Ajouter ADR-001 pour la separation theme/plugins.
- [x] Ajouter un document de progression globale de plateforme.
- [~] Supprimer les fallbacks legacy du theme une fois toute la logique confirmee dans les plugins actifs.
- [~] Documenter exhaustivement les hooks publics, filtres, tables, options et capabilities de chaque plugin. README Identity actualise; exemples providers et compatibilite multisite restent a completer.

## 2. Securite fondamentale WordPress

- [x] Remplacer les controles purement visuels par des controles serveur pour les medias sensibles.
- [x] Utiliser nonces + capabilities sur les actions admin sensibles implementees.
- [x] Ajouter validation serveur pour inscriptions, profils, uploads media et abonnements newsletter.
- [x] Eviter les secrets dans les nouveaux fichiers versionnes.
- [x] Ajouter `SECURITY.md` avec posture, regles et gaps connus.
- [x] Terminer l'inventaire REST/AJAX avec classification public/auth/capability/ownership/protected.
- [~] Ajouter tests automatises CSRF, privilege escalation, IDOR, REST unauthorized et suppression de `required` HTML. Matrice media Core validee pour anonyme, non verifie, verifie, owner, grant, manager et admin; actions admin-post et autres plugins restantes.

## 3. PhotoVault Core - medias, galerie, acces

- [x] Enregistrer CPT `media_item` et taxonomies `media_folder` / `media_category` dans `photovault-core`.
- [x] Ajouter roles/capabilities PhotoVault et acces admin/media manager.
- [x] Servir les pages d'ensemble avec miniatures/variants et non les originaux 4K.
- [x] Utiliser `secure-image` pour previews et downloads controles.
- [x] Bloquer les medias prives dans listings REST et endpoint image si l'utilisateur n'a pas acces.
- [x] Exiger session + nonce + identite verifiee pour downloads sensibles non admin.
- [x] Garder l'acces complet admin/media manager.
- [x] Ajouter demandes d'acces aux collections protegees.
- [x] Ajouter grants d'acces par email hash + collection.
- [x] Appliquer les grants aux medias prives de collection.
- [x] Ajouter audit media: vues, previews, downloads, refus, grants, demandes.
- [x] Deplacer les originaux proteges/prives vers stockage prive durci pour les nouveaux medias controles.
- [x] Ajouter traitement admin par lots pour originaux existants.
- [x] Ajouter commande WP-CLI `wp photovault secure-originals`.
- [x] Ajouter ADR-002 pour le stockage prive des originaux.
- [~] Verifier en runtime les migrations DB, pages admin et traitement des originaux existants.
- [~] Verifier que Nginx/Apache bloque effectivement `wp-content/photovault-private/` en environnement cible. Nginx Docker valide; Apache et production restent a confirmer.
- [x] Ajouter cache/derivatives pour ne pas recalculer le filigrane a chaque requete.
- [ ] Ajouter tests d'ID guessing sur REST, preview et download.

## 4. Filigrane, thumbnails et telechargement

- [x] Generer/servir des tailles adaptees `card` et `preview` pour eviter les images HD en grille.
- [x] Rendre le filigrane serveur plus visible sur les medias proteges.
- [x] Empecher le download direct des medias proteges pour les utilisateurs non autorises.
- [x] Mettre le download HD derriere endpoint controle.
- [~] Generer ou regenerer toutes les miniatures des medias existants en runtime si necessaire.
- [x] Ajouter options admin pour opacite, texte, image personnalisee, densite et cache du filigrane.

## 5. Identity Security Kit

- [x] Creer plugin generique `identity-security-kit`.
- [x] Deplacer login, inscription, profil et forgot-password dans le kit.
- [x] Ajouter validation serveur des champs critiques.
- [x] Ajouter reponse anti-enumeration pour reset password.
- [x] Ajouter verification email.
- [x] Ajouter renvoi de verification email.
- [x] Ajouter changement d'email differe avec mot de passe, stockage chiffre, confirmation expirable et notification de l'ancienne adresse.
- [x] Ajouter audit identite sans secrets, reset keys ou IP brute.
- [x] Ajouter reglages bornes pour politiques de securite deja exposees.
- [x] Ajouter OTP email avec expiration, tentatives, anti-replay et rate limiting; moteur et refus critiques valides dans WordPress.
- [~] Ajouter OTP SMS/provider abstraction. Moteur, adapter generique, Twilio et fail-closed testes; credentials et livraison SMS reelle restent a valider.
- [~] Ajouter telephone international E.164 avec prefixe et unicite serveur. Stockage, unicite et verification OTP valides; librairie de plans et migration restent a faire.
- [~] Brancher les seconds facteurs email et telephone au challenge de connexion MFA generique. Enrollment, login et desactivation runtime valides; parcours guide de remplacement et E2E navigateur restent a faire.
- [x] Ajouter la desactivation TOTP/email/SMS avec mot de passe ou preuve du canal, invalidation des autres sessions et interdiction de retirer le dernier facteur obligatoire.
- [~] Ajouter TOTP/MFA. Enrolement, secret chiffre, anti-rejeu et login runtime valides; QR et E2E navigateur restent a faire.
- [x] Ajouter recovery codes avec generation 80 bits, hashes, affichage unique, consommation et anti-rejeu valides en runtime.
- [~] Ajouter grace period MFA 15 jours et enforcement wp-admin privilegie. Calcul J+15, rappels J+1/J+7/J+12 et changements de role/politique valides; matrice HTTP et multisite restent a faire.
- [x] Ajouter un cron MFA horaire borne, pagine, sans doublon et nettoye a la desactivation.
- [~] Ajouter invalidation de sessions sur evenement sensible. Autres sessions invalidees sur changement de mot de passe et changement MFA; autres evenements sensibles restent a inventorier.
- [x] Ajouter threat model identity documente.
- [x] Ajouter rate limiting login/register/forgot/resend verification configurable.
- [x] Uniformiser les flux email Identity et les notifications natives de changement email/mot de passe en templates HTML/texte multipart, avec CTA, OTP, branding filtrable et preuve Mailpit.

## 6. Newsletter Campaign Kit

- [x] Creer plugin generique `newsletter-campaign-kit`.
- [x] Ajouter capture d'abonnement avec consentement et nonce.
- [x] Stocker metadata sensible de maniere minimisee/hashee quand pertinent.
- [x] Ajouter unsubscribe via token serveur.
- [x] Ajouter one-click unsubscribe RFC 8058 avec POST idempotent et en-tetes HTTPS/DKIM explicites.
- [x] Ajouter suppression robuste avec registre HMAC durable, annulation de queue, refus audience/provider, levee explicite et blocage apres effacement/re-import.
- [x] Ajouter preferences thematiques publiques avec token opaque, nonce, GET non mutatif et application aux audiences/campagnes.
- [x] Integrer export, effacement et contenu de politique Privacy aux outils natifs WordPress.
- [x] Ajouter premiere UI admin abonnes.
- [x] Documenter capabilities et tables newsletter.
- [~] Ajouter listes, segments, tags et imports/exports. Fait: listes, tags, affectations admin, segments dynamiques all/any, lifecycle, volumes, import CSV et snapshots immuables valides en runtime. Reste: exports avances des audiences.
- [~] Ajouter campagnes, templates, etats et transitions serveur. Campagnes ciblees editables en brouillon, duplication sure, programmation, templates reutilisables avec cycle de vie, preview protegee HTML/texte et multipart valides en runtime; bibliotheque de blocs editoriaux restante.
- [~] Ajouter queue d'envoi batch + retry/backoff. Fait: queue idempotente, verrous atomiques, reprise stale, cron minute et test runtime. Reste: provider API et observabilite production.
- [~] Ajouter provider abstraction SMTP/API. Fait: provider `wp_mail`, reglages admin, filtre adaptateur externe. Reste: provider API dedie avec secrets hors Git.
- [~] Ajouter reporting campagne. Fait: rapports de livraison et preuve du snapshot d'audience depuis la queue. Reste: tracking ouvertures/clics et exports avances.
- [x] Ajouter audit newsletter.
- [x] Ajouter threat model newsletter.

## 7. Experience publique PhotoVault

- [x] Enrichir la home avec hero editorial, manifeste, oeuvres, collections, espaces public/protege, services, processus, timeline, FAQ et CTA.
- [x] Remplacer les images blog manquantes par illustration fallback coherente.
- [x] Reutiliser les posts blog dans `Carnets visuels` lorsque possible.
- [x] Completer footer avec navigation, galerie, collections, services, journal, compte, legal, newsletter.
- [x] Ajouter distinction galerie publique / collections protegees.
- [x] Garder style portfolio premium/editorial plutot que SaaS generique.
- [~] Verifier visuellement home, galerie, blog, detail media, profil et dashboard sur mobile/desktop.
- [~] Finaliser accessibilite clavier, focus visible, menu mobile, fermeture ESC/overlay. Le dashboard gere clavier, overlay et Echap; l'audit transversal reste a faire.
- [x] Ajouter et tester favoris, preferences et pages utilisateur completes: favoris, historique, acces, newsletter, securite et reservations sont integres avec isolation par compte.

## 8. Upload media et admin PhotoVault

- [x] Ajouter validation serveur des uploads image: taille, MIME/ext, dimensions, count.
- [x] Isoler certains uploads par utilisateur et durcir les dossiers.
- [x] Ajouter page admin acces/downloads.
- [x] Ajouter page admin demandes d'acces.
- [x] Ajouter page admin audit media.
- [x] Ameliorer l'UX upload: selection visible, progression par fichier, nom, taille, statut et succes/erreur.
- [x] Permettre edition rapide apres upload: titre, description, categorie, collection, confidentialite et tags, avec isolation owner/admin validee en runtime.
- [x] Ajouter module `Shootings` avec types, dates, lieux, contact, statuts, ownership, e-mails professionnels et transitions serveur verifies en runtime.
- [x] Professionnaliser les e-mails contact et acces: HTML/texte multipart, `Reply-To` sur l'adresse visiteur, accuse studio/client, decisions et preuve Mailpit.
- [x] Ajouter dashboard utilisateur complet: overview, profil, favoris, downloads, acces, preferences, securite, newsletter et reservations avec isolation par compte.

## 9. Blog, header, profil, pages connues

- [x] Masquer les champs de changement de mot de passe derriere une section progressive dans le profil.
- [x] Ajouter fallback image pour les blogs sans visuel.
- [~] Verifier pagination blog, query vars, liens et etats loading en runtime.
- [~] Verifier header mobile et navigation clavier en navigateur.
- [ ] Ajouter tests UX/responsive systematiques.

## 10. Docker, environnement et exploitation

- [x] Ajouter fichiers Docker a la racine WordPress: Nginx, PHP-FPM, MariaDB, Mailpit, cron, WP-CLI.
- [x] Ajouter `.env.example` sans secrets reels.
- [x] Ajouter `.dockerignore` excluant `.env`, uploads, cache et stockage prive.
- [x] Ajouter config Nginx bloquant `wp-content/photovault-private/`.
- [x] Valider `docker compose config`.
- [x] Ajouter doc Docker versionnee dans le theme.
- [x] Initialiser un depot Git d'infrastructure a la racine WordPress et versionner Docker sans inclure le coeur, les uploads, `.env` ni les depots imbriques.
- [x] Construire et lancer `docker compose up --build`; valider MariaDB, PHP-FPM, Nginx et Mailpit sains.
- [x] Verifier installation WordPress, trois plugins actifs, migrations, Mailpit et cron dans Docker.
- [x] Ajouter et tester sauvegarde/restauration DB, uploads et stockage prive: checksums, manifeste, base temporaire, maintenance, rollback et restauration reelle valides Docker.
- [~] Ajouter README principal installation/dev/prod: base complete ajoutee, a finaliser apres verification runtime/prod.

## 11. Tests et qualite

- [x] Lints PHP `php -l` lances sur les fichiers modifies lors des lots critiques.
- [x] `git diff --check` utilise avant commits.
- [~] Runtime Docker valide avec WordPress, WP-CLI, MariaDB, Nginx, Mailpit, cron et premiers flux Identity/Newsletter; parcours PhotoVault et E2E complets restent a valider.
- [ ] Ajouter PHPCS / WordPress Coding Standards.
- [~] Ajouter tests Identity: expiration, attempts, replay et purpose valides en runtime; mauvais utilisateur, concurrence resend, UI et politique HTTP restent a couvrir.
- [~] Ajouter tests Newsletter: segmentation, suppression, unsubscribe, retry, idempotence. Segmentation, scheduler, one-click, preferences/CSRF, suppression durable, Privacy, templates/multipart et idempotence valides; couverture retry exhaustive restante.
- [~] Ajouter tests REST authorization matrix. Core media couvre liste, ID guessing, pagination privee, grants, favoris, import, nonce et refus download; Identity, Newsletter et matrice HTTP complete restent a faire.
- [ ] Ajouter tests e2e: register, verify email, login, gallery, protected media, newsletter subscribe/unsubscribe.

## 12. Documentation et reprise equipe

- [x] Ajouter `SECURITY.md`.
- [x] Ajouter `doc/architecture.md`.
- [x] Ajouter `doc/progression-plateforme.md`.
- [x] Ajouter `doc/docker.md`.
- [x] Ajouter ADR-001 separation plugins.
- [x] Ajouter ADR-002 stockage originaux proteges.
- [x] Ajouter README principal complet.
- [x] Ajouter README par plugin.
- [x] Ajouter matrice capabilities par role.
- [x] Ajouter inventaire REST/AJAX complet.
- [x] Ajouter threat models media, identity, newsletter.

## Statut global

Progression recalculee: 73% d'implementation fonctionnelle et 63% de preparation production stricte.

L'ancien calcul sur 125 lignes agregees surestimait fortement le resultat. La nouvelle matrice repart des 113 sections techniques du cahier initial et applique sa Definition of Done. Voir [progression-objectif-initial-v2.md](progression-objectif-initial-v2.md).

Le projet a maintenant ses fondations modulaires, une securite media applicative solide, une premiere protection de stockage prive, l'identite email, l'audit, les bases newsletter avec campagnes et reporting, une experience publique enrichie, une bibliotheque personnelle, un dashboard role-aware, les reservations, l'import media et la matrice d'autorisation Core verifies en runtime. Il ne doit pas encore etre declare production-ready tant que les matrices Identity/Newsletter/admin-post, les parcours navigateur critiques, la preuve multipart post-correction et l'exploitation de production n'ont pas ete verifies ou termines.
