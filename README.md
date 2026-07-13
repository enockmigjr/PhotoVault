# PhotoVault

PhotoVault est une plateforme WordPress modulaire pour archives visuelles, portfolio photographique, collections protegees et services de shooting.

Le projet est organise autour d'un theme editorial et de trois plugins metier reutilisables:

- `PhotoVault` theme: experience publique, pages editoriales, galerie, templates media, blog, profil et dashboard.
- `photovault-core`: medias, collections, acces proteges, secure-image, downloads, thumbnails, stockage prive et audit media.
- `identity-security-kit`: login, inscription, profil, reset password, verification email, politiques d'identite et audit.
- `newsletter-campaign-kit`: abonnements consentis, unsubscribe tokenise, administration abonnes et future base campagnes.

## Etat actuel

Progression reelle estimee: 64% fonctionnel, 53% production stricte.

Les fondations principales sont en place: separation theme/plugins, controles serveur sur les medias sensibles, previews/miniatures au lieu des originaux HD dans les vues ensemble, endpoint de telechargement controle, verification email, audit, newsletter de base, documentation securite et Docker.

Le projet ne doit pas encore etre considere production-ready tant que la verification runtime WordPress/MySQL, les tests automatises et le blocage serveur du stockage prive n'ont pas ete confirmes.

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

La configuration a ete validee avec `docker compose config`, mais `docker compose up --build` n'a pas encore ete execute dans cet environnement.

## Documentation utile

- [Objectif initial et progression](doc/tasks-objectif-initial.md)
- [Progression plateforme](doc/progression-plateforme.md)
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

# Verification Git basique
git status --short
git diff --check
```

## Depots

Le theme et les plugins actifs sont des depots Git separes. Quand un plugin actif est modifie, committer le plugin concerne dans son depot, puis committer le theme/projet principal si sa documentation ou ses copies sources ont ete modifiees.

## Reste majeur

- Verification runtime WordPress/MySQL/Docker.
- Tests REST/AJAX, CSRF, IDOR, privilege escalation et e2e.
- Cache des previews filigranees.
- Options admin avancees du filigrane.
- OTP, MFA, recovery codes et invalidation de sessions.
- Campagnes newsletter, queue d'envoi, provider et reporting.
- Module shootings et dashboard utilisateur complet.
- README plus oriente production lorsque l'environnement cible sera valide.
