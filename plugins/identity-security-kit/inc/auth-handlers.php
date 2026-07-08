<?php
/**
 * Authentication and profile handlers for Identity Security Kit.
 *
 * @package IdentitySecurityKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get configurable routes while preserving existing PhotoVault URLs by default.
 *
 * @return array<string,string>
 */
function identity_security_kit_get_routes() {
	$gallery_url = get_post_type_archive_link( 'media_item' );
	if ( ! $gallery_url ) {
		$gallery_url = home_url( '/' );
	}

	$routes = array(
		'login'       => home_url( '/login/' ),
		'register'    => home_url( '/register/' ),
		'profile'     => home_url( '/profile/' ),
		'dashboard'   => home_url( '/dashboard/' ),
		'after_login' => $gallery_url,
	);

	/**
	 * Filter Identity Kit route targets.
	 *
	 * @param array<string,string> $routes Route map.
	 */
	return apply_filters( 'identity_security_kit_routes', $routes );
}

/**
 * Return a route URL by key.
 *
 * @param string $key Route key.
 * @return string
 */
function identity_security_kit_get_route_url( $key ) {
	$routes = identity_security_kit_get_routes();

	return isset( $routes[ $key ] ) ? $routes[ $key ] : home_url( '/' );
}

/**
 * Redirect safely to a configured route.
 *
 * @param string              $key  Route key.
 * @param array<string,mixed> $args Query arguments.
 */
function identity_security_kit_redirect( $key, $args = array() ) {
	$url = identity_security_kit_get_route_url( $key );
	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}

	wp_safe_redirect( $url );
	exit;
}

/**
 * Check whether the current request is a POST request.
 *
 * @return bool
 */
function identity_security_kit_is_post_request() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';

	return 'POST' === strtoupper( $method );
}

/**
 * Return allowed image MIME types for identity uploads.
 *
 * @return array<string,string>
 */
function identity_security_kit_get_allowed_image_mimes() {
	$mimes = array(
		'jpg|jpeg' => 'image/jpeg',
		'png'      => 'image/png',
		'webp'     => 'image/webp',
	);

	/**
	 * Filter allowed image MIME types for identity uploads.
	 *
	 * @param array<string,string> $mimes MIME map.
	 */
	return apply_filters( 'identity_security_kit_allowed_image_mimes', $mimes );
}

/**
 * Validate an uploaded profile image server-side.
 *
 * @param array<string,mixed> $file Uploaded file array.
 * @return true|WP_Error
 */
function identity_security_kit_validate_uploaded_image_file( $file ) {
	if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
		return new WP_Error( 'invalid_upload', __( 'The uploaded file is invalid.', 'identity-security-kit' ) );
	}

	$max_size = (int) apply_filters( 'identity_security_kit_max_avatar_size', 6 * MB_IN_BYTES );
	$size     = isset( $file['size'] ) ? (int) $file['size'] : 0;
	if ( $size <= 0 || $size > $max_size ) {
		return new WP_Error( 'file_too_large', __( 'The uploaded file is too large.', 'identity-security-kit' ) );
	}

	$filename = isset( $file['name'] ) ? sanitize_file_name( wp_unslash( $file['name'] ) ) : '';
	$check    = wp_check_filetype_and_ext( $file['tmp_name'], $filename, identity_security_kit_get_allowed_image_mimes() );
	if ( empty( $check['ext'] ) || empty( $check['type'] ) ) {
		return new WP_Error( 'invalid_file_type', __( 'This image type is not allowed.', 'identity-security-kit' ) );
	}

	$dimensions = @getimagesize( $file['tmp_name'] );
	if ( false === $dimensions ) {
		return new WP_Error( 'invalid_image', __( 'The uploaded file is not a valid image.', 'identity-security-kit' ) );
	}

	$max_dimension = (int) apply_filters( 'identity_security_kit_max_avatar_dimension', 6000 );
	if ( $dimensions[0] > $max_dimension || $dimensions[1] > $max_dimension ) {
		return new WP_Error( 'image_too_large', __( 'The uploaded image dimensions are too large.', 'identity-security-kit' ) );
	}

	return true;
}

/**
 * Resolve the default role for front-office registrations.
 *
 * @return string
 */
function identity_security_kit_get_registration_role() {
	$role = get_role( 'client' ) ? 'client' : 'subscriber';

	/**
	 * Filter the role assigned during front-office registration.
	 *
	 * @param string $role Role slug.
	 */
	return sanitize_key( apply_filters( 'identity_security_kit_registration_role', $role ) );
}

/**
 * Handle front-office login submissions.
 */
function identity_security_kit_handle_login() {
	if ( ! identity_security_kit_is_post_request() || ! isset( $_POST['photovault_login_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_login_nonce'] ) ), 'photovault_login_action' ) ) {
		wp_die( esc_html__( 'Security verification failed.', 'identity-security-kit' ) );
	}

	$creds = array(
		'user_login'    => isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '',
		'user_password' => isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '',
		'remember'      => isset( $_POST['rememberme'] ),
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		identity_security_kit_redirect( 'login', array( 'login' => 'failed' ) );
	}

	if ( current_user_can( 'photovault_manage_media' ) || current_user_can( 'manage_options' ) ) {
		identity_security_kit_redirect( 'dashboard' );
	}

	identity_security_kit_redirect( 'after_login' );
}
add_action( 'template_redirect', 'identity_security_kit_handle_login' );

