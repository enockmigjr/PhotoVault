<?php
/**
 * Rôles et permissions du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Création et configuration du rôle "Photographe" et "Client" à l'activation du thème.
 */
function photovault_register_roles() {
	// Supprimer le rôle s'il existe déjà pour éviter les duplications.
	remove_role( 'photographer' );
	remove_role( 'client' );

	// Ajouter le rôle photographe.
	add_role( 'photographer', esc_html__( 'Photographe', 'photovault' ), array(
		'read'                      => true,
		'upload_files'              => true,
		'publish_posts'             => false,
		'edit_posts'                => false,
		'delete_posts'              => false,
		'edit_media_item'           => true,
		'read_media_item'           => true,
		'delete_media_item'         => true,
		'edit_media_items'          => true,
		'edit_others_media_items'   => false,
		'publish_media_items'       => true,
		'read_private_media_items'  => true,
		'delete_media_items'        => true,
		'delete_private_media_items'=> true,
		'delete_published_media_items'=> true,
		'delete_others_media_items' => false,
		'edit_private_media_items'  => true,
		'edit_published_media_items'=> true,
	) );

	// Ajouter le rôle client.
	add_role( 'client', esc_html__( 'Client / Visiteur', 'photovault' ), array(
		'read'                      => true,
		'upload_files'              => false,
		'publish_posts'             => false,
		'edit_posts'                => false,
		'delete_posts'              => false,
	) );
}
add_action( 'after_switch_theme', 'photovault_register_roles' );

/**
 * Bloquer l'accès à wp-admin et rediriger les photographes/clients vers leur dashboard/accueil.
 */
function photovault_restrict_admin_access() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( in_array( 'photographer', (array) $user->roles ) ) {
			wp_safe_redirect( home_url( '/dashboard/' ) );
			exit;
		}
		if ( in_array( 'client', (array) $user->roles ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}
}
add_action( 'admin_init', 'photovault_restrict_admin_access' );

/**
 * Restreindre l'accès aux galeries et médias pour les utilisateurs anonymes.
 */
function photovault_enforce_login_for_media() {
	if ( ! is_user_logged_in() ) {
		if ( is_post_type_archive( 'media_item' ) || is_singular( 'media_item' ) || is_tax( 'media_folder' ) || is_tax( 'media_category' ) ) {
			wp_safe_redirect( home_url( '/login/' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'photovault_enforce_login_for_media' );

/**
 * Redirection de wp-login.php si l'utilisateur essaie de s'y connecter directement.
 */
function photovault_redirect_login_page() {
	global $pagenow;
	
	if ( 'wp-login.php' === $pagenow && ! isset( $_GET['action'] ) && $_SERVER['REQUEST_METHOD'] === 'GET' ) {
		wp_safe_redirect( home_url( '/login/' ) );
		exit;
	}
}
add_action( 'init', 'photovault_redirect_login_page' );

/**
 * Cacher la barre d'administration pour les photographes, clients et visiteurs.
 */
function photovault_hide_admin_bar() {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( in_array( 'photographer', (array) $user->roles ) || in_array( 'client', (array) $user->roles ) ) {
			show_admin_bar( false );
		}
	} else {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'photovault_hide_admin_bar' );

