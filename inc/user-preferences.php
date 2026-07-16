<?php
/**
 * Per-user presentation preferences for PhotoVault frontend surfaces.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Return one validated presentation preference for a user. */
function photovault_get_user_preference( $user_id, $key ) {
	$defaults = array(
		'gallery_density'  => 'editorial',
		'reduce_motion'    => '0',
		'dashboard_landing' => 'overview',
	);
	if ( ! isset( $defaults[ $key ] ) ) {
		return '';
	}
	$value = (string) get_user_meta( absint( $user_id ), 'photovault_' . $key, true );

	return '' !== $value ? $value : $defaults[ $key ];
}

/** Apply saved preferences as stable frontend body classes. */
function photovault_user_preference_body_classes( $classes ) {
	if ( ! is_user_logged_in() ) {
		return $classes;
	}
	$user_id   = get_current_user_id();
	$classes[] = 'compact' === photovault_get_user_preference( $user_id, 'gallery_density' ) ? 'pv-gallery-compact' : 'pv-gallery-editorial';
	if ( '1' === photovault_get_user_preference( $user_id, 'reduce_motion' ) ) {
		$classes[] = 'pv-reduce-motion';
	}

	return $classes;
}
add_filter( 'body_class', 'photovault_user_preference_body_classes' );

/** Save frontend presentation preferences for the authenticated account. */
function photovault_handle_user_preferences() {
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}
	check_admin_referer( 'photovault_save_preferences' );

	$density = isset( $_POST['gallery_density'] ) ? sanitize_key( wp_unslash( $_POST['gallery_density'] ) ) : 'editorial';
	$landing = isset( $_POST['dashboard_landing'] ) ? sanitize_key( wp_unslash( $_POST['dashboard_landing'] ) ) : 'overview';
	$density = in_array( $density, array( 'editorial', 'compact' ), true ) ? $density : 'editorial';
	$landing = in_array( $landing, array( 'overview', 'favorites', 'access', 'bookings' ), true ) ? $landing : 'overview';
	$user_id = get_current_user_id();

	update_user_meta( $user_id, 'photovault_gallery_density', $density );
	update_user_meta( $user_id, 'photovault_reduce_motion', isset( $_POST['reduce_motion'] ) ? '1' : '0' );
	update_user_meta( $user_id, 'photovault_dashboard_landing', $landing );

	wp_safe_redirect( add_query_arg( 'profile', 'preferences_updated', home_url( '/profile/' ) ) );
	exit;
}
add_action( 'admin_post_photovault_save_preferences', 'photovault_handle_user_preferences' );