/**
 * Handle front-office registration submissions.
 */
function identity_security_kit_handle_registration() {
	if ( ! identity_security_kit_is_post_request() || ! isset( $_POST['photovault_register_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_register_nonce'] ) ), 'photovault_register_action' ) ) {
		wp_die( esc_html__( 'Security verification failed.', 'identity-security-kit' ) );
	}

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$username   = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$password_c = isset( $_POST['password_confirm'] ) ? (string) wp_unslash( $_POST['password_confirm'] ) : '';

	$error_code = '';

	if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
		$error_code = 'fields_required';
	} elseif ( ! is_email( $email ) ) {
		$error_code = 'invalid_email';
	} elseif ( strlen( $password ) < 8 ) {
		$error_code = 'weak_password';
	} elseif ( $password !== $password_c ) {
		$error_code = 'password_mismatch';
	} elseif ( email_exists( $email ) ) {
		$error_code = 'email_exists';
	} elseif ( username_exists( $username ) ) {
		$error_code = 'username_exists';
	}

	if ( ! empty( $error_code ) ) {
		identity_security_kit_redirect( 'register', array( 'register' => 'failed', 'err' => $error_code ) );
	}

	$user_id = wp_insert_user(
		array(
			'user_login' => $username,
			'user_email' => $email,
			'user_pass'  => $password,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'role'       => identity_security_kit_get_registration_role(),
		)
	);

	if ( is_wp_error( $user_id ) ) {
		identity_security_kit_redirect( 'register', array( 'register' => 'failed', 'err' => 'failed' ) );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	identity_security_kit_redirect( 'after_login' );
}
add_action( 'template_redirect', 'identity_security_kit_handle_registration' );

/**
 * Handle front-office profile updates.
 */
function identity_security_kit_handle_profile_update() {
	if ( ! identity_security_kit_is_post_request() || ! isset( $_POST['photovault_profile_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_profile_nonce'] ) ), 'photovault_profile_action' ) ) {
		wp_die( esc_html__( 'Security verification failed.', 'identity-security-kit' ) );
	}

	if ( ! is_user_logged_in() ) {
		identity_security_kit_redirect( 'login' );
	}

	$current_user_id = get_current_user_id();
	$current_user    = wp_get_current_user();
	$email           = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$bio             = isset( $_POST['bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		identity_security_kit_redirect( 'profile', array( 'profile' => 'invalid_email' ) );
	}

	$email_owner = email_exists( $email );
	if ( $email_owner && (int) $email_owner !== (int) $current_user_id ) {
		identity_security_kit_redirect( 'profile', array( 'profile' => 'email_exists' ) );
	}

	$user_data = array(
		'ID'          => $current_user_id,
		'user_email'  => $email,
		'description' => $bio,
	);

	if ( ! empty( $_POST['password'] ) || ! empty( $_POST['password_confirm'] ) ) {
		$current_password = isset( $_POST['current_password'] ) ? (string) wp_unslash( $_POST['current_password'] ) : '';
		$password         = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
		$password_c       = isset( $_POST['password_confirm'] ) ? (string) wp_unslash( $_POST['password_confirm'] ) : '';

		if ( empty( $current_password ) || ! wp_check_password( $current_password, $current_user->user_pass, $current_user_id ) ) {
			identity_security_kit_redirect( 'profile', array( 'profile' => 'current_password_invalid' ) );
		}

		if ( strlen( $password ) < 8 ) {
			identity_security_kit_redirect( 'profile', array( 'profile' => 'weak_password' ) );
		}

		if ( $password !== $password_c ) {
			identity_security_kit_redirect( 'profile', array( 'profile' => 'pwd_mismatch' ) );
		}

		$user_data['user_pass'] = $password;
	}

	if ( ! empty( $_FILES['profile_avatar']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$validation = identity_security_kit_validate_uploaded_image_file( $_FILES['profile_avatar'] );
		if ( is_wp_error( $validation ) ) {
			identity_security_kit_redirect( 'profile', array( 'profile' => sanitize_key( $validation->get_error_code() ) ) );
		}

		$attachment_id = media_handle_upload(
			'profile_avatar',
			0,
			array(),
			array( 'mimes' => identity_security_kit_get_allowed_image_mimes() )
		);

		if ( is_wp_error( $attachment_id ) ) {
			identity_security_kit_redirect( 'profile', array( 'profile' => 'avatar_upload_failed' ) );
		}

		$avatar_meta_key = sanitize_key( apply_filters( 'identity_security_kit_avatar_meta_key', 'photovault_avatar_id' ) );
		update_user_meta( $current_user_id, $avatar_meta_key, $attachment_id );
	}

	$result = wp_update_user( $user_data );
	if ( is_wp_error( $result ) ) {
		identity_security_kit_redirect( 'profile', array( 'profile' => 'failed' ) );
	}

	identity_security_kit_redirect( 'profile', array( 'profile' => 'success' ) );
}
add_action( 'template_redirect', 'identity_security_kit_handle_profile_update' );