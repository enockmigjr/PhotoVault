# Recette externe PhotoVault

Derniere mise a jour: 2026-07-16

Ce guide couvre uniquement les preuves impossibles a produire avec des identifiants locaux factices. Le code, les diagnostics et les protections associes sont deja presents. Ne jamais inscrire une cle reelle dans Git, une capture ou un ticket.

## 1. Packaging des plugins - valide localement

Decision actuelle: les depots actifs sous `wp-content/plugins` sont les sources d'execution. Les copies sous `PhotoVault/plugins` restent des miroirs de distribution synchronises et ne sont jamais chargees par WordPress.

Critere d'acceptation:

1. Comparer chaque miroir a son depot actif avec `git diff --no-index -- plugins/<plugin> ../../plugins/<plugin>` depuis le theme.
2. Verifier dans `wp plugin list` qu'une seule occurrence de chaque plugin est active.
3. Pour une distribution Composer ou un deploiement par depots separes, exclure le dossier `PhotoVault/plugins` de l'artefact au lieu de supprimer les depots actifs.

Preuve: sortie des trois comparaisons sans difference et liste des plugins actifs.

## 2. SMS Brevo ou Twilio

Etat local au 2026-07-16: **recette staging validee**. Apres installation de la paire Test Account SID/Test Auth Token et du `From` magique, l'adapter Twilio a accepte le diagnostic. Un challenge OTP SMS complet a ensuite ete cree pour l'administrateur, accepte par Twilio, stocke uniquement avec hashes et expiration, audite puis neutralise. Les Test Credentials ne contactent pas les operateurs; la remise physique reste une preuve de production avec credentials live et numero Twilio SMS provisionne.

1. Choisir le provider dans `Identity Kit > Overview > SMS provider`.
2. Ajouter les constantes affichees sous `Show wp-config.php examples` avant la ligne de fin d'edition de `wp-config.php`, ou utiliser les memes variables d'environnement.
3. Recharger PHP si les variables viennent de l'environnement.
4. Verifier l'etat `Provider credentials detected`.
5. Dans `SMS provider test`, saisir un numero de recette E.164 puis envoyer une fois.
6. Confirmer la reception et consulter `Identity Kit > Security audit` pour l'evenement `sms_provider_test`.
7. Enroler ensuite le facteur SMS depuis le profil et effectuer une connexion MFA complete.

Critere: message recu, resultat accepte, numero masque dans l'audit, code OTP a usage unique et aucune valeur secrete en base ou dans les logs.

## 3. Newsletter Brevo ou Resend et DKIM

Etat local au 2026-07-16: **recette staging validee**. Resend confirme `delivered` pour le diagnostic `[PhotoVault] Delivery provider test`. Une campagne temporaire distincte a ensuite cree un abonne de test, traverse la queue et le rendu HTML/texte, ete acceptee par Resend avec statut `sent`, puis ses donnees temporaires ont ete nettoyees. L'expediteur persistant reste local; le domaine SPF/DKIM et une reception sur boite reelle restent des preuves de production.

1. Choisir le provider dans `Newsletter Kit > Settings`.
2. Ajouter la constante affichee dans `Server-side credentials` puis configurer une adresse `From` verifiee chez le provider.
3. Utiliser `Delivery provider test` avec une boite de recette.
4. Dans les en-tetes recus, verifier `spf=pass`, `dkim=pass` et `dmarc=pass`.
5. Sur une URL publique HTTPS, activer RFC 8058 seulement si la signature DKIM couvre aussi `List-Unsubscribe` et `List-Unsubscribe-Post`.
6. Creer une liste de recette, une campagne, figer l'audience, programmer, executer le cron et verifier le rapport final.

Critere: email HTML et texte recu, authentification du domaine valide, desinscription en un clic effective, queue terminee sans retry et evenement `newsletter_provider_test` present sans adresse brute.

## 4. WordPress multisite - non applicable a la livraison actuelle

La livraison actuelle reste mono-site. Cette validation redevient requise uniquement si le deploiement final active Multisite.

1. Activer les plugins site par site puis au niveau reseau sur une copie de recette.
2. Verifier que les tables utilisent le prefixe du site courant.
3. Tester un administrateur reseau, un administrateur de site et un membre sans capability.
4. Confirmer qu'aucun grant media, audit, abonne ou campagne ne traverse les sites.

Critere: isolation complete des donnees et des capabilities. Si Multisite n'est pas retenu, consigner la decision `non applicable` dans la fiche de mise en production.

## 5. Mesure ouverture et clic - decision validee

Decision actuelle: tracking desactive. Il n'est pas necessaire au fonctionnement de la newsletter.

Avant activation, documenter la finalite, la base legale, la duree de conservation, le mecanisme de consentement et le fournisseur analytics. Sans cette validation juridique et produit, conserver le tracking desactive.

Critere: decision signee `desactive` ou specification consentie avec recette Privacy et suppression.

## 6. Accessibilite assistee - acceptee

