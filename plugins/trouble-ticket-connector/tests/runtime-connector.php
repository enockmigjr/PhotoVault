<?php
/** Runtime validation. Usage: wp eval-file .../tests/runtime-connector.php */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function ttc_runtime_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function ttc_runtime_decode( $value ) {
	$padding = strlen( $value ) % 4;
	if ( $padding ) {
		$value .= str_repeat( '=', 4 - $padding );
	}
	return base64_decode( strtr( $value, '-_', '+/' ), true );
}

$old_settings = get_option( 'trouble_ticket_connector_settings', null );
$old_secret   = get_option( 'trouble_ticket_connector_secret', null );
$user_id      = 0;

try {
	ttc_runtime_assert( function_exists( 'trouble_ticket_connector_create_assertion' ), 'Connector is not active.' );
	ttc_runtime_assert( '' === trouble_ticket_connector_normalize_origin( 'https://evil.test/path' ), 'Origin with path accepted.' );
	ttc_runtime_assert( '' === trouble_ticket_connector_normalize_support_url( 'http://evil.test' ), 'Insecure remote URL accepted.' );
	ttc_runtime_assert( 'http://localhost:3005' === trouble_ticket_connector_normalize_support_url( 'http://localhost:3005' ), 'Local development URL rejected.' );

	$settings = trouble_ticket_connector_sanitize_settings(
		array(
			'support_url'     => 'https://example.com',
			'integration_key' => 'photovault-public-key',
			'integration_id'  => '018f0f00-1234-7abc-8def-0123456789ab',
			'audience'        => 'telecom-integration-assertion',
			'origin'          => 'https://photos.example.test',
		)
	);
	update_option( 'trouble_ticket_connector_settings', $settings, false );
	$secret = 'runtime-secret-value-with-at-least-32-characters';
	ttc_runtime_assert( true === trouble_ticket_connector_store_secret( $secret ), 'Secret encryption failed.' );
	ttc_runtime_assert( hash_equals( $secret, trouble_ticket_connector_get_secret() ), 'Secret round-trip failed.' );

	$email   = 'ttc-runtime-' . wp_generate_password( 8, false ) . '@example.test';
	$user_id = wp_insert_user( array( 'user_login' => $email, 'user_email' => $email, 'user_pass' => wp_generate_password( 24 ), 'display_name' => 'Runtime Connector' ) );
	ttc_runtime_assert( ! is_wp_error( $user_id ), 'Runtime user creation failed.' );
	ttc_runtime_assert( is_wp_error( trouble_ticket_connector_create_assertion( $user_id ) ), 'Unverified email accepted.' );
	update_user_meta( $user_id, identity_security_kit_email_verified_meta_key(), '1' );

	$assertion = trouble_ticket_connector_create_assertion( $user_id );
	if ( is_wp_error( $assertion ) ) {
		throw new RuntimeException( 'Verified user assertion failed: ' . $assertion->get_error_code() . ' - ' . $assertion->get_error_message() );
	}
	$parts = explode( '.', $assertion );
	ttc_runtime_assert( 3 === count( $parts ), 'JWT is not compact.' );
	$claims = json_decode( ttc_runtime_decode( $parts[1] ), true );
	ttc_runtime_assert( is_array( $claims ), 'Claims are invalid.' );
	ttc_runtime_assert( 'telecom-integration-assertion' === $claims['aud'], 'Audience mismatch.' );
	ttc_runtime_assert( 'https://photos.example.test' === $claims['origin'], 'Origin mismatch.' );
	ttc_runtime_assert( 120 === $claims['exp'] - $claims['iat'], 'Assertion lifetime mismatch.' );
	ttc_runtime_assert( 1 === preg_match( '/^wp_[a-f0-9]{48}$/', $claims['sub'] ), 'Subject is not opaque.' );
	$signature = trouble_ticket_connector_base64url( hash_hmac( 'sha256', $parts[0] . '.' . $parts[1], $secret, true ) );
	ttc_runtime_assert( hash_equals( $signature, $parts[2] ), 'JWT signature mismatch.' );

	$second = trouble_ticket_connector_create_assertion( $user_id );
	$second_claims = json_decode( ttc_runtime_decode( explode( '.', $second )[1] ), true );
	ttc_runtime_assert( $claims['sub'] === $second_claims['sub'], 'Subject is not stable.' );
	ttc_runtime_assert( $claims['jti'] !== $second_claims['jti'], 'Assertion nonce was reused.' );

	wp_set_current_user( $user_id );
	$valid_request = new WP_REST_Request( 'POST', '/trouble-ticket/v1/assertion' );
	$valid_request->set_header( 'X-WP-Nonce', wp_create_nonce( 'wp_rest' ) );
	ttc_runtime_assert( true === trouble_ticket_connector_rest_permission( $valid_request ), 'Valid REST nonce rejected.' );
	$invalid_request = new WP_REST_Request( 'POST', '/trouble-ticket/v1/assertion' );
	$invalid_request->set_header( 'X-WP-Nonce', 'invalid' );
	ttc_runtime_assert( is_wp_error( trouble_ticket_connector_rest_permission( $invalid_request ) ), 'Invalid REST nonce accepted.' );

	trouble_ticket_connector_revoke_local_secret();
	ttc_runtime_assert( '' === trouble_ticket_connector_get_secret(), 'Local secret revocation failed.' );
	WP_CLI::success( 'Trouble Ticket Connector runtime checks passed.' );
} finally {
	if ( $user_id && ! is_wp_error( $user_id ) ) {
		wp_delete_user( $user_id );
	}
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
