<?php
/**
 * PhotoVault Theme Functions and Definitions
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Définir des constantes pour le thème.
define( 'PHOTOVAULT_VERSION', '1.0.0' );
define( 'PHOTOVAULT_DIR', get_template_directory() );
define( 'PHOTOVAULT_URI', get_template_directory_uri() );

// Chargement des modules de l'architecture.
$photovault_includes = array(
	'inc/theme-setup.php',    // Configuration de base (menus, scripts, pages automatiques)
	'inc/roles.php',          // Rôles utilisateurs & redirections
	'inc/post-types.php',     // Déclaration du CPT media_item
	'inc/taxonomies.php',     // Déclaration des taxonomies (folders, categories)
	'inc/auth-handlers.php',  // Connexion, Inscription, Réinitialisation MDP, Profil
	'inc/media-handlers.php', // Upload, Édition, Suppression de médias et sécurité
	'inc/ajax-filters.php',   // API REST et filtres de recherche
	'inc/helpers.php',        // Fonctions utilitaires, statistiques, badges de protection
);

foreach ( $photovault_includes as $file ) {
	$filepath = PHOTOVAULT_DIR . '/' . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}
