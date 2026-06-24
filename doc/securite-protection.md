# Sécurité & Protection de la Propriété Intellectuelle — PhotoVault

Ce document décrit en détail les mécanismes de protection implémentés dans PhotoVault pour garantir la confidentialité et empêcher le vol d'images de votre galerie.

---

## 🔒 1. Protection Absolue des Images d'Origine (Proxy d'images)

Afin d'empêcher les utilisateurs avancés de récupérer l'image d'origine depuis l'inspecteur web en récupérant son URL directe, PhotoVault n'expose jamais les URLs réelles des fichiers physiques (ex: `wp-content/uploads/...`).

### Fonctionnement du Proxy REST
Toutes les balises d'images du thème (`img src`) pointent vers un point de terminaison de l'API REST personnalisée de WordPress :
`https://votre-site.com/wp-json/photovault/v1/secure-image?id=ID_DU_MEDIA`

Lorsqu'une image est demandée par le navigateur :
1. **Contrôle d'accès par cookie** : Le endpoint REST valide la session de l'utilisateur connecté de manière sécurisée en lisant son cookie de session WordPress classique (via `wp_validate_auth_cookie()`). Cela évite les erreurs d'autorisation REST (401/403) sans compromettre la sécurité.
2. **Détermination du statut** : Le script vérifie la métadonnée `is_protected` associée au média.
3. **Application du filigrane côté serveur** :
   - Si `is_protected` est actif et que l'utilisateur n'est pas l'administrateur, le serveur charge le fichier d'origine en mémoire via la bibliothèque graphique **GD** de PHP, dessine une grille répétée de filigranes (texte configurable dans "Réglages PhotoVault" de wp-admin) directement dans les pixels de l'image, puis sert l'image modifiée à la volée.
   - Si `is_protected` est inactif, l'image est servie normalement (après validation d'accès).
4. **Bénéfice** : L'image d'origine haute définition n'est **jamais** envoyée au client pour un média protégé. Si le visiteur effectue une capture réseau ou télécharge l'image, il n'obtiendra que la version déjà filigranée par le serveur.

---

## 📁 2. Restriction d'Accès aux Médias Privés

Si un média est configuré en statut "Privé" dans WordPress, seul l'administrateur propriétaire (ou l'auteur du post) peut y accéder. Toute tentative d'accès via le frontend ou via l'API REST par un simple client ou un visiteur anonyme se soldera par une erreur `403 Forbidden` ou une redirection stricte.

---

## 🛡️ 3. Protection Frontend (Anti-Copie)

Pour dissuader les visiteurs d'enregistrer l'image via des raccourcis simples, un script JS d'interception est injecté au pied de la page [single-media_item.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/single-media_item.php) si le média est protégé :
- **Clic droit bloqué** : Intercepte l'événement `contextmenu` et affiche un message d'alerte.
- **Raccourcis bloqués** : Désactive l'utilisation de `Ctrl+S` (Sauvegarde), `Ctrl+C` (Copie), `Ctrl+U` (Afficher le code source) et les touches d'ouverture des outils de développement comme `F12` ou `Ctrl+Shift+I`.
- **Drag & Drop désactivé** : Bloque le glisser-déposer de l'image vers le bureau ou un autre onglet.

---

## 👁️ 4. Restriction des Accès pour les Visiteurs Anonymes

Les visiteurs anonymes (déconnectés) n'ont accès qu'aux pages vitrines d'information et d'authentification :
- `front-page.php` (Landing page d'accueil)
- `page-pricing.php` (Prestations)
- `page-about.php` (À propos)
- `page-contact.php` (Contact)
- Formulaires de connexion / inscription

Toute tentative d'accès à la galerie publique (`/gallery/`), à une fiche de photo (`/gallery/titre-media/`), ou à une taxonomie de classement de photos (`/folder/` ou `/media-category/`) par un visiteur déconnecté déclenchera une **redirection immédiate** vers la page personnalisée de connexion (`/login/`).
