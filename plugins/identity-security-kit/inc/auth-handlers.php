<?php
/**
 * Authentication and profile handlers for Identity Security Kit.
 *
 * @package IdentitySecurityKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function photovault_handle_login() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['photovault_login_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_login_nonce'] ) ), 'photovault_login_action' ) ) {
		wp_die( esc_html__( 'Echec de la verification de securite.', 'photovault' ) );
	}

	$creds = array(
		'user_login'    => isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '',
		'user_password' => isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '',
		'remember'      => isset( $_POST['rememberme'] ),
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		wp_safe_redirect( add_query_arg( 'login', 'failed', home_url( '/login/' ) ) );
		exit;
	}

	if ( function_exists( 'photovault_current_user_can' ) && photovault_current_user_can( 'photovault_manage_media' ) ) {
		wp_safe_redirect( home_url( '/dashboard/' ) );
	} else {
		wp_safe_redirect( get_post_type_archive_link( 'media_item' ) );
	}
	exit;
}
add_action( 'template_redirect', 'photovault_handle_login' );

function photovault_handle_registration() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['photovault_register_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_register_nonce'] ) ), 'photovault_register_action' ) ) {
		wp_die( esc_html__( 'Echec de la verification de securite.', 'photovault' ) );
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
		wp_safe_redirect( add_query_arg( array( 'register' => 'failed', 'err' => $error_code ), home_url( '/register/' ) ) );
		exit;
	}

	$user_id = wp_insert_user( array(
		'user_login' => $username,
		'user_email' => $email,
		'user_pass'  => $password,
		'first_name' => $first_name,
		'last_name'  => $last_name,
		'role'       => 'client',
	) );

	if ( is_wp_error( $user_id ) ) {
		wp_safe_redirect( add_query_arg( array( 'register' => 'failed', 'err' => 'failed' ), home_url( '/register/' ) ) );
		exit;
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id );
	wp_safe_redirect( get_post_type_archive_link( 'media_item' ) );
	exit;
}
add_action( 'template_redirect', 'photovault_handle_registration' );

function photovault_handle_profile_update() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['photovault_profile_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_profile_nonce'] ) ), 'photovault_profile_action' ) ) {
		wp_die( esc_html__( 'Echec de la verification de securite.', 'photovault' ) );
	}

	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( home_url( '/login/' ) );
		exit;
	}

	$current_user_id = get_current_user_id();
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$bio   = isset( $_POST['bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'profile', 'invalid_email', home_url( '/profile/' ) ) );
		exit;
	}

	$user_data = array(
		'ID'          => $current_user_id,
		'user_email'  => $email,
		'description' => $bio,
	);

	if ( ! empty( $_POST['password'] ) || ! empty( $_POST['password_confirm'] ) ) {
		$password   = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
		$password_c = isset( $_POST['password_confirm'] ) ? (string) wp_unslash( $_POST['password_confirm'] ) : '';

		if ( strlen( $password ) < 8 ) {
			wp_safe_redirect( add_query_arg( 'profile', 'weak_password', home_url( '/profile/' ) ) );
			exit;
		}

		if ( $password !== $password_c ) {
			wp_safe_redirect( add_query_arg( 'profile', 'pwd_mismatch', home_url( '/profile/' ) ) );
			exit;
		}

		$user_data['user_pass'] = $password;
	}

	if ( ! empty( $_FILES['profile_avatar']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		if ( function_exists( 'photovault_validate_uploaded_image_file' ) ) {
			$validation = photovault_validate_uploaded_image_file( $_FILES['profile_avatar'] );
			if ( is_wp_error( $validation ) ) {
				wp_safe_redirect( add_query_arg( 'profile', sanitize_key( $validation->get_error_code() ), home_url( '/profile/' ) ) );
				exit;
			}
		}

		$attachment_id = media_handle_upload( 'profile_avatar', 0, array(), array( 'mimes' => function_exists( 'photovault_get_allowed_image_mimes' ) ? photovault_get_allowed_image_mimes() : null ) );
		if ( ! is_wp_error( $attachment_id ) ) {
			update_user_meta( $current_user_id, 'photovault_avatar_id', $attachment_id );
		}
	}

	$result = wp_update_user( $user_data );
	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( 'profile', 'failed', home_url( '/profile/' ) ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( 'profile', 'success', home_url( '/profile/' ) ) );
	exit;
}
add_action( 'template_redirect', 'photovault_handle_profile_update' );