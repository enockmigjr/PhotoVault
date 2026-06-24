<?php
/**
 * Configuration initiale du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configuration de base du thème.
 */
function photovault_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'primary' => esc_html__( 'Menu Principal', 'photovault' ),
		'dashboard' => esc_html__( 'Menu Dashboard', 'photovault' ),
	) );
}
add_action( 'after_setup_theme', 'photovault_setup' );

/**
 * Enqueue des scripts et styles.
 */
function photovault_scripts() {
	// Google Fonts: Outfit & Inter.
	wp_enqueue_style( 'photovault-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap', array(), null );

	// Tailwind CSS v4 CDN.
	wp_enqueue_script( 'photovault-tailwind', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', array(), null, false );

	// Style CSS custom du thème.
	wp_enqueue_style( 'photovault-style', PHOTOVAULT_URI . '/css/main.css', array(), PHOTOVAULT_VERSION );

	// Script JS principal global.
	wp_enqueue_script( 'photovault-main-js', PHOTOVAULT_URI . '/js/main.js', array(), PHOTOVAULT_VERSION, true );
	
	// Support d'AJAX pour le JS (localisé sur notre script principal toujours chargé).
	wp_localize_script( 'photovault-main-js', 'photovault_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'rest_url' => esc_url_raw( rest_url( 'photovault/v1' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'photovault_scripts' );

/**
 * Création automatique des pages de base du thème à son activation.
 */
function photovault_create_app_pages() {
	$pages_to_create = array(
		'login'           => array( 'title' => 'Connexion', 'template' => 'page-login.php' ),
		'register'        => array( 'title' => 'Inscription', 'template' => 'page-register.php' ),
		'forgot-password' => array( 'title' => 'Mot de passe oublié', 'template' => 'page-forgot-password.php' ),
		'dashboard'       => array( 'title' => 'Dashboard', 'template' => 'page-dashboard.php' ),
		'pricing'         => array( 'title' => 'Tarifs', 'template' => 'page-pricing.php' ),
		'about'           => array( 'title' => 'À Propos', 'template' => 'page-about.php' ),
		'contact'         => array( 'title' => 'Contact', 'template' => 'page-contact.php' ),
	);

	foreach ( $pages_to_create as $slug => $data ) {
		$page_check = get_page_by_path( $slug );
		if ( ! isset( $page_check->ID ) ) {
			$page_id = wp_insert_post( array(
				'post_title'    => $data['title'],
				'post_content'  => '',
				'post_status'   => 'publish',
				'post_type'     => 'page',
				'post_name'     => $slug,
			) );

			if ( $page_id && ! is_wp_error( $page_id ) && ! empty( $data['template'] ) ) {
				update_post_meta( $page_id, '_wp_page_template', $data['template'] );
			}
		}
	}
}
add_action( 'after_switch_theme', 'photovault_create_app_pages' );
