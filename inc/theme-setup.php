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
	add_theme_support( 'custom-logo', array( 'height' => 96, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'photovault-card', 400, 400, true );
	add_image_size( 'photovault-preview', 1600, 1600, false );

	register_nav_menus( array(
		'primary' => esc_html__( 'Menu Principal', 'photovault' ),
		'dashboard' => esc_html__( 'Menu Dashboard', 'photovault' ),
		'footer_explore' => esc_html__( 'Pied de page : Explorer', 'photovault' ),
		'footer_services' => esc_html__( 'Pied de page : Services', 'photovault' ),
		'footer_information' => esc_html__( 'Pied de page : Informations', 'photovault' ),
	) );
}
add_action( 'after_setup_theme', 'photovault_setup' );

/**
 * Enqueue des scripts et styles.
 */
function photovault_scripts() {
	// Google Fonts: Outfit & Inter.
	wp_enqueue_style( 'photovault-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap', array(), null );

	// Tailwind is compiled locally and committed for a zero-runtime production frontend.
	$tailwind_version = file_exists( PHOTOVAULT_DIR . '/css/tailwind.css' ) ? (string) filemtime( PHOTOVAULT_DIR . '/css/tailwind.css' ) : PHOTOVAULT_VERSION;
	wp_enqueue_style( 'photovault-tailwind', PHOTOVAULT_URI . '/css/tailwind.css', array(), $tailwind_version );

	// Style CSS custom du thème.
	$style_version = file_exists( PHOTOVAULT_DIR . '/css/main.css' ) ? (string) filemtime( PHOTOVAULT_DIR . '/css/main.css' ) : PHOTOVAULT_VERSION;
	wp_enqueue_style( 'photovault-style', PHOTOVAULT_URI . '/css/main.css', array( 'photovault-tailwind' ), $style_version );

	// Script JS principal global.
	$script_version = file_exists( PHOTOVAULT_DIR . '/js/main.js' ) ? (string) filemtime( PHOTOVAULT_DIR . '/js/main.js' ) : PHOTOVAULT_VERSION;
	wp_enqueue_script( 'photovault-main-js', PHOTOVAULT_URI . '/js/main.js', array(), $script_version, true );
	
	// Configuration REST disponible avant le script principal et les scripts inline.
	$photovault_frontend_config = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'rest_url' => esc_url_raw( rest_url( 'photovault/v1' ) ),
		'nonce'    => wp_create_nonce( 'wp_rest' ),
	);
	wp_add_inline_script(
		'photovault-main-js',
		'window.photovault_ajax = window.photovault_ajax || ' . wp_json_encode( $photovault_frontend_config ) . ';',
		'before'
	);
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
		'reset-password'  => array( 'title' => 'Nouveau mot de passe', 'template' => 'page-reset-password.php' ),
		'dashboard'       => array( 'title' => 'Dashboard', 'template' => 'page-dashboard.php' ),
		'profile'         => array( 'title' => 'Profil', 'template' => 'page-profile.php' ),
		'booking'         => array( 'title' => 'Reserver un shooting', 'template' => 'page-booking.php' ),
		'pricing'         => array( 'title' => 'Tarifs', 'template' => 'page-pricing.php' ),
		'about'           => array( 'title' => 'À Propos', 'template' => 'page-about.php' ),
		'contact'         => array( 'title' => 'Contact', 'template' => 'page-contact.php' ),
		'fonctionnalites' => array( 'title' => 'Fonctionnalites', 'template' => 'page-features.php' ),
		'journal'         => array( 'title' => 'Carnets visuels', 'template' => 'page-journal.php' ),
	);

	foreach ( $pages_to_create as $slug => $data ) {
		$page_check = get_page_by_path( $slug );
		if ( ! $page_check instanceof WP_Post ) {
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
		} elseif ( ! empty( $data['template'] ) && get_page_template_slug( $page_check->ID ) !== $data['template'] ) {
			update_post_meta( $page_check->ID, '_wp_page_template', $data['template'] );
		}
	}

	update_option( 'photovault_app_pages_version', '1.4.0', false );
}
add_action( 'after_switch_theme', 'photovault_create_app_pages' );

/** Provision newly introduced system pages on already active installations. */
function photovault_maybe_create_app_pages() {
	if ( '1.4.0' !== get_option( 'photovault_app_pages_version' ) ) {
		photovault_create_app_pages();
	}
}
add_action( 'init', 'photovault_maybe_create_app_pages', 20 );
