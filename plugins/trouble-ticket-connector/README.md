# Trouble Ticket Connector

Connecteur WordPress autonome vers le portail public Telecom ITSM. Il injecte le widget et, pour un membre PhotoVault
connecté dont l’email est vérifié par Identity Security Kit, produit une assertion HS256 à usage unique. Aucun ticket,
commentaire, fichier ou historique n’est stocké dans WordPress.

## Prérequis

- WordPress avec HTTPS en production et un portail support sur une origine distincte ;
- Identity Security Kit actif pour la connexion sans nouvel OTP ;
- intégration `ACTIVE` créée côté backend avec l’origine exacte du site ;
- même secret de 32 à 256 caractères configuré dans les deux systèmes ;
- domaine du portail autorisé par la CSP du site dans `script-src`, `frame-src` et `connect-src`.

## Installation

1. Installer ce dossier dans `wp-content/plugins/trouble-ticket-connector` et activer le plugin.
2. Dans **Réglages > Support client**, saisir l’URL du portail, sa clé publique, l’UUID d’intégration, l’audience et
   l’origine exacte du site.
3. Définir de préférence `TROUBLE_TICKET_CONNECTOR_SECRET` dans `wp-config.php` ou l’environnement. Le formulaire
   admin constitue un repli chiffré avec Sodium ou AES-256-GCM.
4. Choisir l’injection automatique ou placer `[trouble_ticket_support]` sur les pages voulues.
5. Tester la connexion publique. Ce test ne transmet jamais le secret.

Le chargeur épinglé est `/widget/v2/widget.js`, avec l’intégrité
`sha384-63HovOBBVveI4gTSPtMyLyAmo64gADN020jaBKjd1vzmlRh3aVqe3+HX/nJ2eHq7`.

## Rotation et révocation

Créer d’abord un nouveau credential côté backend, puis enregistrer la même valeur ici. Le backend conserve
temporairement l’ancien credential pendant la grâce. Le bouton de révocation locale supprime uniquement l’option
chiffrée ; une constante serveur doit être retirée de la configuration, et le credential doit toujours être révoqué
côté plateforme.

## Sécurité et comportement dégradé

- l’endpoint `POST /wp-json/trouble-ticket/v1/assertion` exige session WordPress et `X-WP-Nonce` ;
- l’assertion expire après 120 secondes et contient un `jti` aléatoire consommé par Redis côté backend ;
- le sujet est opaque, stable par utilisateur/intégration et ne révèle pas l’ID WordPress ;
- le téléphone n’est inclus que si la preuve est liée au numéro canonique courant ;
- si Identity Security Kit ou l’assertion échoue, le widget garde son parcours public par email ; la CSP doit autoriser
  explicitement le domaine support pour que le chargeur puisse apparaître ;
- la désactivation ne modifie ni le thème ni les trois autres plugins. La désinstallation retire les options et la
  capability, mais conserve volontairement les sujets opaques afin qu’une réinstallation retrouve les tickets historiques.

## Validation runtime

Depuis WP-CLI :

```sh
wp eval-file wp-content/plugins/trouble-ticket-connector/tests/runtime-connector.php
```

Recette navigateur PhotoVault (depuis le thème, qui contient Playwright) :

```sh
$env:PHOTOVAULT_TEST_BASE_URL = "http://localhost:8080"
$env:PHOTOVAULT_TEST_USERNAME = "membre-verifie"
$env:PHOTOVAULT_TEST_PASSWORD = "mot-de-passe"
node ..\..\plugins\trouble-ticket-connector\tests\browser-connector.js
```

Elle vérifie que le widget s'ouvre pour un visiteur anonyme (parcours email) et qu'un membre PhotoVault dont l'email
est vérifié accède directement au widget sans nouvel OTP. Le moteur est sélectionnable avec
`PHOTOVAULT_TEST_BROWSER` (`chromium`, `firefox` ou `webkit`) ; les trois moteurs sont validés.
