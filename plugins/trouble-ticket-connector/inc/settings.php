<?php
/**
 * Settings and validation.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return safe connector defaults.
 *
 * @return array<string,mixed>
 */
function trouble_ticket_connector_default_settings() {
	return array(
		'support_url'      => '',
		'integration_key'  => '',
		'integration_id'   => '',
		'audience'         => 'telecom-integration-assertion',
		'origin'           => trouble_ticket_connector_site_origin(),
		'display_mode'     => 'shortcode',
		'page_ids'         => array(),
		'position'         => 'right',
		'label'            => __( 'Assistance', 'trouble-ticket-connector' ),
		'loader_integrity' => 'sha384-63HovOBBVveI4gTSPtMyLyAmo64gADN020jaBKjd1vzmlRh3aVqe3+HX/nJ2eHq7',
	);
}

/**
 * Return normalized stored settings.
 *
 * @return array<string,mixed>
 */
function trouble_ticket_connector_get_settings() {
	$stored = get_option( 'trouble_ticket_connector_settings', array() );
	return trouble_ticket_connector_sanitize_settings( is_array( $stored ) ? $stored : array() );
}

/**
 * Sanitize the complete settings structure.
 *
 * @param mixed $input Raw settings.
 * @return array<string,mixed>
 */
function trouble_ticket_connector_sanitize_settings( $input ) {
	$defaults = trouble_ticket_connector_default_settings();
	$input    = is_array( $input ) ? $input : array();
	$pages    = isset( $input['page_ids'] ) ? $input['page_ids'] : array();
	if ( is_string( $pages ) ) {
		$pages = preg_split( '/[\s,]+/', $pages );
	}
	$pages = is_array( $pages ) ? array_values( array_unique( array_filter( array_map( 'absint', $pages ) ) ) ) : array();

	$support_url = trouble_ticket_connector_normalize_support_url( isset( $input['support_url'] ) ? $input['support_url'] : '' );
	$origin      = trouble_ticket_connector_normalize_origin( isset( $input['origin'] ) ? $input['origin'] : '' );
	$key         = isset( $input['integration_key'] ) ? sanitize_text_field( $input['integration_key'] ) : '';
	$id          = isset( $input['integration_id'] ) ? strtolower( sanitize_text_field( $input['integration_id'] ) ) : '';
	$audience    = isset( $input['audience'] ) ? sanitize_text_field( $input['audience'] ) : '';
	$integrity   = isset( $input['loader_integrity'] ) ? trim( sanitize_text_field( $input['loader_integrity'] ) ) : '';

	return array(
		'support_url'      => $support_url,
		'integration_key'  => preg_match( '/^[A-Za-z0-9._-]{16,80}$/', $key ) ? $key : '',
		'integration_id'   => trouble_ticket_connector_is_uuid( $id ) ? $id : '',
		'audience'         => preg_match( '/^[A-Za-z0-9._:-]{3,120}$/', $audience ) ? $audience : $defaults['audience'],
		'origin'           => $origin ? $origin : trouble_ticket_connector_site_origin(),
		'display_mode'     => isset( $input['display_mode'] ) && 'auto' === $input['display_mode'] ? 'auto' : 'shortcode',
		'page_ids'         => $pages,
		'position'         => isset( $input['position'] ) && 'left' === $input['position'] ? 'left' : 'right',
		'label'            => isset( $input['label'] ) ? mb_substr( sanitize_text_field( $input['label'] ), 0, 60 ) : $defaults['label'],
		'loader_integrity' => preg_match( '/^sha384-[A-Za-z0-9+\/=]{64}$/', $integrity ) ? $integrity : $defaults['loader_integrity'],
	);
}

/**
 * Normalize a secure portal base URL.
 *
 * @param mixed $value Raw URL.
 * @return string
 */
function trouble_ticket_connector_normalize_support_url( $value ) {
	$url = untrailingslashit( esc_url_raw( (string) $value ) );
	if ( ! wp_http_validate_url( $url ) ) {
		return '';
	}
	$parts = wp_parse_url( $url );
	if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) || ! empty( $parts['path'] ) || isset( $parts['query'] ) || isset( $parts['fragment'] ) ) {
		return '';
	}
	$host  = isset( $parts['host'] ) ? strtolower( $parts['host'] ) : '';
	if ( 'https' !== ( $parts['scheme'] ?? '' ) && ! in_array( $host, array( 'localhost', 'support.localhost' ), true ) ) {
		return '';
	}
	$origin = trouble_ticket_connector_origin_from_parts( $parts );
	return $origin === trouble_ticket_connector_site_origin() ? '' : $origin;
}

/**
 * Normalize an exact HTTPS origin.
 *
 * @param mixed $value Raw origin.
 * @return string
 */
function trouble_ticket_connector_normalize_origin( $value ) {
	$url   = esc_url_raw( (string) $value );
	$parts = wp_parse_url( $url );
	if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) || isset( $parts['path'] ) || isset( $parts['query'] ) || isset( $parts['fragment'] ) ) {
		return '';
	}
	if ( 'https' !== $parts['scheme'] && ! in_array( strtolower( $parts['host'] ), array( 'localhost', '127.0.0.1' ), true ) ) {
		return '';
	}
	return trouble_ticket_connector_origin_from_parts( $parts );
}

/**
 * Return the exact origin of the current WordPress site.
 *
 * @return string
 */
function trouble_ticket_connector_site_origin() {
	$parts = wp_parse_url( home_url( '/' ) );
	if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || empty( $parts['host'] ) ) {
		return '';
	}
	return trouble_ticket_connector_origin_from_parts( $parts );
}

/**
 * Build a browser-equivalent origin and remove default ports.
 *
 * @param array<string,mixed> $parts Parsed URL parts.
 * @return string
 */
function trouble_ticket_connector_origin_from_parts( $parts ) {
	$scheme = strtolower( (string) $parts['scheme'] );
	$host   = strtolower( (string) $parts['host'] );
	$port   = isset( $parts['port'] ) ? absint( $parts['port'] ) : 0;
	if ( ( 'https' === $scheme && 443 === $port ) || ( 'http' === $scheme && 80 === $port ) ) {
		$port = 0;
	}
	return $scheme . '://' . $host . ( $port ? ':' . $port : '' );
}

/**
 * Validate a canonical UUID.
 *
 * @param mixed $value Candidate value.
 * @return bool
 */
function trouble_ticket_connector_is_uuid( $value ) {
	return 1 === preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', (string) $value );
}

/**
 * Check whether all runtime credentials are available.
 *
 * @return bool
 */
function trouble_ticket_connector_is_configured() {
	$settings = trouble_ticket_connector_get_settings();
	return '' !== $settings['support_url'] && '' !== $settings['integration_key'] && '' !== $settings['integration_id'] && '' !== trouble_ticket_connector_get_secret();
}

/**
 * Check whether the public widget can load without an identity assertion.
 *
 * @return bool
 */
function trouble_ticket_connector_has_public_config() {
	$settings = trouble_ticket_connector_get_settings();
	return '' !== $settings['support_url'] && '' !== $settings['integration_key'];
}
