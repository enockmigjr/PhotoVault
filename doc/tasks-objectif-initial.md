# Tasks - objectif initial PhotoVault

Derniere mise a jour: 2026-07-13

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
- [~] Documenter exhaustivement les hooks publics, filtres, tables, options et capabilities de chaque plugin. Les surfaces MFA nouvelles restent a reporter dans le README Identity apres l echec du sandbox.

## 2. Securite fondamentale WordPress

- [x] Remplacer les controles purement visuels par des controles serveur pour les medias sensibles.
- [x] Utiliser nonces + capabilities sur les actions admin sensibles implementees.
- [x] Ajouter validation serveur pour inscriptions, profils, uploads media et abonnements newsletter.
- [x] Eviter les secrets dans les nouveaux fichiers versionnes.
- [x] Ajouter `SECURITY.md` avec posture, regles et gaps connus.
- [x] Terminer l'inventaire REST/AJAX avec classification public/auth/capability/ownership/protected.
- [ ] Ajouter tests automatises CSRF, privilege escalation, IDOR, REST unauthorized et suppression de `required` HTML.

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
- [x] Ajouter audit identite sans secrets, reset keys ou IP brute.
- [x] Ajouter reglages bornes pour politiques de securite deja exposees.
- [~] Ajouter OTP email avec expiration, tentatives, anti-replay et rate limiting. Backend generique, shortcode, hooks, reglages bornes et audit ajoutes; reste validation runtime et tests automatises.
- [~] Ajouter OTP SMS/provider abstraction. Moteur OTP multicanal, adapter Twilio sans secrets, hook provider et test cible ajoutes; credentials et livraison runtime restent a valider.
- [~] Ajouter telephone international E.164 avec prefixe et unicite serveur. Plugin et formulaires PhotoVault implementes; verification telephone runtime et migration des comptes existants restent a valider.
- [~] Brancher les seconds facteurs email et telephone au challenge de connexion MFA generique. Enrollment explicite, preference, choix login et recovery sont cables; runtime WordPress et remplacement/desactivation des facteurs restent a valider.
- [~] Ajouter TOTP/MFA. Moteur RFC 6238, secret chiffre, anti-rejeu, enrolement et challenge de connexion implementes; runtime WordPress bout-en-bout reste a valider.
- [~] Ajouter recovery codes. Generation 80 bits, hashes, affichage unique et consommation atomique implementes; runtime reste a valider.
- [~] Ajouter grace period MFA 15 jours et enforcement wp-admin privilegie. Politique par capabilities, avertissement, blocage admin/AJAX et allowlist d enrolement implementes; runtime reste a valider.
- [~] Ajouter invalidation de sessions sur evenement sensible. Autres sessions invalidees sur changement de mot de passe et changement MFA; autres evenements sensibles restent a inventorier.
- [x] Ajouter threat model identity documente.
- [x] Ajouter rate limiting login/register/forgot/resend verification configurable.

## 6. Newsletter Campaign Kit

- [x] Creer plugin generique `newsletter-campaign-kit`.
- [x] Ajouter capture d'abonnement avec consentement et nonce.
- [x] Stocker metadata sensible de maniere minimisee/hashee quand pertinent.
- [x] Ajouter unsubscribe via token serveur.
- [x] Ajouter premiere UI admin abonnes.
- [x] Documenter capabilities et tables newsletter.
- [~] Ajouter listes, segments, tags et imports/exports. Fait: listes/tags/admin/liaisons. Reste: imports/exports avances.
- [~] Ajouter campagnes, templates, etats et transitions serveur. Fait: table campagnes, brouillons, UI admin, programmation timezone-aware et transitions serveur. Reste: templates reutilisables/previsualisation.
- [~] Ajouter queue d'envoi batch + retry/backoff. Fait: queue idempotente, verrous atomiques, reprise stale, cron minute et test runtime. Reste: provider API et observabilite production.
- [~] Ajouter provider abstraction SMTP/API. Fait: provider `wp_mail`, reglages admin, filtre adaptateur externe. Reste: provider API dedie avec secrets hors Git.
- [~] Ajouter reporting campagne. Fait: rapports de livraison depuis la queue. Reste: tracking ouvertures/clics et exports avances.
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
- [ ] Finaliser accessibilite clavier, focus visible, menu mobile, fermeture ESC/overlay.
- [ ] Ajouter et tester favoris, preferences et pages utilisateur completes.

## 8. Upload media et admin PhotoVault

- [x] Ajouter validation serveur des uploads image: taille, MIME/ext, dimensions, count.
- [x] Isoler certains uploads par utilisateur et durcir les dossiers.
- [x] Ajouter page admin acces/downloads.
- [x] Ajouter page admin demandes d'acces.
- [x] Ajouter page admin audit media.
- [~] Ameliorer l'UX upload: progression, nom, taille, statut, succes/erreur.
- [~] Permettre edition rapide apres upload: titre, description, categorie, collection, confidentialite, tags.
- [ ] Ajouter module `Shootings` avec types, dates, lieux, contact, statuts et transitions serveur.
- [ ] Ajouter dashboard utilisateur complet: overview, profil, favoris, downloads, acces, preferences, securite, newsletter, reservations.

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
- [~] Verifier installation WordPress, plugins actifs, Mailpit et cron dans Docker. Mailpit, transport SMTP et cron valides; installation WordPress et plugins en attente d'initialisation admin.
- [ ] Ajouter procedures sauvegarde/restauration DB et medias.
- [~] Ajouter README principal installation/dev/prod: base complete ajoutee, a finaliser apres verification runtime/prod.

## 11. Tests et qualite

- [x] Lints PHP `php -l` lances sur les fichiers modifies lors des lots critiques.
- [x] `git diff --check` utilise avant commits.
- [~] Runtime Docker disponible avec WP-CLI 2.12.0, MariaDB, Nginx, Mailpit et cron; installation WordPress et flux applicatifs complets encore a initialiser.
- [ ] Ajouter PHPCS / WordPress Coding Standards.
- [ ] Ajouter tests unitaires Identity: OTP expiration, attempts, replay, purpose.
- [ ] Ajouter tests Newsletter: segmentation, suppression, unsubscribe, retry, idempotence.
- [ ] Ajouter tests REST authorization matrix.
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

Progression corrigee: 48% d'implementation fonctionnelle et 36% de preparation production stricte.

L'ancien calcul sur 125 lignes agregees surestimait fortement le resultat. La nouvelle matrice repart des 113 sections techniques du cahier initial et applique sa Definition of Done. Voir [progression-objectif-initial-v2.md](progression-objectif-initial-v2.md).

Le projet a maintenant ses fondations modulaires, une securite media applicative solide, une premiere protection de stockage prive, l'identite email, l'audit, les bases newsletter avec audit, campagnes, queue, provider et reporting, une experience publique enrichie et une base Docker. Il ne doit pas encore etre declare production-ready tant que le runtime WordPress, les tests automatises, les fonctions Identity avancees, les tests d'autorisation, le dashboard utilisateur, le module shootings et la configuration serveur cible n'ont pas ete verifies ou termines.
