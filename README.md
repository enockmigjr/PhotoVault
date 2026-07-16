# PhotoVault

PhotoVault est une plateforme WordPress modulaire pour archives visuelles, portfolio photographique, collections protegees et services de shooting.

Le projet est organise autour d'un theme editorial et de trois plugins metier reutilisables:

- `PhotoVault` theme: experience publique, pages editoriales, galerie, templates media, blog, profil et dashboard.
- `photovault-core`: medias, collections, acces proteges, secure-image, downloads, thumbnails, stockage prive et audit media.
- `identity-security-kit`: login, inscription, profil, reset password, verification email, politiques d'identite et audit.
- `newsletter-campaign-kit`: abonnements consentis, unsubscribe tokenise, administration abonnes et future base campagnes.

## Etat actuel

Mise en oeuvre logicielle: 100 %. Cinquante-six preuves sur 64 sont reproductibles localement; les huit preuves externes dependent du packaging final, de providers et de l'environnement de recette/production. Voir la checklist canonique et la recette externe.

Les fondations principales sont en place: separation theme/plugins, controles serveur sur les medias sensibles, previews/miniatures au lieu des originaux HD dans les vues ensemble, endpoint de telechargement controle, verification email, audit, campagnes newsletter, dashboard personnel, reservations de shootings et Docker.

Le runtime local WordPress/MySQL, le blocage Nginx, les tests critiques et les parcours navigateur principaux sont confirmes. La mise en production exige encore TLS/domaine, DKIM, credentials de staging, supervision, CI et recette accessibilite.

## Installation locale classique

1. Copier le theme dans `wp-content/themes/PhotoVault`.
2. Copier les plugins actifs dans `wp-content/plugins/photovault-core`, `wp-content/plugins/identity-security-kit` et `wp-content/plugins/newsletter-campaign-kit`.
3. Activer les trois plugins depuis WordPress.
4. Activer le theme PhotoVault.
5. Aller dans l'admin PhotoVault pour verifier les pages media, demandes d'acces, audit et reglages.
6. Regenerer ou securiser les originaux existants si le site contient deja des medias.

## Installation Docker

Une base Docker existe a la racine WordPress avec Nginx, PHP-FPM, MariaDB, Mailpit, cron et WP-CLI.

Voir [doc/docker.md](doc/docker.md).

La pile a ete validee en execution avec `docker compose`: Nginx, WordPress/PHP-FPM, MariaDB, Mailpit et cron disposent de controles de sante.

## Styles frontend

Tailwind CSS est compile localement puis commite dans `css/tailwind.css`. Le theme ne depend donc pas du Play CDN en production.

```bash
pnpm install
pnpm run build:css
```

Utiliser `pnpm run watch:css` pendant le developpement des templates. Le paquet natif optionnel `@parcel/watcher` reste desactive dans la configuration pnpm: le build CSS statique n'en depend pas.

Les tests navigateur utilisent la dependance de developpement Playwright. Sur une nouvelle machine, installer son navigateur une fois avec `pnpm exec playwright install chromium`, puis fournir les variables `PHOTOVAULT_TEST_USERNAME` et `PHOTOVAULT_TEST_PASSWORD`. Le test `node tests/browser-provider-admin.js` controle les deux diagnostics provider et peut enregistrer des captures via `PHOTOVAULT_TEST_SCREENSHOT_DIR`.

## Documentation utile

- [Objectif initial et progression](doc/tasks-objectif-initial.md)
- [Architecture](doc/architecture.md)
- [Securite](SECURITY.md)
- [Inventaire REST/AJAX](doc/rest-ajax-inventory.md)
- [Matrice capabilities](doc/capabilities-matrix.md)
- [Tables, options et hooks](doc/plugin-surfaces.md)
- [Threat model media](doc/threat-model-media.md)
- [Threat model identity](doc/threat-model-identity.md)
- [Threat model newsletter](doc/threat-model-newsletter.md)
- [ADR separation plugins](doc/adr/ADR-001-plugin-separation.md)
- [ADR stockage prive](doc/adr/ADR-002-protected-media-storage.md)

## Securite media

Les pages d'ensemble doivent charger des variantes adaptees (`card`, `preview`) et non les originaux 4K. Les originaux HD sont servis uniquement via `secure-image` en mode download, avec controles serveur.

Les medias proteges/prives utilisent:

- filtrage serveur dans les listes REST;
- verification d'acces par ownership, grant, role media manager ou admin;
- nonce REST pour les downloads;
- verification email pour les utilisateurs non privilegies;
- stockage prive des originaux sensibles quand la migration est appliquee;
- audit des previews, downloads et refus.

## Commandes utiles

```bash
# Securiser les originaux existants si WP-CLI est disponible
wp photovault secure-originals --limit=25

# Installer une base de demonstration idempotente
wp photovault seed_demo

# Verification Git basique
git status --short
git diff --check
```

## Depots

Le theme et les plugins actifs sont des depots Git separes. Quand un plugin actif est modifie, committer le plugin concerne dans son depot, puis committer le theme/projet principal si sa documentation ou ses copies sources ont ete modifiees.

## Configuration des providers

Les secrets restent hors de WordPress et de Git. Les noms exacts et exemples wp-config sont documentes dans les README de identity-security-kit et newsletter-campaign-kit. Brevo est le choix recommande pour SMS/newsletter; Twilio et Resend restent disponibles.

## Reste externe

La liste exhaustive et non surestimee est maintenue dans [doc/tasks-objectif-initial.md](doc/tasks-objectif-initial.md). Les points ouverts concernent principalement providers reels, DKIM, multisite/analytics si retenus, accessibilite de recette, CI et hebergement final. Chaque recette, critere et preuve attendue est detaille dans [doc/validation-externe.md](doc/validation-externe.md).
