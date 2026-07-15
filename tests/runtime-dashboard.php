<?php
/**
 * WordPress runtime verification for the role-aware PhotoVault dashboard.
 *
 * Run with: wp eval-file wp-content/themes/PhotoVault/tests/runtime-dashboard.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function photovault_dashboard_runtime_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

global $wpdb;

$suffix           = strtolower( wp_generate_password( 8, false, false ) );
$email            = 'dashboard-' . $suffix . '@photovault.test';
$previous_user_id = get_current_user_id();
$client_id        = 0;
$media_id         = 0;
$subscriber_id    = 0;
$shooting_id      = 0;
$previous_server_name = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : null;
$_SERVER['SERVER_NAME'] = 'localhost';
$render_dashboard = static function ( $section ) {
	$_GET['section'] = $section;
	ob_start();
	include get_template_directory() . '/page-dashboard.php';
	return ob_get_clean();
};

try {
	photovault_create_app_pages();
	$profile_page = get_page_by_path( 'profile' );
	photovault_dashboard_runtime_assert( $profile_page instanceof WP_Post && 'page-profile.php' === get_page_template_slug( $profile_page->ID ), 'The profile system page was not provisioned with its template.' );
	$booking_page = get_page_by_path( 'booking' );
	photovault_dashboard_runtime_assert( $booking_page instanceof WP_Post && 'page-booking.php' === get_page_template_slug( $booking_page->ID ), 'The booking system page was not provisioned with its template.' );
	photovault_core_activate();
	$client_id = wp_insert_user(
		array(
			'user_login'   => 'dashboard-' . $suffix,
			'user_email'   => $email,
			'user_pass'    => wp_generate_password( 24, true, true ),
			'display_name' => 'Dashboard Runtime',
			'role'         => 'client',
		)
	);
	photovault_dashboard_runtime_assert( ! is_wp_error( $client_id ), 'Dashboard runtime user creation failed.' );
	$client_id = (int) $client_id;
	if ( function_exists( 'identity_security_kit_email_verified_meta_key' ) ) {
		update_user_meta( $client_id, identity_security_kit_email_verified_meta_key(), '1' );
	}
	wp_set_current_user( $client_id );

	$media_id = wp_insert_post(
		array(
			'post_type'   => 'media_item',
			'post_status' => 'publish',
			'post_title'  => 'Dashboard runtime work ' . $suffix,
			'post_author' => $client_id,
		)
	);
	photovault_dashboard_runtime_assert( ! is_wp_error( $media_id ), 'Dashboard runtime media creation failed.' );
	$media_id = (int) $media_id;
	photovault_dashboard_runtime_assert( true === photovault_add_user_favorite( $client_id, $media_id ), 'Dashboard runtime favorite creation failed.' );
	photovault_log_media_event( 'media_download', 'success', $media_id, array( 'user_id' => $client_id ) );
	$mail_filter = static function () { return true; };
	add_filter( 'pre_wp_mail', $mail_filter );
	$shooting_id = photovault_create_shooting(
		array(
			'type'          => 'portrait',
			'desired_date'  => wp_date( 'Y-m-d', time() + ( 8 * DAY_IN_SECONDS ) ),
			'location'      => 'Porto-Novo runtime studio',
			'message'       => 'Portrait editorial utilise pour verifier le dashboard client.',
			'contact_name'  => 'Dashboard Runtime',
			'contact_email' => $email,
			'contact_phone' => '+2290100000000',
		),
		$client_id
	);
	remove_filter( 'pre_wp_mail', $mail_filter );
	photovault_dashboard_runtime_assert( is_int( $shooting_id ), 'Dashboard runtime shooting creation failed.' );

	if ( function_exists( 'newsletter_campaign_kit_subscribe_email' ) ) {
		$result = newsletter_campaign_kit_subscribe_email( $email, 'runtime_dashboard', 'Dashboard integration verification' );
		photovault_dashboard_runtime_assert( true === $result, 'Dashboard newsletter subscription setup failed.' );
		$subscriber = newsletter_campaign_kit_get_subscriber_by_email( $email );
		$subscriber_id = $subscriber ? (int) $subscriber['id'] : 0;
		photovault_dashboard_runtime_assert( $subscriber_id > 0 && 'subscribed' === $subscriber['status'], 'Dashboard newsletter bridge did not resolve the owner subscription.' );
	}

	$overview = $render_dashboard( 'overview' );
	photovault_dashboard_runtime_assert( false !== strpos( $overview, 'Votre espace PhotoVault' ) && false !== strpos( $overview, 'Dashboard runtime work' ), 'Client overview did not render personal data.' );
	photovault_dashboard_runtime_assert( false === strpos( $overview, 'Analytique de la plateforme' ) && false === strpos( $overview, 'Administration' ), 'Client dashboard exposed manager navigation.' );

	$favorites = $render_dashboard( 'favorites' );
	photovault_dashboard_runtime_assert( false !== strpos( $favorites, 'data-pv-favorite-card="' . $media_id . '"' ) && false !== strpos( $favorites, 'data-pv-favorite' ), 'Favorites section did not render interactive personal media.' );
	$downloads = $render_dashboard( 'downloads' );
	photovault_dashboard_runtime_assert( false !== strpos( $downloads, 'Historique des telechargements' ) && false !== strpos( $downloads, 'Telecharger a nouveau' ), 'Download history section did not render a reusable secure action.' );
	$newsletter = $render_dashboard( 'newsletter' );
	photovault_dashboard_runtime_assert( false !== strpos( $newsletter, 'Gerer mes thematiques' ), 'Newsletter section did not expose the owner preference center.' );
	$bookings = $render_dashboard( 'bookings' );
	photovault_dashboard_runtime_assert( false !== strpos( $bookings, 'Porto-Novo runtime studio' ) && false !== strpos( $bookings, 'Nouvelle reservation' ), 'Bookings section did not render the owner reservation.' );

	$administrator = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ids' ) );
	photovault_dashboard_runtime_assert( ! empty( $administrator ), 'No administrator is available for dashboard analytics verification.' );
	wp_set_current_user( (int) $administrator[0] );
	$analytics = $render_dashboard( 'analytics' );
	photovault_dashboard_runtime_assert( false !== strpos( $analytics, 'Analytique de la plateforme' ) && false !== strpos( $analytics, 'Administration' ), 'Administrator analytics or navigation is missing.' );

	echo wp_json_encode(
		array(
			'client_dashboard'       => true,
			'favorites_ui'           => true,
			'download_history_ui'    => true,
			'newsletter_integration' => true,
			'admin_analytics'        => true,
			'role_isolation'         => true,
			'system_pages'           => true,
			'bookings'               => true,
		)
	);
} finally {
	unset( $_GET['section'] );
	if ( null === $previous_server_name ) {
		unset( $_SERVER['SERVER_NAME'] );
	} else {
		$_SERVER['SERVER_NAME'] = $previous_server_name;
	}
	wp_set_current_user( $previous_user_id );
	if ( $subscriber_id ) {
		$wpdb->delete( newsletter_campaign_kit_get_subscriber_topics_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_subscriber_lists_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_subscriber_tags_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_audit_table(), array( 'subscriber_id' => $subscriber_id ), array( '%d' ) );
		$wpdb->delete( newsletter_campaign_kit_get_subscribers_table(), array( 'id' => $subscriber_id ), array( '%d' ) );
	}
	if ( $client_id ) {
		$wpdb->delete( photovault_get_favorites_table(), array( 'user_id' => $client_id ), array( '%d' ) );
		$wpdb->delete( photovault_get_media_audit_table(), array( 'user_id' => $client_id ), array( '%d' ) );
	}
	if ( $media_id ) {
		wp_delete_post( $media_id, true );
	}
	if ( $shooting_id ) {
		wp_delete_post( $shooting_id, true );
	}
	if ( $client_id ) {
		wp_delete_user( $client_id );
	}
}
