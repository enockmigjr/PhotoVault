<?php
/**
 * Create short-lived HS256 assertions accepted by the telecom BFF.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create an assertion for one verified WordPress user.
 *
 * @param int $user_id WordPress user ID.
 * @return string|WP_Error
 */
function trouble_ticket_connector_create_assertion( $user_id ) {
	if ( ! function_exists( 'identity_security_kit_is_email_verified' ) ) {
		return new WP_Error( 'identity_kit_missing', __( 'Identity Security Kit est requis.', 'trouble-ticket-connector' ), array( 'status' => 503 ) );
	}
	$user_id = absint( $user_id );
	if ( ! $user_id || ! identity_security_kit_is_email_verified( $user_id ) ) {
		return new WP_Error( 'email_unverified', __( 'Votre adresse email doit être vérifiée.', 'trouble-ticket-connector' ), array( 'status' => 403 ) );
	}
	$user     = get_userdata( $user_id );
	$settings = trouble_ticket_connector_get_settings();
	$secret   = trouble_ticket_connector_get_secret();
	if ( ! $user instanceof WP_User || ! is_email( $user->user_email ) || ! trouble_ticket_connector_is_configured() ) {
		return new WP_Error( 'connector_unavailable', __( 'Le connecteur de support n’est pas configuré.', 'trouble-ticket-connector' ), array( 'status' => 503 ) );
	}
	$issued_at = time();
	$payload   = array(
		'iss'            => $settings['integration_key'],
		'aud'            => $settings['audience'],
		'sub'            => trouble_ticket_connector_subject( $user_id, $settings['integration_id'] ),
		'integration_id' => $settings['integration_id'],
		'email'          => strtolower( $user->user_email ),
		'display_name'   => mb_substr( sanitize_text_field( $user->display_name ), 0, 160 ),
		'origin'         => $settings['origin'],
		'iat'            => $issued_at,
		'exp'            => $issued_at + 120,
		'jti'            => bin2hex( random_bytes( 16 ) ),
	);
	if ( function_exists( 'identity_security_kit_is_phone_verified' ) && identity_security_kit_is_phone_verified( $user_id ) ) {
		$phone = get_user_meta( $user_id, identity_security_kit_phone_meta_key(), true );
		if ( is_string( $phone ) && '' !== $phone ) {
			$payload['phone'] = $phone;
		}
	}
	return trouble_ticket_connector_sign_jwt( $payload, $secret );
}

/**
 * Return a stable opaque subject scoped to one integration.
 *
 * @param int    $user_id        WordPress user ID.
 * @param string $integration_id Integration UUID.
 * @return string
 */
function trouble_ticket_connector_subject( $user_id, $integration_id ) {
	$meta_key = 'ttc_subject_' . substr( hash( 'sha256', $integration_id ), 0, 16 );
	$subject  = get_user_meta( absint( $user_id ), $meta_key, true );
	if ( is_string( $subject ) && 1 === preg_match( '/^wp_[a-f0-9]{48}$/', $subject ) ) {
		return $subject;
	}
	$subject = 'wp_' . bin2hex( random_bytes( 24 ) );
	if ( ! add_user_meta( absint( $user_id ), $meta_key, $subject, true ) ) {
		$existing = get_user_meta( absint( $user_id ), $meta_key, true );
		return is_string( $existing ) && '' !== $existing ? $existing : $subject;
	}
	return $subject;
}

/**
 * Sign a compact JWT with HS256.
 *
 * @param array<string,mixed> $payload Assertion claims.
 * @param string              $secret  Integration secret.
 * @return string
 */
function trouble_ticket_connector_sign_jwt( $payload, $secret ) {
	$header  = trouble_ticket_connector_base64url(
		wp_json_encode(
			array(
				'alg' => 'HS256',
				'typ' => 'JWT',
			)
		)
	);
	$claims  = trouble_ticket_connector_base64url( wp_json_encode( $payload ) );
	$content = $header . '.' . $claims;
	return $content . '.' . trouble_ticket_connector_base64url( hash_hmac( 'sha256', $content, $secret, true ) );
}

/**
 * Encode binary or JSON content using base64url.
 *
 * @param string $value Raw value.
 * @return string
 */
function trouble_ticket_connector_base64url( $value ) {
	return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Required by the JWT standard.
}
