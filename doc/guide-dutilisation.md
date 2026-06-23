# Guide d'Utilisation & Validation — PhotoVault

Ce document vous guide pas à pas pour installer, configurer et tester l'ensemble des fonctionnalités du thème **PhotoVault**.

---

## ⚙️ 1. Activation et Configuration Initiale

1. **Installer le thème** : Copiez le dossier `PhotoVault` dans votre répertoire `wp-content/themes/`.
2. **Activer le thème** : Connectez-vous à votre administration WordPress en tant qu'administrateur, accédez à *Apparence > Thèmes*, puis cliquez sur **Activer** sous PhotoVault.
3. **Création automatique des pages** : Lors de l'activation, le thème va vérifier et insérer automatiquement les pages indispensables dans votre espace WordPress :
   - `Connexion` (slug : `/login/`, assigné au template `page-login.php`)
   - `Inscription` (slug : `/register/`, assigné au template `page-register.php`)
   - `Mon Profil` (slug : `/profile/`, assigné au template `page-profile.php`)
   - `Dashboard` (slug : `/dashboard/`, assigné au template `page-dashboard.php`)
   - `Mes Médias` (slug : `/my-media/`, assigné au template `page-my-media.php`)
   - `Ajouter Média` (slug : `/upload-media/`, assigné au template `page-upload-media.php`)
   - `Tarifs` (slug : `/pricing/`)
   - `À Propos` (slug : `/about/`)
   - `Contact` (slug : `/contact/`)
   - `Mot de passe oublié` (slug : `/forgot-password/`)

---

## 👥 2. Rôles et Permissions à Tester

### Rôle 1 : Administrateur (Admin WP)
- A accès à tout (l'administration `/wp-admin/`, les paramètres généraux).
- Peut modérer tous les médias importés par les photographes.

### Rôle 2 : Photographe (SaaS User)
- **Comment tester** : Accédez à la page d'inscription `/register/` pour créer un compte de photographe.
- **Vérification d'accès** : Une fois connecté, tentez d'accéder à `/wp-admin/` dans votre navigateur. Vous devez être automatiquement redirigé vers votre espace `/dashboard/` en frontend.
- **Fonctionnalités** :
  - Accédez au tableau de bord avec vos statistiques mises à jour à chaque import.
  - Utilisez `/upload-media/` pour téléverser une ou plusieurs images, configurez la visibilité (publique/privée) et la protection 🔒.
  - Gérez vos images importées depuis `/my-media/` (avec option de suppression).
  - Modifiez vos informations de compte (nom, bio, mot de passe, avatar de profil) sur `/profile/`.

### Rôle 3 : Client / Visiteur connecté (Simple User)
- **Comment tester** : Créez un utilisateur standard WordPress avec le rôle `client` (ou `subscriber` pour les abonnés standard) depuis le panneau d'administration.
- **Fonctionnalités** :
  - Ce rôle permet de visiter la galerie publique et de consulter le détail des images.
  - Il n'a aucun accès aux pages du dashboard photographe (`/dashboard/`, `/my-media/`, `/upload-media/`).
  - S'il tente d'y accéder, il est redirigé vers l'accueil.

### Visiteur Anonyme (Déconnecté)
- **Vérification d'accès** : Déconnectez-vous et tentez d'ouvrir `/gallery/` ou la fiche d'une photo. Vous devez être redirigé automatiquement vers `/login/`.
- Les seules pages accessibles sans compte sont : la Landing Page d'accueil, la page des Tarifs, la page À Propos, le formulaire de Contact, et les formulaires de connexion et d'inscription.

---

## 🔒 3. Validation de la Protection Anti-Vol d'Images

1. Connectez-vous en tant que photographe et importez une photo en cochant la case **"🔒 Protéger ce média"**.
2. Connectez-vous en tant que **Client** (ou simple visiteur connecté) et accédez à cette image dans la galerie publique.
3. Tentez d'effectuer un clic droit sur l'image : un message d'alerte s'affiche et l'action est bloquée.
4. Tentez de glisser l'image vers votre bureau : le glissement est inactif.
5. Ouvrez l'inspecteur d'éléments pour localiser l'image. Vous constaterez que son URL source pointe vers la route sécurisée de l'API REST de PhotoVault.
6. Si vous copiez et ouvrez cette URL REST sécurisée dans un nouvel onglet, vous obtiendrez l'image **avec un filigrane de sécurité PHOTOVAULT incrusté directement dans ses pixels**, car le traitement a été fait par le serveur en PHP GD. L'originale reste introuvable.
