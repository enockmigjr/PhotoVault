<?php
/**
 * Custom Post Types du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistrement du Custom Post Type 'media_item'.
 */
function photovault_register_post_types() {
	$labels = array(
		'name'               => _x( 'Médias', 'post type general name', 'photovault' ),
		'singular_name'      => _x( 'Média', 'post type singular name', 'photovault' ),
		'menu_name'          => _x( 'PhotoVault', 'admin menu', 'photovault' ),
		'name_admin_bar'     => _x( 'Média', 'add new on admin bar', 'photovault' ),
		'add_new'            => _x( 'Ajouter Nouveau', 'media_item', 'photovault' ),
		'add_new_item'       => __( 'Ajouter un nouveau média', 'photovault' ),
		'new_item'           => __( 'Nouveau média', 'photovault' ),
		'edit_item'          => __( 'Modifier le média', 'photovault' ),
		'view_item'          => __( 'Voir le média', 'photovault' ),
		'all_items'          => __( 'Tous les médias', 'photovault' ),
		'search_items'       => __( 'Rechercher des médias', 'photovault' ),
		'parent_item_colon'  => __( 'Médias parents :', 'photovault' ),
		'not_found'          => __( 'Aucun média trouvé.', 'photovault' ),
		'not_found_in_trash' => __( 'Aucun média trouvé dans la corbeille.', 'photovault' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'gallery', 'with_front' => false ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-images-alt2',
		'show_in_rest'       => true, // Essentiel pour l'API REST
		'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
		'capability_type'     => array( 'media_item', 'media_items' ),
		'map_meta_cap'        => true, // Laisse WP mapper automatiquement les capacités
	);

	register_post_type( 'media_item', $args );
}
add_action( 'init', 'photovault_register_post_types' );
