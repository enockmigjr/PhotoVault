# Objectif initial PhotoVault - suivi canonique

Derniere mise a jour: 2026-07-16

Ce document est l'unique source de verite pour la progression. Une tache est cochee seulement quand le code est cable et qu'une preuve locale existe. Les validations exigeant un fournisseur ou un environnement de production restent explicitement separees.

## Architecture et modularite

- [x] Separer le theme editorial, photovault-core, identity-security-kit et newsletter-campaign-kit.
- [x] Donner aux trois plugins actifs leur depot, leur version, leur README et leurs tests runtime.
- [x] Conserver le theme centre sur les templates, le layout, la navigation et l'experience utilisateur.
- [x] Centraliser les regles media, identite et newsletter dans leurs plugins.
- [x] Documenter les capabilities, tables, endpoints, hooks, menaces et decisions d'architecture.
- [x] Formaliser le packaging: depots actifs comme sources d'execution, miroirs de distribution non charges et verifies identiques.

## Medias et galerie

- [x] Charger des variantes card/preview dans toutes les vues d'ensemble, jamais l'original 4K.
- [x] Reserver l'original HD au endpoint de telechargement autorise.
- [x] Filtrer les medias prives cote serveur et appliquer ownership, grants et capabilities.
- [x] Servir les apercus proteges avec un filigrane diagonal visible qui couvre l'image.
- [x] Stocker les originaux sensibles hors de l'arborescence publique et fournir migration/cache.
- [x] Fournir une visionneuse plein ecran avec precedent, suivant, clavier, swipe et fermeture.
- [x] Rendre les filtres galerie compacts et horizontalement defilables sur petit ecran.
- [x] Garder l'acces complet aux administrateurs et media managers.
- [x] Ajouter favoris, historique de telechargement, collections autorisees et audit.
- [x] Valider la galerie peuplee en navigateur desktop et mobile sans debordement.

## Identite et profil

- [x] Implementer inscription, connexion, reset, verification email et changement d'email confirme.
- [x] Corriger l'upload avatar independamment des champs telephone/profil et le valider en multipart reel.
- [x] Integrer le profil dans le layout Dashboard sans header/footer public duplique.
- [x] Utiliser des lignes non editables puis des modales/actions ciblees pour profil, telephone, email et mot de passe.
- [x] Ajouter fermeture des notifications, etats de chargement et prevention des doubles soumissions.
- [x] Valider les numeros avec giggsey/libphonenumber-for-php-lite et normaliser en E.164.
- [x] Implementer OTP email/SMS, TOTP, QR local, recovery codes et methode preferee repliee.
- [x] Implementer grace MFA, rappels, retrait re-authentifie et invalidation des autres sessions.
- [x] Habiller le challenge MFA WordPress et corriger la redirection vide apres validation.
- [x] Paginer et filtrer l'audit de securite avec details nettoyes.
- [x] Uniformiser verification, reset, OTP, changement email/mot de passe et recovery mode en emails HTML/texte.
- [x] Valider avatar, MFA, audit et layout profil sur WordPress reel.
- [ ] Valider une livraison SMS avec credentials Brevo ou Twilio de staging.
- [x] Fournir dans l'administration un diagnostic SMS protege, limite et audite pour cette recette.
- [x] Consigner WordPress Multisite comme non retenu pour la livraison actuelle; une recette d'isolation est fournie pour toute activation future.

## Newsletter

- [x] Implementer consentement, double opt-in, rate limits, unsubscribe et RFC 8058.
- [x] Implementer listes, tags, segments dynamiques et thematiques.
- [x] Capturer plusieurs thematiques des le formulaire footer/dashboard et les conserver pendant le pending.
- [x] Ajouter centre de preferences securise avec retour vers PhotoVault.
- [x] Implementer import CSV, exports, Privacy, suppressions durables et levee explicite.
- [x] Implementer templates, blocs editoriaux, campagnes, revue finale, programmation et duplication.
- [x] Implementer queue, verrous, retry/backoff, cron supervise, snapshots immuables et rapports.
- [x] Fournir wp_mail, Brevo, Resend, HTTP generique et filtre d'adaptation sans secret en base.
- [x] Transformer manuellement un article publie en brouillon de campagne sans ressaisie.
- [x] Proposer les modes publication article: manuel, brouillon automatique ou envoi automatique explicite.
- [x] Paginer toutes les listes et afficher les details de queue/audit.
- [x] Uniformiser les formulaires admin et prevenir les doubles soumissions.
- [x] Valider preferences, article vers campagne, paginations, queue et remise wp_mail dans Docker/Mailpit.
- [ ] Valider une campagne avec une cle Brevo ou Resend et un domaine DKIM de staging.
- [x] Fournir un email de diagnostic professionnel pour chaque transport, sans creer d'abonne ni de campagne.
- [x] Conserver le tracking ouverture/clic desactive par defaut tant qu'aucune decision consentement/analytics ne justifie son activation.

## Experience publique

