<?php
/**
 * Subscriber handling for Newsletter Campaign Kit.
 *
 * @package NewsletterCampaignKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a safe redirect URL after subscription attempts.
 *
 * @param string $status Result status.
 * @return string
 */
function newsletter_campaign_kit_get_redirect_url( $status ) {
	$referer = wp_get_referer();
	$url     = $referer ? $referer : home_url( '/' );

	return add_query_arg( 'newsletter', sanitize_key( $status ), $url );
}

/**
 * Return a normalized IP hash without storing the raw address.
 *
 * @return string
 */
function newsletter_campaign_kit_get_request_ip_hash() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( empty( $ip ) ) {
		return '';
	}

	return hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
}

/**
 * Subscribe or reactivate an email address.
 *
 * @param string $email        Email address.
 * @param string $source       Subscription source.
 * @param string $consent_text Consent label shown to the user.
 * @return true|WP_Error
 */
function newsletter_campaign_kit_subscribe_email( $email, $source, $consent_text ) {
	global $wpdb;

	$email = sanitize_email( $email );
	if ( empty( $email ) || ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'Invalid email address.', 'newsletter-campaign-kit' ) );
	}

	$table_name = newsletter_campaign_kit_get_subscribers_table();
	$email_hash = hash_hmac( 'sha256', strtolower( $email ), wp_salt( 'auth' ) );
	$now        = current_time( 'mysql', true );
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 255 ) : '';

	$existing_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$table_name} WHERE email_hash = %s LIMIT 1",
			$email_hash
		)
	);

	$data = array(
		'email'        => $email,
		'status'       => 'subscribed',
		'source'       => sanitize_key( $source ),
		'consent_text' => sanitize_textarea_field( $consent_text ),
		'ip_hash'      => newsletter_campaign_kit_get_request_ip_hash(),
		'user_agent'   => $user_agent,
		'updated_at'   => $now,
	);

	if ( $existing_id ) {
		$updated = $wpdb->update(
			$table_name,
			$data,
			array( 'id' => (int) $existing_id ),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);

		return false === $updated ? new WP_Error( 'db_error', __( 'Subscription could not be saved.', 'newsletter-campaign-kit' ) ) : true;
	}

	$data['email_hash'] = $email_hash;
	$data['created_at'] = $now;

	$inserted = $wpdb->insert(
		$table_name,
		$data,
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	return false === $inserted ? new WP_Error( 'db_error', __( 'Subscription could not be saved.', 'newsletter-campaign-kit' ) ) : true;
}

/**
 * Handle public newsletter subscription submissions.
 */
function newsletter_campaign_kit_handle_subscribe() {
	if ( ! isset( $_POST['newsletter_campaign_kit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['newsletter_campaign_kit_nonce'] ) ), 'newsletter_campaign_kit_subscribe' ) ) {
		wp_safe_redirect( newsletter_campaign_kit_get_redirect_url( 'security_failed' ) );
		exit;
	}

	if ( empty( $_POST['newsletter_consent'] ) ) {
		wp_safe_redirect( newsletter_campaign_kit_get_redirect_url( 'consent_required' ) );
		exit;
	}

	$email        = isset( $_POST['newsletter_email'] ) ? sanitize_email( wp_unslash( $_POST['newsletter_email'] ) ) : '';
	$source       = isset( $_POST['newsletter_source'] ) ? sanitize_key( wp_unslash( $_POST['newsletter_source'] ) ) : 'front_footer';
	$consent_text = __( 'I agree to receive PhotoVault editorial updates.', 'newsletter-campaign-kit' );
	$result       = newsletter_campaign_kit_subscribe_email( $email, $source, $consent_text );

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( newsletter_campaign_kit_get_redirect_url( $result->get_error_code() ) );
		exit;
	}

	wp_safe_redirect( newsletter_campaign_kit_get_redirect_url( 'subscribed' ) );
	exit;
}
add_action( 'admin_post_nopriv_newsletter_campaign_kit_subscribe', 'newsletter_campaign_kit_handle_subscribe' );
add_action( 'admin_post_newsletter_campaign_kit_subscribe', 'newsletter_campaign_kit_handle_subscribe' );