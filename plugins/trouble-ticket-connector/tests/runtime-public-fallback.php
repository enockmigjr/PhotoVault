<?php
/** Runtime check that anonymous support survives without a signing secret. */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$old_settings = get_option( 'trouble_ticket_connector_settings', null );
$old_secret   = get_option( 'trouble_ticket_connector_secret', null );

try {
	update_option(
		'trouble_ticket_connector_settings',
		trouble_ticket_connector_sanitize_settings(
			array(
				'support_url'     => 'https://example.com',
				'integration_key' => 'photovault-public-key',
				'integration_id'  => '',
			)
		),
		false
	);
	delete_option( 'trouble_ticket_connector_secret' );
	wp_set_current_user( 0 );
	if ( ! trouble_ticket_connector_has_public_config() || trouble_ticket_connector_is_configured() ) {
		throw new RuntimeException( 'Public and assertion readiness were not separated.' );
	}
	trouble_ticket_connector_shortcode();
	if ( ! wp_script_is( 'trouble-ticket-widget-loader', 'enqueued' ) || ! wp_script_is( 'trouble-ticket-widget-bridge', 'enqueued' ) ) {
		throw new RuntimeException( 'Anonymous widget scripts were not enqueued.' );
	}
	WP_CLI::success( 'Anonymous public fallback remains available without a secret.' );
} finally {
	if ( null === $old_settings ) {
		delete_option( 'trouble_ticket_connector_settings' );
	} else {
		update_option( 'trouble_ticket_connector_settings', $old_settings, false );
	}
	if ( null === $old_secret ) {
		delete_option( 'trouble_ticket_connector_secret' );
	} else {
		update_option( 'trouble_ticket_connector_secret', $old_secret, false );
	}
}
