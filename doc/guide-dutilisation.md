# Guide d'Utilisation & Validation — PhotoVault

Ce document vous guide pas à pas pour installer, configurer et tester l'ensemble des fonctionnalités du thème **PhotoVault**.

---

## ⚙️ 1. Activation et Configuration Initiale

1. **Installer le thème** : Copiez le dossier `PhotoVault` dans votre répertoire `wp-content/themes/`.
2. **Activer le thème** : Connectez-vous à votre administration WordPress en tant qu'administrateur, accédez à *Apparence > Thèmes*, puis cliquez sur **Activer** sous PhotoVault.
3. **Création automatique des pages** : Lors de l'activation, le thème va vérifier et insérer automatiquement les pages indispensables dans votre espace WordPress :
   - `Connexion` (slug : `/login/`, assigné au template `page-login.php`)
   - `Inscription` (slug : `/register/`, assigné au template `page-register.php`)
   - `Dashboard` (slug : `/dashboard/`, assigné au template `page-dashboard.php` - Réservé à l'administrateur propriétaire)
   - `Tarifs` (slug : `/pricing/`)
   - `À Propos` (slug : `/about/`)
   - `Contact` (slug : `/contact/`)
   - `Mot de passe oublié` (slug : `/forgot-password/`)

---

## 👥 2. Rôles et Permissions à Tester

### Rôle 1 : Administrateur / Propriétaire (Admin WP)
- A accès à tout (l'administration `/wp-admin/`, les paramètres généraux, la création de dossiers et catégories).
- **Import de photos** : S'effectue exclusivement dans le back-office WordPress sous le menu **PhotoVault > Ajouter Nouveau**.
- Dispose d'une **Metabox centrale intuitive** "Fichier Média (Image)" dans l'éditeur de média pour téléverser et lier directement ses photos.
- Peut consulter le **Dashboard analytique global** (`/dashboard/`) sur le frontend avec toutes les statistiques consolidées (total des médias, vues et téléchargements).

### Rôle 2 : Client / Visiteur connecté (Simple User)
- **Comment tester** : Accédez à la page d'inscription `/register/` pour créer un compte de client.
- **Vérification d'accès** : Tentez d'accéder à `/wp-admin/` dans votre navigateur. Vous devez être automatiquement redirigé vers la page d'accueil.
- **Fonctionnalités** :
  - Ce rôle permet de visiter la galerie publique et de consulter le détail des images.
  - S'il dispose de médias privés qui lui sont attribués (par dossier/projet), il peut les visionner de manière sécurisée.
  - Il n'a aucun droit d'import (l'import frontend est désactivé).

### Visiteur Anonyme (Déconnecté)
- **Vérification d'accès** : Déconnectez-vous et tentez d'ouvrir `/gallery/` (l'archive des médias) ou la fiche d'une photo. Vous devez être redirigé automatiquement vers la page de connexion `/login/`.
- Les seules pages accessibles sans compte sont : la Landing Page d'accueil, la page des Tarifs, la page À Propos, le formulaire de Contact, et les formulaires de connexion et d'inscription.

---

## 🔒 3. Validation de la Protection de Propriété Intellectuelle

1. Connectez-vous en tant qu'administrateur dans le back-office, créez un nouveau média dans le menu **PhotoVault** et téléversez une photo en cochant la case **"🔒 Activer la protection"**.
2. Connectez-vous en tant que **Client** et accédez à cette image dans la galerie publique.
3. Tentez d'effectuer un clic droit sur l'image : un message d'alerte s'affiche et l'action est bloquée.
4. Tentez de glisser l'image vers votre bureau : le glissement est inactif.
5. Ouvrez l'inspecteur d'éléments pour localiser l'image. Vous constaterez que son URL source pointe vers la route sécurisée de l'API REST de PhotoVault.
6. Si vous copiez et ouvrez cette URL REST sécurisée dans un nouvel onglet, vous obtiendrez l'image **avec un filigrane de sécurité PHOTOVAULT incrusté directement dans ses pixels**, car le traitement a été fait par le serveur en PHP GD. L'originale reste introuvable.
7. Sur la page de détail, le bouton de téléchargement de l'image est masqué pour l'utilisateur standard, empêchant tout vol d'image.
