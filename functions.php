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
define( 'PHOTOVAULT_VERSION', '1.4.0' );
define( 'PHOTOVAULT_DIR', get_template_directory() );
define( 'PHOTOVAULT_URI', get_template_directory_uri() );

// Le theme ne possede que la presentation. Les fonctionnalites applicatives
// sont fournies par PhotoVault Core et Identity Security Kit.
$photovault_includes = array(
	'inc/theme-setup.php',
	'inc/presentation-helpers.php',
	'inc/customizer.php',
	'inc/user-preferences.php',
);
foreach ( $photovault_includes as $file ) {
	$filepath = PHOTOVAULT_DIR . '/' . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}

/** Warn administrators when a required application plugin is unavailable. */
function photovault_render_required_plugins_notice() {
	$missing = array();
	if ( ! defined( 'PHOTOVAULT_CORE_VERSION' ) ) {
		$missing[] = 'PhotoVault Core';
	}
	if ( ! defined( 'IDENTITY_SECURITY_KIT_VERSION' ) ) {
		$missing[] = 'Identity Security Kit';
	}
	if ( empty( $missing ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p><strong>%1$s</strong> %2$s</p></div>',
		esc_html__( 'PhotoVault est incomplet.', 'photovault' ),
		esc_html( sprintf( __( 'Activez les extensions requises : %s.', 'photovault' ), implode( ', ', $missing ) ) )
	);
}
add_action( 'admin_notices', 'photovault_render_required_plugins_notice' );
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
