<?php
/**
 * Remove connector configuration while preserving stable subject mappings.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'trouble_ticket_connector_settings' );
delete_option( 'trouble_ticket_connector_secret' );

$administrator = get_role( 'administrator' );
if ( $administrator ) {
	$administrator->remove_cap( 'manage_trouble_ticket_connector' );
}
