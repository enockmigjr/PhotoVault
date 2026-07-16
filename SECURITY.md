# Sécurité de la plateforme PhotoVault

## Objectif et limites

PhotoVault applique une stratégie de défense en profondeur : refus par défaut, moindre privilège, contrôles côté serveur, traçabilité et reprise après incident. Aucun site ne peut être déclaré « le plus sécurisé au monde » de manière sérieuse. La sécurité dépend aussi du serveur, des mises à jour, des secrets, des fournisseurs, des administrateurs et des tests continus.

L’objectif réaliste est un niveau de sécurité élevé, mesurable et maintenable. Une fonctionnalité n’est considérée protégée que si le contrôle est appliqué côté serveur et couvert par une preuve reproductible ; masquer un bouton ou bloquer le clic droit n’est jamais une barrière de sécurité.

## Architecture de confiance

- `identity-security-kit` gère l’identité, la vérification des coordonnées, les mots de passe, les facteurs multiples et l’audit de sécurité.
- `photovault-core` gère les médias, les capacités, les autorisations, les aperçus et la remise contrôlée des originaux.
- `newsletter-campaign-kit` gère le consentement, les listes, les campagnes, les désinscriptions et les événements fournisseur.
- Le thème présente l’interface. Il ne doit pas devenir l’autorité finale d’une décision sensible.
- Nginx interdit l’accès HTTP direct au stockage privé ; WordPress ne remet un original qu’après authentification et autorisation.

## Protections actives

### Identité et authentification

- Les formulaires publics valident et normalisent les entrées côté serveur.
- Les actions mutantes exigent un nonce WordPress et les capacités appropriées.
- Les réponses de récupération de mot de passe ne révèlent pas si un compte existe.
- Les liens de vérification et de réinitialisation sont temporaires, à usage unique et liés au compte concerné.
- La double authentification prend en charge TOTP, e-mail et SMS lorsque le fournisseur est configuré.
- Les secrets TOTP sont chiffrés au repos et les codes de récupération ne sont pas stockés en clair.
- Les mots de passe, OTP, secrets TOTP, clés de réinitialisation et jetons fournisseur ne sont jamais écrits dans les journaux.
- Les événements sensibles sont consignés dans `wp_identity_security_audit` avec des données minimisées.

### Autorisations et rôles

- Les décisions reposent sur les capacités WordPress, pas uniquement sur le nom d’un rôle.
- Les administrateurs et gestionnaires PhotoVault disposent des capacités métier nécessaires à l’assistance et à la modération.
- Les comptes ordinaires ne peuvent lire que leurs propres données, leurs autorisations et leurs collections accessibles.
- L’accès à l’administration WordPress reste limité aux comptes possédant une capacité d’administration native ; les appels AJAX légitimes ne sont pas bloqués.

### Médias et originaux

- Les listes chargent des miniatures `photovault-card` de 400 × 400 et la visionneuse charge un aperçu limité, jamais l’original 4K.
- L’original n’est transmis que par l’endpoint de téléchargement après contrôle de session, nonce, visibilité et autorisation.
- Les originaux protégés sont déplacés sous `wp-content/photovault-private/originals` et ne sont pas servis directement par Nginx.
- Les aperçus protégés reçoivent un filigrane côté serveur. Le blocage du clic droit améliore seulement l’expérience et ne remplace pas l’autorisation.
- Les consultations, refus, aperçus, téléchargements et attributions d’accès sont audités dans `wp_photovault_media_audit`.

### Newsletter et e-mails

- L’abonnement exige un consentement explicite et un double opt-in à durée limitée.
- Les réponses publiques restent neutres pour réduire l’énumération et le list bombing.
- La désinscription utilise un jeton opaque ; l’adresse e-mail n’apparaît pas dans l’URL.
- Les campagnes disposent d’un instantané immuable de leur audience et de clés d’idempotence.
- Les webhooks de bounce et de plainte exigent une signature HMAC horodatée et une protection contre le rejeu.
- Les adresses supprimées restent protégées par une empreinte de suppression sans conserver leur valeur en clair.

### Infrastructure et exploitation

- Les secrets sont injectés à l’exécution et exclus de Git et des images Docker.
- Le backend Docker écoute uniquement sur `127.0.0.1` derrière un reverse proxy TLS.
- Les ports publics, en-têtes HTTP, sauvegardes, restaurations, mises à jour et retours arrière sont documentés dans le guide DevOps canonique du dépôt WordPress principal.
- Les sauvegardes comprennent la base, les médias publics et privés, un manifeste et des sommes SHA-256 ; une sauvegarde n’est valide qu’après test de restauration isolé.
- Le déploiement vérifie la santé des services, WordPress, le thème et les trois plugins applicatifs.

## Comment viser un niveau de sécurité exceptionnel

1. Conserver WordPress, PHP, MariaDB, Nginx, le thème et les plugins à jour après validation en préproduction.
2. Utiliser SSH par clé, TLS moderne, pare-feu minimal, secrets uniques et rotation documentée.
3. Activer la MFA pour tous les comptes privilégiés et supprimer les comptes inutilisés.
4. Tester automatiquement les autorisations, CSRF, XSS, injections, traversées de chemin, téléchargements et élévations de privilèges.
5. Faire réaliser régulièrement un audit indépendant et corriger les constats selon leur criticité.
6. Superviser les erreurs, connexions administrateur, échecs MFA, refus d’accès, fournisseurs et sauvegardes depuis un service externe.
7. Préparer une procédure d’incident : isolement, conservation des preuves, rotation des secrets, restauration, notification et retour d’expérience.
8. Mesurer les objectifs de reprise : sauvegardes quotidiennes, test mensuel, RPO et RTO adaptés à la valeur des archives.

## Vérifications avant production

- [ ] HTTPS forcé, HSTS et en-têtes de sécurité contrôlés sur le domaine public.
- [ ] Debug désactivé et aucune erreur sensible exposée au navigateur.
- [ ] Comptes privilégiés protégés par MFA et principe du moindre privilège.
- [ ] Originaux privés inaccessibles par URL directe.
- [ ] Téléchargements autorisés testés pour propriétaire, bénéficiaire, visiteur et administrateur.
- [ ] SPF, DKIM et DMARC validés sur le domaine d’envoi.
- [ ] Twilio/Resend en mode live lorsque la réception réelle est requise.
- [ ] Sauvegarde créée, vérifiée, restaurée sur un environnement isolé et copiée hors site.
- [ ] Alertes externes et procédure d’incident opérationnelles.

## Règles de développement

- Ne jamais committer de secret, mot de passe, clé SMTP, jeton fournisseur ou export de données personnelles.
- Valider toutes les entrées et échapper chaque sortie selon son contexte.
- Utiliser `$wpdb->prepare()` pour toute requête SQL contenant une valeur variable.
- Protéger chaque mutation par nonce et capacité ; vérifier l’objet ciblé côté serveur.
- Préférer les API WordPress pour les sessions, utilisateurs, mots de passe, e-mails et fichiers.
- Ne pas affaiblir une autorisation pour résoudre un problème d’interface.
- Synchroniser les copies de distribution des plugins seulement après validation du dépôt source actif.

## Signalement d’une vulnérabilité

Une vulnérabilité doit être transmise en privé au propriétaire ou à l’équipe de développement avec les étapes de reproduction, l’impact et, si possible, une proposition de correction. Ne publiez pas de détail exploitable avant le déploiement du correctif et la rotation des secrets concernés.
