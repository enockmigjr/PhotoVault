<?php
/**
 * Load the remote widget and the local identity bridge.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether automatic injection applies to the current page.
 *
 * @return bool
 */
function trouble_ticket_connector_should_auto_render() {
	$settings = trouble_ticket_connector_get_settings();
	if ( 'auto' !== $settings['display_mode'] || is_admin() ) {
		return false;
	}
	return empty( $settings['page_ids'] ) || ( is_singular() && in_array( get_queried_object_id(), $settings['page_ids'], true ) );
}

/** Enqueue the immutable loader and local identity bridge once. */
function trouble_ticket_connector_enqueue_widget() {
	static $loaded = false;
	if ( $loaded || ! trouble_ticket_connector_has_public_config() ) {
		return;
	}
	$loaded   = true;
	$settings = trouble_ticket_connector_get_settings();
	$loader   = $settings['support_url'] . '/widget/v2/widget.js';
	wp_enqueue_script( 'trouble-ticket-widget-loader', $loader, array(), '2', true );
	wp_enqueue_script(
		'trouble-ticket-widget-bridge',
		TROUBLE_TICKET_CONNECTOR_URL . 'assets/widget-bridge.js',
		array( 'trouble-ticket-widget-loader' ),
		TROUBLE_TICKET_CONNECTOR_VERSION,
		true
	);
	wp_localize_script(
		'trouble-ticket-widget-bridge',
		'TroubleTicketConnector',
		array(
			'assertionUrl'  => esc_url_raw( rest_url( 'trouble-ticket/v1/assertion' ) ),
			'nonce'         => is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '',
			'authenticated' => is_user_logged_in(),
		)
	);
}

/** Enqueue the widget when automatic mode matches the current page. */
function trouble_ticket_connector_enqueue_auto_widget() {
	if ( trouble_ticket_connector_should_auto_render() ) {
		trouble_ticket_connector_enqueue_widget();
	}
}
add_action( 'wp_enqueue_scripts', 'trouble_ticket_connector_enqueue_auto_widget', 30 );

/**
 * Render the shortcode anchor and enqueue the widget.
 *
 * @return string
 */
function trouble_ticket_connector_shortcode() {
	trouble_ticket_connector_enqueue_widget();
	return '<span class="trouble-ticket-connector-anchor" aria-hidden="true"></span>';
}
add_shortcode( 'trouble_ticket_support', 'trouble_ticket_connector_shortcode' );

/**
 * Add public configuration and SRI attributes to the remote loader.
 *
 * @param string $tag    Generated script tag.
 * @param string $handle Script handle.
 * @return string
 */
function trouble_ticket_connector_loader_tag( $tag, $handle ) {
	if ( 'trouble-ticket-widget-loader' !== $handle ) {
		return $tag;
	}
	$settings = trouble_ticket_connector_get_settings();
	$attrs    = sprintf(
		' data-integration-key="%s" data-label="%s" data-position="%s" integrity="%s" crossorigin="anonymous"',
		esc_attr( $settings['integration_key'] ),
		esc_attr( $settings['label'] ),
		esc_attr( $settings['position'] ),
		esc_attr( $settings['loader_integrity'] )
	);
	return preg_replace( '/<script\b/', '<script' . $attrs, $tag, 1 );
}
add_filter( 'script_loader_tag', 'trouble_ticket_connector_loader_tag', 10, 2 );
