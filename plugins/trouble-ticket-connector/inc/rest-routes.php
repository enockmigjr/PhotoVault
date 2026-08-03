<?php
/**
 * Authenticated same-origin REST endpoint for one-time assertions.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the authenticated assertion endpoint. */
function trouble_ticket_connector_register_rest_routes() {
	register_rest_route(
		'trouble-ticket/v1',
		'/assertion',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'trouble_ticket_connector_rest_assertion',
			'permission_callback' => 'trouble_ticket_connector_rest_permission',
		)
	);
}
add_action( 'rest_api_init', 'trouble_ticket_connector_register_rest_routes' );

/**
 * Require both a WordPress session and a REST nonce.
 *
 * @param WP_REST_Request $request Current request.
 * @return true|WP_Error
 */
function trouble_ticket_connector_rest_permission( WP_REST_Request $request ) {
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'rest_not_logged_in', __( 'Connexion requise.', 'trouble-ticket-connector' ), array( 'status' => 401 ) );
	}
	$nonce = $request->get_header( 'x_wp_nonce' );
	if ( ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error( 'rest_cookie_invalid_nonce', __( 'Nonce REST invalide.', 'trouble-ticket-connector' ), array( 'status' => 403 ) );
	}
	return true;
}

/**
 * Issue one short-lived identity assertion.
 *
 * @return WP_REST_Response|WP_Error
 */
function trouble_ticket_connector_rest_assertion() {
	$user_id = get_current_user_id();
	if ( ! trouble_ticket_connector_assertion_rate_limit( $user_id ) ) {
		return new WP_Error( 'rate_limited', __( 'Trop de demandes. Réessayez dans une minute.', 'trouble-ticket-connector' ), array( 'status' => 429 ) );
	}
	$assertion = trouble_ticket_connector_create_assertion( $user_id );
	if ( is_wp_error( $assertion ) ) {
		return $assertion;
	}
	$response = new WP_REST_Response(
		array(
			'success' => true,
			'data'    => array( 'assertion' => $assertion ),
		),
		200
	);
	$response->header( 'Cache-Control', 'no-store, private' );
	$response->header( 'Pragma', 'no-cache' );
	$response->header( 'X-Content-Type-Options', 'nosniff' );
	return $response;
}

/**
 * Apply a privacy-preserving per-user issuance limit.
 *
 * @param int $user_id WordPress user ID.
 * @return bool
 */
function trouble_ticket_connector_assertion_rate_limit( $user_id ) {
	$key      = 'ttc_assert_' . hash_hmac( 'sha256', (string) absint( $user_id ), wp_salt( 'nonce' ) );
	$attempts = absint( get_transient( $key ) );
	if ( $attempts >= 10 ) {
		return false;
	}
	set_transient( $key, $attempts + 1, MINUTE_IN_SECONDS );
	return true;
}
