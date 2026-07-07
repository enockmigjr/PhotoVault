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

// Chargement des modules de presentation du theme.
$photovault_includes = array(
	'inc/theme-setup.php', // Configuration de base (menus, scripts, pages automatiques)
	'inc/helpers.php',     // Fonctions utilitaires de rendu du theme
);

// Fallback legacy : si le plugin PhotoVault Core n'est pas actif, le theme
// conserve les anciens modules applicatifs pour eviter une rupture immediate.
if ( ! defined( 'PHOTOVAULT_CORE_VERSION' ) ) {
	$photovault_includes = array_merge( $photovault_includes, array(
		'inc/roles.php',
		'inc/post-types.php',
		'inc/taxonomies.php',
		'inc/auth-handlers.php',
		'inc/media-handlers.php',
		'inc/ajax-filters.php',
	) );
}
foreach ( $photovault_includes as $file ) {
	$filepath = PHOTOVAULT_DIR . '/' . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}