- [x] Construire la home editoriale complete: hero, manifeste, oeuvres, collections, espaces, services, processus, chiffres, timeline, temoignages, carnets, expositions, FAQ et CTA.
- [x] Utiliser les vrais articles pour les carnets et une illustration coherente lorsqu'une image manque.
- [x] Rendre header, logo, libelles, footer et menus configurables depuis WordPress.
- [x] Corriger l'etat actif Journal sur la home et supporter les sous-menus.
- [x] Completer Dashboard, profil, reservations, acces, newsletter, favoris et analytics.
- [x] Ajouter loaders globaux et annulation des requetes galerie devenues obsoletes.
- [x] Valider navigation, profil, galerie et visionneuse en navigateur, puis les cinq routes critiques avec Axe WCAG 2.0/2.1 A/AA sans violation serieuse ou critique.
- [x] Recette WCAG assistee acceptee par le proprietaire; controle vocal NVDA differe sans bloquer la livraison.

## Administration et donnees

- [x] Fournir import media avec progression, edition immediate et validation serveur.
- [x] Fournir demandes d'acces, grants, shootings, audit et securisation par lots.
- [x] Unifier les ecrans PhotoVault avec tableaux responsives, pagination, details et loaders.
- [x] Fournir wp photovault seed_demo, idempotent, pour installer des donnees de demonstration riches.
- [x] Installer localement 48 medias, 12 articles, 15 visiteurs, 45 demandes, 10 grants, 36 shootings, 80 abonnes, 30 campagnes et des journaux volumineux.

## Exploitation et preuves

- [x] Fournir Docker Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI avec healthchecks.
- [x] Fournir un Makefile d'exploitation, documenter le deploiement mono-hote et valider build, healthchecks, WordPress, theme, plugins et cron.
- [x] Bloquer le stockage prive dans Nginx et valider sauvegarde/restauration avec checksums.
- [x] Executer lints PHP, git diff --check, tests runtime Identity/Newsletter/Core et tests navigateur critiques.
- [x] Fournir les emplacements exacts des cles API dans les README et interfaces admin.
- [x] Fournir une recette executable et des criteres de preuve pour chaque validation externe dans `doc/validation-externe.md`.
- [x] Executer WPCS 3.3 dans GitHub Actions avec une baseline versionnee qui interdit toute nouvelle violation.
- [ ] Valider TLS, headers, cache, sauvegarde hors site, rotation des secrets et supervision sur l'hebergement final.

## Recette ciblee des 14 demandes de cloture

| # | Demande | Statut et preuve locale |
|---:|---|---|
| 1 | Header et footer configurables | Valide: controles et valeurs par defaut dans `inc/customizer.php`. |
| 2 | Methode MFA preferee masquee | Valide: section repliee avec `details` dans Identity Kit. |
| 3 | README et documentation d'integration | Valide: README des quatre depots et guides canoniques actualises. |
| 4 | Emplacements des cles API | Valide: exemples `wp-config.php` affiches dans les deux interfaces provider et documentes. |
| 5 | Pagination des logs Identity | Valide: filtres, pagination serveur et details nettoyes testes en runtime. |
| 6 | Formulaires et listes Newsletter | Valide: styles communs, largeurs responsives et pagination sur abonnes, campagnes, segments, listes, tags, sujets, templates, blocs, queue et audit. |
| 7 | Administration PhotoVault et acces proteges | Valide: tableaux responsives, actions securisees, demandes, grants et journal media. |
| 8 | Loaders et doubles soumissions | Valide: etats globaux publics/admin et annulation des requetes galerie obsoletes. |
| 9 | Avatar independant du telephone | Valide: upload multipart reel et profil mis a jour sans exiger un numero. |
| 10 | Navigation active et sous-menus | Valide: etat Journal corrige, sous-menus desktop/mobile et libelles personnalisables. |
| 11 | Filtres galerie sur petit ecran | Valide: barre compacte horizontalement defilable et visionneuse plein ecran. |
| 12 | Parcours verification et newsletter | Valide: redirections PhotoVault, preferences, footer/dashboard et multi-thematiques. |
| 13 | Challenge MFA WordPress | Valide: habillage coherent et redirection post-challenge non vide. |
| 14 | Article vers newsletter | Valide: brouillon manuel sans ressaisie, modes brouillon automatique et envoi automatique explicite. |

Preuves du 2026-07-16: diagnostics providers rendus a 1440 px sans debordement ni erreur fatale; cinq routes Axe sans violation serieuse ou critique; dialogues ouverts nommes et valides par Axe; reflow equivalent a 200 %, piege/retour du focus et galerie clavier valides; seed version 2 rejoue deux fois avec 10 grants distincts sans duplication; deploiement Docker reconstruit avec cinq services sains, `make verify` valide et cron Newsletter execute.

## Progression

- Mise en oeuvre logicielle demandee: **100 %**. Les fonctionnalites, diagnostics et procedures executables sont livres.
- Preuves locales, simulees ou acceptees par le proprietaire: **62 exigences validees sur 65 (95%)**.
- Recette externe: **3 preuves restantes**, toutes detaillees dans `doc/validation-externe.md`.

Les trois points restants ne cachent pas de fonctionnalite locale inachevee: ils dependent des credentials Twilio, d'une cle Resend avec domaine DKIM et de l'hebergement final. Aucun de ces points ne doit etre marque termine sans sa preuve externe.
