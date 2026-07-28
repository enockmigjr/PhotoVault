# Etat fonctionnel de PhotoVault

Mise a jour : 28 juillet 2026.

Ce document remplace l'ancienne liste de taches initiales. Les audits techniques detailles restent dans les autres documents du dossier `doc/`.

## Theme PhotoVault

- Pages publiques et tableau de bord harmonises.
- Galerie chargee avec des miniatures adaptees aux cartes.
- Acces aux originaux soumis aux droits et a la politique de telechargement.
- Visionneuse plein ecran avec navigation precedente/suivante.
- Filtres de galerie asynchrones reinitialises apres navigation interne.
- Formulaires proteges par nonce, validation serveur et retour utilisateur.
- Boutons de soumission avec etat de chargement visible.
- Profil, authentification multifacteur et parcours de reinitialisation integres au frontend.

## PhotoVault Core

- Medias publics/proteges, collections, demandes d'acces et imports admin.
- Miniatures, filigranes et politique de livraison des fichiers.
- Demandes de shooting et d'acces tracees.
- Les sources actives se trouvent dans `wp-content/plugins/photovault-core`; la copie du theme est un miroir de distribution.

## Identity Security Kit

- Profil, verification d'email, mot de passe, avatar et roles WordPress.
- MFA TOTP, email et SMS lorsque le fournisseur est configure.
- Journal de securite et protections des parcours sensibles.
- Les sources actives se trouvent dans `wp-content/plugins/identity-security-kit`.

## Newsletter Campaign Kit

- Double opt-in, preferences, listes, tags, segments et themes.
- Templates et blocs avec editeur visuel.
- Campagnes versionnees, revue finale, programmation, queue et retries.
- Rapports, suivi first-party, imports/exports et audit.
- Resend/Brevo/wp_mail/HTTP generique disponibles selon configuration.
- Le guide complet est dans `wp-content/plugins/newsletter-campaign-kit/GUIDE-ADMIN.md`.

## Validation et deploiement

- La pile Docker et les commandes d'exploitation sont documentees dans `doc/docker.md`.
- Les preuves externes et limites de validation sont dans `doc/validation-externe.md`.
- Les plugins actifs doivent etre synchronises vers `PhotoVault/plugins/` avant une livraison du theme.
- Les secrets restent dans la configuration serveur et ne sont jamais committes.

