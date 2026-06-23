# Architecture Technique — PhotoVault

Ce document décrit la structure modulaire et l'organisation du code du thème WordPress professionnel **PhotoVault**.

---

## 📂 Organisation Modulaire du Code

Afin de respecter les standards modernes de développement de thèmes et de maintenir une taille de fichier minimale (strictement inférieure à 200 lignes par fichier), toute la logique applicative a été extraite du fichier classique `functions.php` et structurée dans des modules indépendants sous `/inc/`.

```text
PhotoVault/
├── functions.php               # Point d'entrée, charge la logique de /inc/
└── inc/
    ├── theme-setup.php         # Init du thème, CSS/JS, création automatique des pages
    ├── roles.php               # Gestion des rôles Photographe/Client et sécurité d'accès
    ├── post-types.php          # Déclaration du CPT media_item
    ├── taxonomies.php          # Déclaration de media_folder (dossiers) et media_category
    ├── auth-handlers.php       # Inscription, connexion et mise à jour de profil
    ├── media-handlers.php      # Traitement des uploads, ownership et suppression
    ├── ajax-filters.php        # API REST pour les filtres et proxy d'images sécurisé
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
- **Automatisation SaaS** : Crée automatiquement les pages requises (`Connexion`, `Inscription`, `Dashboard`, `Profil`, etc.) et leur assigne leur template correspondant lors de l'activation du thème.

### 3. [roles.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/roles.php)
- Déclare les rôles utilisateur personnalisés : `photographer` (droits de création et gestion de médias) et `client` (droits de lecture simple).
- Bloque l'accès à l'administration WordPress (`/wp-admin/`) et redirige les photographes vers leur dashboard et les clients vers l'accueil.
- Restreint l'accès aux galeries et détails de médias aux seuls utilisateurs connectés. Les visiteurs anonymes sont automatiquement redirigés vers la page de connexion.

### 4. [post-types.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/post-types.php)
Déclare le Custom Post Type `media_item` (slug publique `/gallery/`). Il active le support de l'API REST de WordPress (`show_in_rest => true`) et map les droits d'édition de manière personnalisée pour correspondre aux capacités de gestion de PhotoVault.

### 5. [taxonomies.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/taxonomies.php)
Déclare deux taxonomies hiérarchiques rattachées au CPT `media_item` :
- `media_folder` : pour la gestion des dossiers clients ou projets (Mariages, Clients, etc.).
- `media_category` : pour le classement thématique (Mode, Nature, Portrait, etc.).

### 6. [auth-handlers.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/auth-handlers.php)
Gère le traitement des requêtes POST pour :
- La connexion utilisateur personnalisée (`log`, `pwd`, `rememberme`).
- L'inscription du photographe (crée l'utilisateur avec le rôle photographe et le connecte automatiquement).
- La modification du profil (met à jour le nom, la bio, le mot de passe et stocke l'ID de l'image d'avatar dans les métadonnées de l'utilisateur).

### 7. [media-handlers.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/media-handlers.php)
- Redirige le stockage physique des images des photographes dans un répertoire isolé : `wp-content/uploads/photographers/user_{ID}/`.
- Gère l'upload simple et multiple en rattachant le fichier comme image à la une du post `media_item` et en enregistrant la valeur de protection (`is_protected`).
- Gère la suppression en vérifiant strictement que l'utilisateur qui fait la demande est le propriétaire du média (sécurité anti-bypass).

### 8. [ajax-filters.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/ajax-filters.php)
- Déclare l'endpoint REST de filtrage `/wp-json/photovault/v1/media` combinant recherche et taxonomies.
- Déclare l'endpoint de proxy d'images `/wp-json/photovault/v1/secure-image` pour appliquer le filigrane côté serveur.

### 9. [helpers.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/inc/helpers.php)
Fournit les compteurs de statistiques et injecte les scripts JS anti-téléchargement au pied des pages.
