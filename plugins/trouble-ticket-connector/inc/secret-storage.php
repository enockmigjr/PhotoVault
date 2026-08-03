<?php
/**
 * Authenticated storage for the integration secret.
 *
 * @package TroubleTicketConnector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the secret from server configuration or encrypted storage.
 *
 * @return string
 */
function trouble_ticket_connector_get_secret() {
	$external = defined( 'TROUBLE_TICKET_CONNECTOR_SECRET' ) ? (string) TROUBLE_TICKET_CONNECTOR_SECRET : (string) getenv( 'TROUBLE_TICKET_CONNECTOR_SECRET' );
	if ( '' !== trim( $external ) ) {
		return trim( $external );
	}
	$stored = get_option( 'trouble_ticket_connector_secret', '' );
	return is_string( $stored ) ? trouble_ticket_connector_decrypt_secret( $stored ) : '';
}

/**
 * Return a non-sensitive description of the secret source.
 *
 * @return string
 */
function trouble_ticket_connector_secret_source() {
	$external = defined( 'TROUBLE_TICKET_CONNECTOR_SECRET' ) || '' !== (string) getenv( 'TROUBLE_TICKET_CONNECTOR_SECRET' );
	return $external ? 'configuration' : ( get_option( 'trouble_ticket_connector_secret', false ) ? 'encrypted_option' : 'missing' );
}

/**
 * Encrypt and persist a local fallback secret.
 *
 * @param string $secret Plaintext secret.
 * @return true|WP_Error
 */
function trouble_ticket_connector_store_secret( $secret ) {
	$secret = trim( (string) $secret );
	if ( strlen( $secret ) < 32 || strlen( $secret ) > 256 ) {
		return new WP_Error( 'invalid_secret', __( 'Le secret doit contenir entre 32 et 256 caractères.', 'trouble-ticket-connector' ) );
	}
	$encrypted = trouble_ticket_connector_encrypt_secret( $secret );
	if ( is_wp_error( $encrypted ) ) {
		return $encrypted;
	}
	update_option( 'trouble_ticket_connector_secret', $encrypted, false );
	return true;
}

/** Remove only the encrypted option, never server configuration. */
function trouble_ticket_connector_revoke_local_secret() {
	delete_option( 'trouble_ticket_connector_secret' );
}

/**
 * Encrypt a secret with authenticated encryption.
 *
 * @param string $plaintext Plaintext secret.
 * @return string|WP_Error
 */
function trouble_ticket_connector_encrypt_secret( $plaintext ) {
	$key = trouble_ticket_connector_secret_key();
	if ( function_exists( 'sodium_crypto_secretbox' ) ) {
		$nonce  = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$cipher = sodium_crypto_secretbox( $plaintext, $nonce, $key );
		return 's1.' . base64_encode( $nonce . $cipher ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Binary envelope encoding.
	}
	if ( function_exists( 'openssl_encrypt' ) ) {
		$nonce  = random_bytes( 12 );
		$tag    = '';
		$cipher = openssl_encrypt( $plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, 'trouble-ticket-connector', 16 );
		if ( false !== $cipher ) {
			return 'g1.' . base64_encode( $nonce . $tag . $cipher ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Binary envelope encoding.
		}
	}
	return new WP_Error( 'crypto_unavailable', __( 'Aucun moteur de chiffrement authentifié n’est disponible.', 'trouble-ticket-connector' ) );
}

/**
 * Decrypt a versioned authenticated envelope.
 *
 * @param string $stored Stored envelope.
 * @return string
 */
function trouble_ticket_connector_decrypt_secret( $stored ) {
	$encoded = substr( $stored, 3 );
	$raw     = base64_decode( $encoded, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Binary envelope decoding.
	if ( false === $raw ) {
		return '';
	}
	$key = trouble_ticket_connector_secret_key();
	if ( 0 === strpos( $stored, 's1.' ) && function_exists( 'sodium_crypto_secretbox_open' ) ) {
		$nonce  = substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$cipher = substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$plain  = sodium_crypto_secretbox_open( $cipher, $nonce, $key );
		return false === $plain ? '' : $plain;
	}
	if ( 0 === strpos( $stored, 'g1.' ) && function_exists( 'openssl_decrypt' ) ) {
		$nonce  = substr( $raw, 0, 12 );
		$tag    = substr( $raw, 12, 16 );
		$cipher = substr( $raw, 28 );
		$plain  = openssl_decrypt( $cipher, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $nonce, $tag, 'trouble-ticket-connector' );
		return false === $plain ? '' : $plain;
	}
	return '';
}

/**
 * Derive a plugin-isolated 256-bit key from WordPress salts.
 *
 * @return string
 */
function trouble_ticket_connector_secret_key() {
	return hash_hmac( 'sha256', 'trouble-ticket-connector:secret-storage:v1', wp_salt( 'secure_auth' ), true );
}
