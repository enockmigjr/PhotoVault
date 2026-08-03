<?php
/**
 * Admin mutations and non-sensitive connectivity check.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Require the connector capability and a valid action nonce.
 *
 * @param string $nonce_action Nonce action name.
 */
function trouble_ticket_connector_admin_authorized( $nonce_action ) {
	if ( ! current_user_can( 'manage_trouble_ticket_connector' ) && ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Accès refusé.', 'trouble-ticket-connector' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( $nonce_action );
}

/** Persist sanitized settings and optionally rotate the local secret. */
function trouble_ticket_connector_save_settings() {
	trouble_ticket_connector_admin_authorized( 'trouble_ticket_connector_save' );
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The nonce is verified above and the full structure is sanitized by the connector.
	$raw      = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();
	$settings = trouble_ticket_connector_sanitize_settings( $raw );
	update_option( 'trouble_ticket_connector_settings', $settings, false );
	// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The nonce is verified above; the secret is validated before authenticated encryption.
	$secret = isset( $_POST['integration_secret'] ) ? trim( (string) wp_unslash( $_POST['integration_secret'] ) ) : '';
	if ( '' !== $secret ) {
		$result = trouble_ticket_connector_store_secret( $secret );
		if ( is_wp_error( $result ) ) {
			trouble_ticket_connector_admin_redirect( 'secret_error' );
		}
	}
	trouble_ticket_connector_admin_redirect( 'saved' );
}
add_action( 'admin_post_trouble_ticket_connector_save', 'trouble_ticket_connector_save_settings' );

/** Revoke only the encrypted local copy of the integration secret. */
function trouble_ticket_connector_revoke_secret() {
	trouble_ticket_connector_admin_authorized( 'trouble_ticket_connector_revoke' );
	if ( 'configuration' === trouble_ticket_connector_secret_source() ) {
		trouble_ticket_connector_admin_redirect( 'constant_secret' );
	}
	trouble_ticket_connector_revoke_local_secret();
	trouble_ticket_connector_admin_redirect( 'revoked' );
}
add_action( 'admin_post_trouble_ticket_connector_revoke', 'trouble_ticket_connector_revoke_secret' );

/** Check the public configuration endpoint without sending a secret. */
function trouble_ticket_connector_test_connection() {
	trouble_ticket_connector_admin_authorized( 'trouble_ticket_connector_test' );
	$settings = trouble_ticket_connector_get_settings();
	if ( '' === $settings['support_url'] || '' === $settings['integration_key'] ) {
		trouble_ticket_connector_admin_redirect( 'test_failed' );
	}
	$query    = add_query_arg(
		array(
			'context'        => 'widget',
			'integrationKey' => $settings['integration_key'],
			'origin'         => $settings['origin'],
		),
		$settings['support_url'] . '/api/config'
	);
	$response = wp_safe_remote_get(
		$query,
		array(
			'timeout'     => 8,
			'redirection' => 0,
			'headers'     => array( 'Accept' => 'application/json' ),
		)
	);
	$success  = ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response );
	trouble_ticket_connector_admin_redirect( $success ? 'test_ok' : 'test_failed' );
}
add_action( 'admin_post_trouble_ticket_connector_test', 'trouble_ticket_connector_test_connection' );

/**
 * Redirect back to the connector page with an allowlisted notice key.
 *
 * @param string $notice Notice key.
 */
function trouble_ticket_connector_admin_redirect( $notice ) {
	wp_safe_redirect( add_query_arg( 'ttc_notice', sanitize_key( $notice ), admin_url( 'options-general.php?page=trouble-ticket-connector' ) ) );
	exit;
}
