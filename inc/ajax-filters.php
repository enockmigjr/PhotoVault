<?php
/**
 * Moteur de recherche et de filtrage via l'API REST WordPress pour PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function photovault_register_rest_routes() {
	register_rest_route( 'photovault/v1', '/media', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'photovault_get_filtered_media',
		'permission_callback' => 'is_user_logged_in',
	) );
	register_rest_route( 'photovault/v1', '/secure-image', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'photovault_serve_secure_image',
		'permission_callback' => 'is_user_logged_in',
	) );
}
add_action( 'rest_api_init', 'photovault_register_rest_routes' );

function photovault_get_filtered_media( $request ) {
	$params = $request->get_params();
	$args = array(
		'post_type'      => 'media_item',
		'post_status'    => array( 'publish' ),
		'posts_per_page' => 12,
		'paged'          => ! empty( $params['page'] ) ? intval( $params['page'] ) : 1,
	);

	if ( is_user_logged_in() ) {
		$current_user_id = get_current_user_id();
		$args['post_status'] = array( 'publish', 'private' );
		if ( ! empty( $params['my_media'] ) && '1' === $params['my_media'] ) {
			$args['author'] = $current_user_id;
		}
	}

	if ( ! empty( $params['search'] ) ) {
		$args['s'] = sanitize_text_field( $params['search'] );
	}

	$tax_query = array( 'relation' => 'AND' );
	foreach ( array( 'folder' => 'media_folder', 'category' => 'media_category' ) as $param => $tax ) {
		if ( ! empty( $params[ $param ] ) ) {
			$tax_query[] = array( 'taxonomy' => $tax, 'field' => 'term_id', 'terms' => intval( $params[ $param ] ) );
		}
	}
	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query;
	}

	if ( ! empty( $params['author_id'] ) ) {
		$args['author'] = intval( $params['author_id'] );
	}

	foreach ( array( 'year', 'month' ) as $date_field ) {
		if ( ! empty( $params[ $date_field ] ) ) {
			$args['date_query'][] = array( $date_field => intval( $params[ $date_field ] ) );
		}
	}

	if ( isset( $params['protected'] ) && '' !== $params['protected'] ) {
		$args['meta_query'] = array( array( 'key' => 'is_protected', 'value' => sanitize_text_field( $params['protected'] ), 'compare' => '=' ) );
	}

	if ( ! empty( $params['orderby'] ) ) {
		$orderby = sanitize_text_field( $params['orderby'] );
		$args['orderby'] = ( 'alphabetical' === $orderby ) ? 'title' : 'date';
		$args['order']   = ( 'alphabetical' === $orderby || 'date_asc' === $orderby ) ? 'ASC' : 'DESC';
	}

	$query = new WP_Query( $args );
	$results = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			if ( 'private' === get_post_status() && get_the_author_meta( 'ID' ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				continue;
			}
			$media_id = get_the_ID();
			ob_start();
			get_template_part( 'templates/media-card' );
			$html = ob_get_clean();

			$results[] = array(
				'id'          => $media_id,
				'title'       => get_the_title(),
				'url'         => get_permalink(),
				'image'       => home_url( '/wp-json/photovault/v1/secure-image?id=' . $media_id ),
				'author'      => get_the_author(),
				'is_protected'=> get_post_meta( $media_id, 'is_protected', true ) === '1',
				'is_private'  => 'private' === get_post_status(),
				'html'        => $html,
			);
		}
		wp_reset_postdata();
	}

	return new WP_REST_Response( array( 'success' => true, 'data' => $results, 'pages' => $query->max_num_pages ), 200 );
}

function photovault_serve_secure_image( $request ) {
	$media_id = intval( $request->get_param( 'id' ) );
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'forbidden', 'Connexion requise.', array( 'status' => 401 ) );
	}
	$post = get_post( $media_id );
	if ( ! $post || 'media_item' !== $post->post_type ) {
		return new WP_Error( 'not_found', 'Média introuvable.', array( 'status' => 404 ) );
	}
	if ( 'private' === $post->post_status && intval( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'forbidden', 'Accès interdit.', array( 'status' => 403 ) );
	}
	$thumb_id = get_post_thumbnail_id( $media_id );
	$filepath = $thumb_id ? get_attached_file( $thumb_id ) : '';
	if ( ! $filepath || ! file_exists( $filepath ) ) {
		return new WP_Error( 'not_found', 'Fichier introuvable.', array( 'status' => 404 ) );
	}
	$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
	$mime = get_post_mime_type( $thumb_id );
	header( 'Content-Type: ' . $mime );

	if ( $is_protected && function_exists( 'imagecreatefromstring' ) ) {
		$img = imagecreatefromstring( file_get_contents( $filepath ) );
		if ( $img ) {
			$col = imagecolorallocatealpha( $img, 255, 255, 255, 90 );
			$w = imagesx( $img );
			$h = imagesy( $img );
			for ( $x = 20; $x < $w; $x += 250 ) {
				for ( $y = 30; $y < $h; $y += 200 ) {
					imagestring( $img, 5, $x, $y, "PHOTOVAULT PROTECTED", $col );
				}
			}
			if ( 'image/png' === $mime ) { imagepng( $img ); } else { imagejpeg( $img ); }
			imagedestroy( $img );
			exit;
		}
	}
	readfile( $filepath );
	exit;
}


