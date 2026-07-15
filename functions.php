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
define( 'PHOTOVAULT_VERSION', '1.2.0' );
define( 'PHOTOVAULT_DIR', get_template_directory() );
define( 'PHOTOVAULT_URI', get_template_directory_uri() );

// Chargement des modules de presentation du theme.
$photovault_includes = array(
	'inc/theme-setup.php', // Configuration de base (menus, scripts, pages automatiques)
);

if ( defined( 'PHOTOVAULT_CORE_VERSION' ) ) {
	$photovault_includes[] = 'inc/presentation-helpers.php';
} else {
	// Fallback legacy : si le plugin PhotoVault Core n'est pas actif, le theme
	// conserve les anciens modules applicatifs pour eviter une rupture immediate.
	$photovault_includes = array_merge( $photovault_includes, array(
		'inc/helpers.php',
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
/**
 * Require Identity Kit MFA for PhotoVault operational roles.
 *
 * @param string[] $capabilities Generic protected capabilities.
 * @return string[]
 */
function photovault_identity_mfa_required_capabilities( $capabilities ) {
	return array_values(
		array_unique(
			array_merge(
				(array) $capabilities,
				array( 'photovault_manage_platform', 'photovault_manage_media', 'photovault_manage_settings' )
			)
		)
	);
}
add_filter( 'identity_security_kit_mfa_required_capabilities', 'photovault_identity_mfa_required_capabilities' );
