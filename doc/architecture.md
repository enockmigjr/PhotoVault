# Architecture Technique - PhotoVault

> Suivi global: voir [tasks-objectif-initial.md](tasks-objectif-initial.md).

Ce document décrit la structure modulaire et l'organisation du code du thème WordPress professionnel **PhotoVault**.

---

## 📂 Organisation Modulaire du Code

Afin de respecter les standards de développement de thèmes et de maintenir une taille de fichier minimale (strictement inférieure à 200 lignes par fichier), toute la logique applicative a été extraite du fichier classique `functions.php` et structurée dans des modules indépendants sous `/inc/`.

```text
PhotoVault/
├── functions.php               # Point d'entrée, charge la logique de /inc/
└── inc/
    ├── theme-setup.php         # Init du thème, CSS/JS, création automatique des pages, nonce REST
    ├── roles.php               # Gestion du rôle Client, restrictions wp-admin et sécurité d'accès
    ├── post-types.php          # Déclaration du CPT media_item et de sa Metabox d'upload
    ├── taxonomies.php          # Déclaration de media_folder (dossiers) et media_category
    ├── auth-handlers.php       # Inscription (rôle client par défaut) et connexion sécurisée
    ├── media-handlers.php      # Traitement des uploads par l'admin et liaisons physiques
    ├── ajax-filters.php        # API REST pour les filtres et proxy d'images avec cookies de secours
    └── helpers.php             # Statistiques du dashboard et JS d'interception anti-téléchargement
```

---

## 🛠️ Description des Fichiers de Logique

### 1. [functions.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/functions.php)
Ce fichier se contente de définir les constantes globales de version et de chemin du thème, puis d'inclure séquentiellement tous les fichiers PHP du sous-dossier `/inc/`.

### 2. [theme-setup.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/theme-setup.php)
- Déclare le support du thème (`title-tag`, `post-thumbnails`, formats HTML5).
- Enregistre les menus (Menu Principal et Menu Dashboard).
- Charge la police Google Fonts (Outfit & Inter), Tailwind CSS v4 via CDN et le fichier de style personnalisé `css/main.css`.
- **Initialisation** : Crée automatiquement les pages requises (`Connexion`, `Inscription`, `Dashboard`, etc.) et leur assigne leur template correspondant lors de l'activation du thème.
- **Nonce REST** : Localise la variable globale `photovault_ajax` et son nonce REST API `wp_rest` sur le script JS principal.

### 3. [roles.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/roles.php)
- Déclare le rôle utilisateur personnalisé `client` (droits de lecture simple de la galerie).
- Bloque l'accès à l'administration WordPress (`/wp-admin/`) et redirige les non-administrateurs vers la page d'accueil du site.
- Restreint l'accès aux galeries et détails de médias aux seuls utilisateurs connectés. Les visiteurs anonymes sont automatiquement redirigés vers la page de connexion.

### 4. [post-types.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/post-types.php)
- Déclare le Custom Post Type `media_item` (slug publique `/gallery/`). Il active le support de l'API REST de WordPress (`show_in_rest => true`).
- Intègre une Metabox d'upload d'image principale centrale et ergonomique dans l'administration WordPress. Cette Metabox utilise le chargeur natif `wp.media` pour lier la photo sélectionnée au post thumbnail (`_thumbnail_id`) à l'enregistrement.
- Élimine le warning de fonction `strip_tags()` dépréciée pour la page des Réglages PhotoVault.

### 5. [taxonomies.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/taxonomies.php)
Déclare deux taxonomies hiérarchiques rattachées au CPT `media_item` :
- `media_folder` : pour la gestion des dossiers clients ou projets (Mariages, Clients, etc.).
- `media_category` : pour le classement thématique (Mode, Nature, Portrait, etc.).

### 6. [auth-handlers.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/auth-handlers.php)
Gère le traitement des requêtes POST pour :
- La connexion utilisateur personnalisée (`log`, `pwd`, `rememberme`).
- L'inscription client (crée l'utilisateur avec le rôle client et le connecte automatiquement).

### 7. [media-handlers.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/media-handlers.php)
- Offre la logique de support sous-jacente pour l'upload d'images et la liaison avec le post thumbnail.
- Valide la propriété et gère la suppression sécurisée des fichiers médias et de leurs pièces jointes associées.

### 8. [ajax-filters.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/ajax-filters.php)
- Déclare l'endpoint REST de filtrage `/wp-json/photovault/v1/media` combinant recherche et taxonomies. Authentifié via nonce REST API.
- Déclare l'endpoint de proxy d'images `/wp-json/photovault/v1/secure-image` pour appliquer le filigrane côté serveur, avec un mécanisme de cookies de secours pour assurer une compatibilité parfaite avec les balises `<img>` standard du navigateur.