Prerequis automatise valide le 2026-07-16: home, galerie, connexion, dashboard et profil ne presentent aucune violation Axe serieuse ou critique pour WCAG 2.0/2.1 A/AA. Le reflow equivalent a un zoom 200 %, les dialogues ouverts, le piege et le retour du focus ainsi que la navigation clavier de la galerie sont egalement valides par Playwright. Le proprietaire accepte cette recette pour la livraison et realisera l'ecoute vocale NVDA ulterieurement.

1. Naviguer au clavier sur home, galerie, lightbox, connexion, profil, preferences et demandes d'acces.
2. Tester NVDA avec Firefox ou Chrome a 200 % de zoom.
3. Confirmer ordre de focus, nom accessible des boutons icones, annonces des erreurs/toasts, piege de focus des modales et retour du focus a la fermeture.
4. Verifier les contrastes avec axe ou Lighthouse, puis confirmer manuellement les cas signales.

Critere: aucun blocage clavier ou lecteur d'ecran de niveau A/AA sur les parcours critiques. Conserver le rapport et les captures des corrections.

## 7. PHPCS dans la CI - implemente

Le workflow `.github/workflows/phpcs.yml` installe WPCS 3.3 depuis `composer.lock`, analyse le theme et les trois miroirs, puis compare le rapport a `phpcs-baseline.json`. La dette historique est explicite; toute nouvelle violation par fichier et par sniff bloque la CI. La baseline locale couvre 127 fichiers et doit diminuer au fil des corrections, jamais augmenter sans revue.

Critere: job reproductible vert sur le commit livre, avec rapport conserve comme artefact.

## 8. Hebergement final

Prerequis local valide le 2026-07-16: images WordPress et cron reconstruites, cinq services Docker `healthy`, accueil et `/healthz` en HTTP 200, WordPress installe, theme PhotoVault et trois plugins actifs, cible `make verify` passee, evenement cron Newsletter execute, OTP SMS Twilio simule et campagne Resend mise en queue puis acceptee. L'activation commerciale future concernera uniquement l'URL publique, les providers live et son infrastructure reelle.

Decision de cloture du 2026-07-16: le proprietaire accepte ces preuves pour la presentation et differe les couts d'hebergement, de numero Twilio et de domaine expediteur. Le projet est donc termine dans son perimetre de presentation. Le transport transactionnel configurable, la reconnaissance HTTPS du reverse proxy, le Caddyfile, les cinq healthchecks, le cron, une sauvegarde avec checksums et sa restauration isolee sur 37 tables sont valides. Les etapes ci-dessous restent le gate obligatoire avant une ouverture commerciale publique.

Apres avoir installe les credentials live et configure l'URL finale, lancer depuis la racine:

    make production-preflight PUBLIC_URL=https://votre-domaine.example

Cette commande verifie l'etat Docker/applicatif, refuse le numero magique Twilio, les expediteurs Resend de test ou locaux et une home WordPress non HTTPS, puis controle sur la reponse publique HSTS, CSP, `X-Content-Type-Options` et `Referrer-Policy`. Elle qualifie une configuration candidate au live; elle ne remplace pas les preuves de reception et d'authentification des fournisseurs.

1. Forcer TLS et verifier HSTS, CSP, `X-Content-Type-Options`, politique de referrer et permissions des cookies.
2. Confirmer que le stockage prive et les originaux ne sont jamais servis directement par Nginx/Apache ou le CDN.
3. Valider cache des miniatures et absence de cache public sur previews protegees, profil et endpoints autorises.
4. Executer sauvegarde hors site puis restauration sur une instance vierge avec verification de checksums.
5. Tester rotation des cles SMS/newsletter sans indisponibilite prolongee.
6. Configurer alertes cron, queue, erreurs PHP, espace disque, sauvegardes et expiration TLS.

Critere: `production-preflight` vert, SMS OTP physiquement recu, message Resend avec SPF/DKIM/DMARC valides, puis rapport de recette signe avec URL, date, versions, resultats et plan de retour arriere.

## Fiche de cloture

| Validation | Statut | Date | Preuve / responsable |
|---|---|---|---|
| Packaging | Valide localement | 2026-07-16 | Miroirs identiques, plugins actifs uniques |
| SMS staging | Valide | 2026-07-16 | Test Credentials, adapter et challenge OTP acceptes; aucune remise operateur attendue |
| Newsletter staging | Valide | 2026-07-16 | Diagnostic `delivered` confirme par le proprietaire et campagne de queue acceptee par Resend |
| Multisite ou non applicable | Non applicable | 2026-07-16 | Livraison mono-site |
| Tracking desactive ou consenti | Desactive | 2026-07-16 | Decision secure-by-default |
| Accessibilite assistee | Validee par le proprietaire | 2026-07-16 | Axe, clavier, reflow et dialogues valides; NVDA differe |
| PHPCS CI | Implemente | 2026-07-16 | WPCS 3.3 + baseline anti-regression |
| Hebergement final | Accepte pour la presentation; activation commerciale differee | 2026-07-16 | Docker, healthchecks, WordPress, theme, plugins, cron, SMTP configurable et providers staging valides; runbook complet livre |
