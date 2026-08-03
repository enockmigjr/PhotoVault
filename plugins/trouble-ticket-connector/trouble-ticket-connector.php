<?php
/**
 * Plugin Name: Trouble Ticket Connector
 * Description: Connecte WordPress au portail public de support sans stocker les tickets localement.
 * Version: 1.0.0
 * Author: PhotoVault
 * Text Domain: trouble-ticket-connector
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TROUBLE_TICKET_CONNECTOR_VERSION', '1.0.0' );
define( 'TROUBLE_TICKET_CONNECTOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'TROUBLE_TICKET_CONNECTOR_URL', plugin_dir_url( __FILE__ ) );

require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/settings.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/secret-storage.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/assertion-service.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/rest-routes.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/widget-renderer.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/admin-actions.php';
require_once TROUBLE_TICKET_CONNECTOR_DIR . 'inc/admin-page.php';

/** Grant the connector capability without changing existing plugin roles. */
function trouble_ticket_connector_activate() {
	$administrator = get_role( 'administrator' );
	if ( $administrator ) {
		$administrator->add_cap( 'manage_trouble_ticket_connector' );
	}
	if ( false === get_option( 'trouble_ticket_connector_settings', false ) ) {
		update_option( 'trouble_ticket_connector_settings', trouble_ticket_connector_default_settings(), false );
	}
}
register_activation_hook( __FILE__, 'trouble_ticket_connector_activate' );
