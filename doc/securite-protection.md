# Sécurité & Protection de la Propriété Intellectuelle — PhotoVault

Ce document décrit en détail les mécanismes de protection implémentés dans PhotoVault pour garantir la confidentialité et empêcher le vol d'images de vos photographes.

---

## 🔒 1. Protection Absolue des Images d'Origine (Proxy d'images)

Afin d'empêcher les utilisateurs avancés de récupérer l'image d'origine depuis l'inspecteur web en récupérant son URL directe, PhotoVault n'expose jamais les URLs réelles des fichiers physiques (ex: `wp-content/uploads/...`).

### Fonctionnement du Proxy REST
Toutes les balises d'images du thème (`img src`) pointent vers un point de terminaison de l'API REST personnalisée de WordPress :
`https://votre-site.com/wp-json/photovault/v1/secure-image?id=ID_DU_MEDIA`

Lorsqu'une image est demandée par le navigateur :
1. **Contrôle d'accès** : L'API REST valide que le visiteur effectuant la demande est connecté. Si ce n'est pas le cas, elle renvoie une erreur `401 Unauthorized` ou `403 Forbidden`.
2. **Détermination du statut** : Le script vérifie la métadonnée `is_protected` associée au média.
3. **Application du filigrane côté serveur** :
   - Si `is_protected` est actif, le serveur charge le fichier d'origine en mémoire via la bibliothèque graphique **GD** de PHP, dessine une grille répétée de filigranes `"PHOTOVAULT PROTECTED"` directement dans les pixels de l'image, puis sert l'image modifiée à la volée.
   - Si `is_protected` est inactif, l'image est servie normalement (après validation d'accès).
4. **Bénéfice** : L'image d'origine haute définition n'est **jamais** envoyée au client pour un média protégé. Si le visiteur effectue une capture réseau ou télécharge l'image, il n'obtiendra que la version déjà filigranée par le serveur.

---

## 📁 2. Isolation des Téléversements par Photographe

Pour éviter que les répertoires d'images ne soient mélangés, un filtre WordPress intercepte le dossier de téléversement (`upload_dir`) lors des actions du photographe. 

Les fichiers importés par un photographe sont stockés dans un répertoire dédié et confidentiel :
`wp-content/uploads/photographers/user_{ID_UTILISATEUR}/`

---

## 🛡️ 3. Protection Frontend (Anti-Copie)

Pour dissuader les visiteurs d'enregistrer l'image via des raccourcis simples, un script JS d'interception est injecté en bas de la page [single-media_item.php](file:///c:/xampp/htdocs/site-wordpress1/wp-content/themes/PhotoVault/single-media_item.php) si le média est protégé :
- **Clic droit bloqué** : Intercepte l'événement `contextmenu` et affiche un message d'alerte.
- **Raccourcis bloqués** : Désactive l'utilisation de `Ctrl+S` (Sauvegarde), `Ctrl+C` (Copie), `Ctrl+U` (Afficher le code source) et les touches d'ouverture des outils de développement comme `F12` ou `Ctrl+Shift+I`.
- **Drag & Drop désactivé** : Bloque le glisser-déposer de l'image vers le bureau ou un autre onglet.

---

## 👁️ 4. Restriction des Accès pour les Visiteurs Anonymes

Pour maximiser la sécurité de la plateforme, les visiteurs anonymes (déconnectés) n'ont accès qu'aux pages vitrines d'information et d'authentification :
- `front-page.php` (Landing page)
- `page-pricing.php` (Tarifs)
- `page-about.php` (À propos)
- `page-contact.php` (Contact)
- Formulaires de connexion / inscription

Toute tentative d'accès à la galerie publique (`/gallery/`), à une fiche de photo (`/gallery/titre-media/`), ou à une taxonomie de classement de photos (`/folder/` ou `/media-category/`) par un visiteur déconnecté déclenchera une **redirection immédiate** vers la page personnalisée de connexion (`/login/`).
