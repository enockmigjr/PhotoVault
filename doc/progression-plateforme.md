# Progression plateforme PhotoVault

Derniere mise a jour: 2026-07-08

## Objectif initial

Faire evoluer PhotoVault d'un theme WordPress centre sur l'affichage vers une plateforme modulaire, securisee, extensible et exploitable en production.

Le cap reste le meme:

- theme PhotoVault pour l'experience editoriale et visuelle;
- `photovault-core` pour les regles metier media, collections, acces, downloads et audit;
- `identity-security-kit` pour les fonctions d'identite reutilisables;
- `newsletter-campaign-kit` pour les abonnements, consentements et campagnes;
- controle serveur reel, pas seulement des boutons masques;
- documentation et verification progressive a chaque lot.

## Cartographie actuelle

```text
PhotoVault
|-- Theme
|   |-- templates publics, home, galerie, detail media, contact, profil
|   |-- presentation editoriale, blog, footer, navigation
|   `-- fallbacks legers pendant la migration
|-- plugins/photovault-core
|   |-- CPT media_item, taxonomies media_folder/media_category
|   |-- roles/capabilities PhotoVault
|   |-- REST media + secure-image
|   |-- thumbnails/previews + download HD controle
|   |-- collections protegees, demandes d'acces, grants
|   `-- audit media
|-- plugins/identity-security-kit
|   |-- login/register/profile/forgot password
|   |-- verification email
|   |-- resend verification
|   `-- audit identite
|-- plugins/newsletter-campaign-kit
|   |-- subscribers
|   |-- consentement
|   |-- unsubscribe tokenise
|   `-- base admin newsletter
`-- doc
    |-- architecture
    |-- securite
    |-- guide utilisateur
    `-- ADR
```

## Travaux termines

### Architecture

- Separation progressive du theme et des plugins.
- Copies sources des plugins conservees dans le theme pendant la phase de migration.
- Depots actifs separes pour `photovault-core`, `identity-security-kit` et `newsletter-campaign-kit`.
- Commits separes effectues lorsque les plugins actifs sont touches.

### Media et galerie

- Les pages d'ensemble utilisent des variantes `card`/`preview` au lieu de servir les originaux HD.
- Les originaux HD ne sont servis que via `download=1` apres verification serveur.
- Le proxy `secure-image` applique les regles d'acces cote serveur.
- Les medias prives sont filtres dans la REST list et dans le proxy image.
- Les medias proteges recoivent un filigrane serveur plus visible.
- Les admins et media managers gardent l'acces complet.

### Acces proteges

- Formulaire de demande d'acces aux collections protegees.
- Table de demandes d'acces.
- Table de grants par email hash + collection.
- Approbation admin avec creation de grant.
- Application des grants aux medias prives rattaches aux collections.
- Verification email requise pour les utilisateurs non privilegies.

### Identite

- Kit generique separe.
- Inscription, connexion, profil, reset password et validation serveur.
- Verification email et renvoi du challenge.
- Audit identite sans mot de passe, reset key, IP brute ou secret complet.

### Newsletter

- Kit generique separe.
- Abonnement avec consentement et nonce.
- Stockage des metadonnees sensibles sous forme hashee lorsque pertinent.
- Desinscription via token serveur.
- Premiere interface admin abonnes.

### Experience publique

- Home enrichie avec hero, manifeste, oeuvres, collections, services, processus, journal, FAQ et CTA.
- Blog reutilise pour les carnets visuels lorsque disponible.
- Illustration fallback lorsque l'image de blog manque.
- Footer plus complet.
- Page detail media avec affichage protege, filigrane, metadata, download controle et medias similaires.

### Observabilite

- Audit media ajoute dans `photovault-core`.
- Evenements traces: vues, previews, previews protegees, downloads, refus, demandes d'acces, grants crees ou echoues.
- IP hachee, user-agent tronque, contexte nettoye.
- Page admin `Audit media`.

## Diagnostic actuel

### Critique

- Les originaux restent potentiellement accessibles si une URL directe `wp-content/uploads/...` est connue. Le proxy WordPress protege les parcours controles, mais pas encore le stockage public lui-meme.
- La verification runtime complete depend de XAMPP/MySQL actif. Sans base accessible, les migrations `dbDelta`, pages admin et flux bout-en-bout ne peuvent pas etre confirmes.

### Eleve

- Les tests automatises d'autorisation REST/AJAX/media ne sont pas encore en place.
- Le workflow upload admin doit encore offrir une UX plus complete: progression, statut, edition rapide des metadonnees apres selection.
- Les politiques MFA/OTP/recovery codes ne sont pas encore implementees dans Identity Kit.
- La newsletter n'a pas encore de queue d'envoi, state machine de campagne, provider abstraction ni reporting.

### Moyen

- Le header mobile et le dashboard utilisateur doivent etre testes visuellement sur petits ecrans.
- La pagination blog et les etats loading doivent etre verifies sur donnees reelles.
- L'audit media n'a pas encore de retention/export.
- La documentation des capabilities et tables par plugin doit etre detaillee.

### Faible

- Harmonisation finale des textes admin et i18n.
- ADR supplementaires a rediger pour media proteges, identity policy, newsletter queue et Docker.

## Backlog priorise

### P0 - Securite media reelle

1. Definir la strategie de stockage des originaux: dossier prive hors webroot, regles serveur, ou migration progressive.
2. Ajouter une politique claire: public HD, logged-in, owner/admin, collection authorized.
3. Ajouter tests d'ID guessing sur REST, preview et download.

### P1 - Verification runtime

1. Demarrer XAMPP/MySQL.
2. Activer/verifier les trois plugins actifs.
3. Verifier les tables creees: access requests, grants, media audit, identity audit, newsletter subscribers.
4. Tester les roles: anonymous, user non verifie, user verifie, owner, media manager, admin.

### P1 - Identity Kit

1. Ajouter OTP email avec expiration, tentatives et anti-replay.
2. Ajouter base MFA/TOTP et recovery codes.
3. Ajouter politiques configurables avec bornes serveur.
4. Ajouter page admin Security Audit/Policies.

### P1 - Newsletter Kit

1. Ajouter campagnes, templates et statuts.
2. Ajouter queue d'envoi par batch.
3. Ajouter unsubscribe/suppression robuste.
4. Ajouter reporting de base.

### P2 - UX metier PhotoVault

1. Ameliorer upload media admin.
2. Ajouter module shooting: type, date, lieu, contact, statut.
3. Completer dashboard utilisateur: profil, favoris, downloads, acces collections, preferences, securite, newsletter, reservations.
4. Verifier responsive mobile et accessibilite clavier.

### P2 - Exploitation

1. Ajouter Docker Compose avec Nginx, PHP-FPM, DB, Mailpit, wpcli/cron selon besoin.
2. Ajouter `.env.example`.
3. Ajouter README principal production/dev.
4. Ajouter healthchecks et docs cron.

### P3 - Qualite

1. Ajouter PHPCS/WordPress Coding Standards si Composer est introduit.
2. Ajouter tests unitaires/integration ciblant les regles critiques.
3. Ajouter matrice d'autorisation documentee.
4. Ajouter ADR supplementaires.

## Definition de progression

Estimation actuelle: 78%.

Cette estimation reflete que les fondations les plus importantes sont posees: modularisation, securite media applicative, verification email, demandes d'acces, audit, enrichissement public et depots plugins separes.

Elle ne signifie pas encore "production ready". Les deux gros blocs qui empechent ce label sont:

- protection serveur/stockage des originaux;
- tests runtime automatises et verification complete avec WordPress actif.
