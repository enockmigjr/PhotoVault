# Progression plateforme PhotoVault

Derniere mise a jour: 2026-07-15

## Objectif initial

Checklist detaillee: [tasks-objectif-initial.md](tasks-objectif-initial.md).
Inventaire REST/AJAX: [rest-ajax-inventory.md](rest-ajax-inventory.md).
Matrice capabilities: [capabilities-matrix.md](capabilities-matrix.md).
Surfaces plugins: [plugin-surfaces.md](plugin-surfaces.md).
Threat models: [media](threat-model-media.md), [identity](threat-model-identity.md), [newsletter](threat-model-newsletter.md).

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
|   |-- base admin newsletter
|   |-- audit newsletter
|   `-- brouillons et transitions campagnes
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
- Les previews filigranees sont mises en cache fichier pour eviter un recalcul GD a chaque requete.
- Le cache des previews protegees inclut l identifiant et la date de modification du fichier de filigrane personnalise.
- Les reglages admin permettent maintenant de borner texte, image personnalisee, opacite, densite et qualite JPEG du filigrane.
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
- OTP email generique ajoute avec expiration, essais bornes, anti-rejeu, cooldown, shortcode et hooks publics.
- Telephone international E.164 ajoute avec prefixe obligatoire, unicite serveur et connexion par telephone desactivee par defaut.
- TOTP RFC 6238 ajoute avec secrets chiffres, anti-rejeu, challenge de connexion natif/PhotoVault et blocage XML-RPC par mot de passe.
- Recovery codes a usage unique ajoutes avec 80 bits aleatoires, stockage hashe et affichage unique.
- Grace MFA configurable ajoutee pour les comptes a capabilities sensibles, avec rappels J+1/J+7/J+12, reconciliation des roles/politiques, blocage wp-admin/AJAX apres 15 jours et invalidation des autres sessions.
- Test runtime Identity ajoute sur WordPress/MySQL: verification email liee a l'adresse, OTP email/SMS, telephone E.164 unique, TOTP, recovery, login multicanal et expiration J+15.
- Les emails Identity sont maintenant verifies via le transport Docker/Mailpit; une erreur fatale des templates verification/reset a ete corrigee.
- Le profil conserve l'email courant jusqu'a confirmation de la nouvelle adresse; demande chiffree, expiration, anti-rejeu, notifications et revocation MFA email sont valides en runtime.
- Les flux email Identity et les notices natives email/mot de passe partagent maintenant un template professionnel responsive, une version texte PHPMailer, des CTA/OTP semantiques et un branding filtrable.

### Newsletter

- Kit generique separe.
- Abonnement avec consentement et nonce.
- Stockage des metadonnees sensibles sous forme hashee lorsque pertinent.
- Desinscription via token serveur.
- Premiere interface admin abonnes.
- Audit newsletter ajoute pour inscriptions, desinscriptions, changements de statut, exports, listes et tags.
- Base campagnes ajoutee avec brouillons, cible liste, statuts serveur et transitions controlees par capabilities.
- Queue newsletter ajoutee avec table dediee, enqueue sur transition sending, traitement batch manuel, retry/backoff et filtre provider.
- Provider wp_mail ajoute avec reglages from name/from email, page Settings et filtre pour adaptateur externe.
- Reporting campagne ajoute depuis la queue: totaux par campagne, sent, failed, pending et taux de livraison simple.
- Programmation timezone-aware et WP-Cron minute valides en runtime avec verrouillage atomique, reprise stale, finalisation et absence de duplication.
- Segments dynamiques all/any ajoutes sur listes, tags, source et anciennete, avec thematiques de campagne, affectations admin et preuve runtime des destinataires.
- One-click unsubscribe RFC 8058 ajoute avec POST idempotent, URL HTTPS, confirmation DKIM explicite et en-tetes verifies dans Mailpit.
- Les jetons de desinscription tournent a la reactivation; un contact `suppressed` ne peut plus etre reactive publiquement et reste annule au dernier controle avant envoi.
- Les templates editoriaux reutilisables disposent de creation, edition, duplication, archivage/restauration et heritage par campagne.
- La preview admin HTML/texte exige capability et nonce, applique une CSP restrictive et les emails sont remis en multipart avec `AltBody` valide dans PHPMailer/Mailpit.

### Experience publique

- Home enrichie avec hero, manifeste, oeuvres, collections, services, processus, journal, FAQ et CTA.
- Blog reutilise pour les carnets visuels lorsque disponible.
- Illustration fallback lorsque l'image de blog manque.
- Footer plus complet.
- Page detail media avec affichage protege, filigrane, metadata, download controle et medias similaires.
- Dashboard role-aware avec overview, favoris persistants, historique des telechargements, demandes/grants, etats Identity et abonnement Newsletter.
- Page profil provisionnee automatiquement sur les installations deja actives; navigation mobile du dashboard fermable par overlay et touche Echap.
- Reservations de shootings avec formulaire authentifie, ownership, date/type/contact valides, cycle `pending/confirmed/cancelled/completed`, administration et e-mails multipart prouves dans Mailpit.

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
- Les tables, options, filtres et actions publiques des trois plugins sont inventories.
- README principal et README par plugin ajoutes pour faciliter la reprise projet.
- Threat models media, identity et newsletter ajoutes avec actifs, menaces, controles, gaps et tests minimum.
## Diagnostic actuel

### Critique

- Aucun blocage d'installation Docker n'est encore ouvert: WordPress, migrations, plugins, cron et transport email sont operationnels. Les blockers restants concernent surtout les tests d'autorisation et les fonctions metier inachevees.

### Eleve

- Les originaux sensibles disposent maintenant d'une premiere migration vers stockage prive, mais les existants doivent etre traites en admin et la regle serveur Nginx/Apache doit etre verifiee en production.
- L'inventaire REST/AJAX est documente et la matrice media Core est automatisee; Identity, Newsletter, admin-post et CSRF HTTP restent a couvrir.
- Le workflow upload admin offre maintenant selection visible, progression XHR, statuts et edition rapide; la preuve multipart HTTP post-correction et la validation navigateur restent a rejouer.
- Identity Kit limite maintenant login, inscription, reset password et renvoi de verification avec des seuils admin bornes.
- Le noyau TOTP/recovery/grace et les MFA email/SMS sont valides par services dans WordPress; les parcours navigateur, le SMS reel et la migration des comptes existants restent a valider.
- La newsletter dispose maintenant de preferences thematiques, suppression-list durable, outils Privacy et templates multipart; provider API externe, imports et tracking ouvertures/clics restent absents.

### Moyen

- Le header mobile et le dashboard utilisateur doivent encore etre valides visuellement sur petits ecrans; le rendu PHP et l'isolation des roles sont testes.
- La pagination blog et les etats loading doivent etre verifies sur donnees reelles.
- L'audit media n'a pas encore de retention/export.
- Les README existent; ils devront etre enrichis avec exemples d'integration apres validation runtime.

### Faible

- Harmonisation finale des textes admin et i18n.
- ADR supplementaires a rediger pour media proteges, identity policy, newsletter queue et Docker.

## Backlog priorise

### P0 - Securite media reelle

1. Traiter les originaux existants depuis l'admin PhotoVault par lots de 25 ou via `wp photovault secure-originals`.
2. Verifier que le serveur web refuse directement `wp-content/photovault-private/` en Apache et Nginx.
3. Etendre les tests d'ID guessing aux acces directs serveur et aux variantes de cache; REST Core est valide.

### P1 - Verification runtime

1. Etendre les roles deja testes sur Core aux routes Identity et Newsletter.
2. Ajouter la matrice admin-post, CSRF et privilege escalation HTTP.
3. Valider les parcours HTTP navigateur register, verification email, login MFA et recuperation.

### P1 - Newsletter Kit

- Newsletter Kit dispose maintenant de listes, tags, tables de liaison, page admin de segmentation, page audit, base campagnes, queue batch, provider wp_mail et reporting de livraison.
- Les nouveaux abonnements publics sont rattaches a une liste editoriale par defaut.

### P1 - Identity Kit

1. Valider un provider SMS reel en staging sans exposer ses secrets.
2. Ajouter QR TOTP et parcours guide de remplacement des facteurs; la desactivation securisee est validee en runtime.
3. Tester en navigateur le challenge login et l'enforcement wp-admin/AJAX par role.
4. Couvrir le multisite et les modifications directes des capabilities d'un role.
5. Ajouter page admin Security Audit/Policies complete.

### P1 - Newsletter Kit

1. Ajouter blocs editoriaux reutilisables et snapshots d'audience immuables.
2. Brancher un provider API externe dedie et ajouter supervision/alertes du cron de traitement.
3. Ajouter webhooks signes pour bounces/complaints et brancher le registre de suppression au futur import.
4. Ajouter tracking ouvertures/clics et exports de reporting avances.

### P2 - UX metier PhotoVault

1. Rejouer l'upload multipart HTTP corrige et valider l'espace d'import admin en navigateur.
2. Verifier responsive mobile et accessibilite clavier du dashboard et du formulaire de reservation.

### P2 - Exploitation

1. Externaliser/chiffrer les snapshots et definir retention/alertes sur l'hebergeur final.
2. Documenter rotation des secrets et strategie d'image immutable de production.
3. Ajouter un smoke test Docker automatise en CI.

### P3 - Qualite

1. Ajouter PHPCS/WordPress Coding Standards si Composer est introduit.
2. Ajouter tests unitaires/integration ciblant les regles critiques.
3. Etendre la matrice d'autorisation automatisee Core aux deux plugins independants.
4. Ajouter ADR supplementaires si les decisions runtime changent.

## Definition de progression

Estimation recalculee: 70% d implementation fonctionnelle et 60% de preparation production stricte.

L'ancienne checklist agregeait trop de sous-exigences et comptait la documentation comme une fonctionnalite terminee. La matrice de reference est maintenant [progression-objectif-initial-v2.md](progression-objectif-initial-v2.md): elle couvre les 113 sections techniques du cahier initial et distingue implementation et preuve de production.

Les fondations les plus importantes sont posees: modularisation, securite media applicative, verification email, demandes d'acces, audit media/identite/newsletter, base campagnes, queue newsletter, provider et reporting, enrichissement public et depots plugins separes.

Elle ne signifie pas encore "production ready". Les blocs qui empechent ce label sont:

- verification serveur du stockage prive sur l'environnement cible;
- externalisation chiffree des backups, retention et smoke tests automatises Docker;
- matrices Identity/Newsletter/admin-post, CSRF HTTP et acces directs aux fichiers;
- validation Identity restante: provider SMS reel, remplacement guide des facteurs et tests navigateur;
- UX metier incomplete: preuve multipart post-correction et validation responsive de l'import, du dashboard et des reservations;
- Newsletter avancee: lifecycle segments/campagnes valide; restent imports, snapshots d'audience, webhooks provider, tracking ouvertures/clics et exports.
