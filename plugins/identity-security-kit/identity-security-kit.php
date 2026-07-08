<?php
/**
 * Plugin Name: Identity Security Kit
 * Description: Reusable identity, login, registration, and profile security handlers.
 * Version: 0.1.3
 * Author: PhotoVault
 * Text Domain: identity-security-kit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IDENTITY_SECURITY_KIT_VERSION', '0.1.3' );
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
 * Return safe default settings.
 *
 * @return array<string,int>
 */
function identity_security_kit_get_default_settings() {
	return array(
		'min_password_length'  => 8,
		'max_avatar_size_mb'   => 6,
		'max_avatar_dimension' => 6000,
	);
}

/**
 * Return normalized plugin settings.
 *
 * @return array<string,int>
 */
function identity_security_kit_get_settings() {
	$settings = get_option( 'identity_security_kit_settings', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$settings = wp_parse_args( $settings, identity_security_kit_get_default_settings() );

	$settings['min_password_length']  = max( 8, min( 128, absint( $settings['min_password_length'] ) ) );
	$settings['max_avatar_size_mb']   = max( 1, min( 12, absint( $settings['max_avatar_size_mb'] ) ) );
	$settings['max_avatar_dimension'] = max( 512, min( 6000, absint( $settings['max_avatar_dimension'] ) ) );

	return $settings;
}

/**
 * Return the audit table name.
 *
 * @return string
 */
function identity_security_kit_get_audit_table() {
	global $wpdb;

	return $wpdb->prefix . 'identity_security_audit';
}

/**
 * Install or upgrade audit storage.
 */
function identity_security_kit_install_schema() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = identity_security_kit_get_audit_table();
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		event varchar(80) NOT NULL,
		status varchar(24) NOT NULL DEFAULT 'info',
		user_id bigint(20) unsigned NULL,
		actor_user_id bigint(20) unsigned NULL,
		ip_hash char(64) NULL,
		user_agent varchar(255) NULL,
		context longtext NULL,
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY event (event),
		KEY status (status),
		KEY user_id (user_id),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
}

/**
 * Grant Identity Kit capabilities to administrators.
 */
function identity_security_kit_activate() {
	$admin = get_role( 'administrator' );
	if ( $admin ) {
		foreach ( identity_security_kit_get_capabilities() as $capability ) {
			$admin->add_cap( $capability );
		}
	}

	if ( false === get_option( 'identity_security_kit_settings', false ) ) {
		update_option( 'identity_security_kit_settings', identity_security_kit_get_default_settings(), false );
	}

	identity_security_kit_install_schema();
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

require_once IDENTITY_SECURITY_KIT_DIR . 'inc/audit.php';
require_once IDENTITY_SECURITY_KIT_DIR . 'inc/auth-handlers.php';
require_once IDENTITY_SECURITY_KIT_DIR . 'inc/admin.php';