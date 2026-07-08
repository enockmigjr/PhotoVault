<?php
/**
 * Plugin Name: Identity Security Kit
 * Description: Reusable identity, login, registration, and profile security handlers.
 * Version: 0.1.1
 * Author: PhotoVault
 * Text Domain: identity-security-kit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IDENTITY_SECURITY_KIT_VERSION', '0.1.1' );
define( 'IDENTITY_SECURITY_KIT_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Return the public capabilities managed by the plugin.
 *
 * @return string[]
 */
function identity_security_kit_get_capabilities() {
	return array(
		'identity_manage_settings',
		'identity_manage_security',
		'identity_view_security_audit',
	);
}

/**
 * Grant Identity Kit capabilities to administrators.
 */
function identity_security_kit_activate() {
	$admin = get_role( 'administrator' );
	if ( ! $admin ) {
		return;
	}

	foreach ( identity_security_kit_get_capabilities() as $capability ) {
		$admin->add_cap( $capability );
	}

	update_option( 'identity_security_kit_version', IDENTITY_SECURITY_KIT_VERSION, false );
}
register_activation_hook( __FILE__, 'identity_security_kit_activate' );

/**
 * Apply versioned upgrades for already active installations.
 */
function identity_security_kit_maybe_upgrade() {
	$installed_version = get_option( 'identity_security_kit_version' );
	if ( IDENTITY_SECURITY_KIT_VERSION === $installed_version ) {
		return;
	}

	identity_security_kit_activate();
}
add_action( 'admin_init', 'identity_security_kit_maybe_upgrade' );

require_once IDENTITY_SECURITY_KIT_DIR . 'inc/auth-handlers.php';