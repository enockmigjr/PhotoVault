<?php
/**
 * Plugin Name: Identity Security Kit
 * Description: Identity, login, registration, and profile handlers for PhotoVault.
 * Version: 0.1.0
 * Author: PhotoVault
 * Text Domain: photovault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IDENTITY_SECURITY_KIT_VERSION', '0.1.0' );
define( 'IDENTITY_SECURITY_KIT_DIR', plugin_dir_path( __FILE__ ) );

if ( defined( 'PHOTOVAULT_CORE_VERSION' ) ) {
	require_once IDENTITY_SECURITY_KIT_DIR . 'inc/auth-handlers.php';
}