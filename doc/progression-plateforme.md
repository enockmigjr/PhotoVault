# Progression plateforme PhotoVault

Derniere mise a jour: 2026-07-09

## Objectif initial

Checklist detaillee: [tasks-objectif-initial.md](tasks-objectif-initial.md).
Inventaire REST/AJAX: [rest-ajax-inventory.md](rest-ajax-inventory.md).
Matrice capabilities: [capabilities-matrix.md](capabilities-matrix.md).

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

### Securite des points d'entree

- Inventaire REST/AJAX/admin-post ajoute avec exposition, controles, risques residuels et tests attendus.
- Les routes media et image sont classees entre liste authentifiee et endpoint public de transport avec garde-fous internes.
- Les actions admin-post sensibles sont cartographiees par capability, nonce et scenario de test.
- La matrice des capabilities documente les permissions actuelles et la delegation cible par profil.
## Diagnostic actuel

### Critique

- La verification runtime complete depend de XAMPP/MySQL actif. Sans base accessible, les migrations `dbDelta`, pages admin et flux bout-en-bout ne peuvent pas etre confirmes.

### Eleve

- Les originaux sensibles disposent maintenant d'une premiere migration vers stockage prive, mais les existants doivent etre traites en admin et la regle serveur Nginx/Apache doit etre verifiee en production.
- L'inventaire REST/AJAX est documente, mais les tests automatises correspondants ne sont pas encore en place.
- Le workflow upload admin doit encore offrir une UX plus complete: progression, statut, edition rapide des metadonnees apres selection.
- Les politiques MFA/OTP/recovery codes ne sont pas encore implementees dans Identity Kit.
- La newsletter n'a pas encore de queue d'envoi, state machine de campagne, provider abstraction ni reporting.

### Moyen

- Le header mobile et le dashboard utilisateur doivent etre testes visuellement sur petits ecrans.
- La pagination blog et les etats loading doivent etre verifies sur donnees reelles.
- L'audit media n'a pas encore de retention/export.
- La documentation des tables par plugin doit encore etre detaillee; la matrice capabilities est ajoutee.

### Faible

- Harmonisation finale des textes admin et i18n.
- ADR supplementaires a rediger pour media proteges, identity policy, newsletter queue et Docker.

## Backlog priorise

### P0 - Securite media reelle

1. Traiter les originaux existants depuis l'admin PhotoVault par lots de 25 ou via `wp photovault secure-originals`.
2. Verifier que le serveur web refuse directement `wp-content/photovault-private/` en Apache et Nginx.
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

1. Verifier `docker compose up --build` sur une machine avec reseau Docker disponible.
2. Finaliser les secrets de `.env` local hors Git.
3. Ajouter README principal production/dev.
4. Ajouter procedures de sauvegarde/restauration DB et medias.

### P3 - Qualite

1. Ajouter PHPCS/WordPress Coding Standards si Composer est introduit.
2. Ajouter tests unitaires/integration ciblant les regles critiques.
3. Transformer la matrice d'autorisation documentee en tests automatises.
4. Ajouter ADR supplementaires.

## Definition de progression

Estimation actuelle: 85%.

Cette estimation reflete que les fondations les plus importantes sont posees: modularisation, securite media applicative, verification email, demandes d'acces, audit, enrichissement public et depots plugins separes.

Elle ne signifie pas encore "production ready". Les deux gros blocs qui empechent ce label sont:

- verification serveur du stockage prive sur l'environnement cible;
- tests runtime automatises et verification complete avec WordPress actif.
