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
		'permission_callback' => '__return_true', // Géré finement en PHP pour éviter les blocages de nonces CORS/REST sur <img>
	) );
}
add_action( 'rest_api_init', 'photovault_register_rest_routes' );

function photovault_get_filtered_media( $request ) {
	$params = $request->get_params();
	$args = array(
		'post_type'      => 'media_item',
		'posts_per_page' => 12,
		'paged'          => ! empty( $params['page'] ) ? intval( $params['page'] ) : 1,
	);

	// Seul l'administrateur peut voir les médias privés
	if ( current_user_can( 'manage_options' ) ) {
		$args['post_status'] = array( 'publish', 'private' );
	} else {
		$args['post_status'] = array( 'publish' );
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
			
			// Sécurité secondaire : exclure les privés si l'utilisateur n'est pas admin
			if ( 'private' === get_post_status() && ! current_user_can( 'manage_options' ) ) {
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
	// Authentification par cookie de secours pour les requêtes standard GET (ex: <img> ou téléchargement direct)
	if ( 0 === get_current_user_id() ) {
		$cookie_user_id = wp_validate_auth_cookie( '', 'logged_in' );
		if ( $cookie_user_id ) {
			wp_set_current_user( $cookie_user_id );
		}
	}

	$media_id = intval( $request->get_param( 'id' ) );
	$post = get_post( $media_id );
	if ( ! $post || 'media_item' !== $post->post_type ) {
		return new WP_Error( 'not_found', 'Média introuvable.', array( 'status' => 404 ) );
	}

	$is_private = 'private' === $post->post_status;
	$is_admin   = current_user_can( 'manage_options' );
	$is_owner   = is_user_logged_in() && (int) $post->post_author === get_current_user_id();

	// 1. Restriction d'accès stricte sur média privé
	if ( $is_private && ! $is_admin && ! $is_owner ) {
		return new WP_Error( 'forbidden', 'Accès interdit.', array( 'status' => 403 ) );
	}

	$thumb_id = get_post_thumbnail_id( $media_id );
	$filepath = $thumb_id ? get_attached_file( $thumb_id ) : '';
	if ( ! $filepath || ! file_exists( $filepath ) ) {
		return new WP_Error( 'not_found', 'Fichier introuvable.', array( 'status' => 404 ) );
	}

	$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
	$mime = get_post_mime_type( $thumb_id );

	// CAS A : Force le téléchargement si le paramètre download=1 est spécifié
	if ( $request->get_param( 'download' ) === '1' ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'unauthorized', 'Vous devez être connecté pour télécharger des médias.', array( 'status' => 401 ) );
		}
		
		// Protection stricte : interdire le téléchargement si le média est protégé (sauf admins et propriétaires)
		if ( $is_protected && ! $is_admin && ! $is_owner ) {
			return new WP_Error( 'forbidden', 'Téléchargement interdit sur un média protégé.', array( 'status' => 403 ) );
		}

		// Enregistrer le téléchargement
		$downloads = (int) get_post_meta( $media_id, 'photovault_downloads_count', true );
		update_post_meta( $media_id, 'photovault_downloads_count', $downloads + 1 );

		// Envoyer les en-têtes et servir le fichier brut d'origine
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: ' . $mime );
		header( 'Content-Disposition: attachment; filename="' . basename( $filepath ) . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $filepath ) );
		
		ob_clean();
		flush();
		readfile( $filepath );
		exit;
	}

	// CAS B : Affichage normal
	// Incrémenter le nombre de vues
	if ( ! $is_admin ) {
		$views = (int) get_post_meta( $media_id, 'photovault_views_count', true );
		update_post_meta( $media_id, 'photovault_views_count', $views + 1 );
	}

	header( 'Content-Type: ' . $mime );

	// Si protégé et que l'utilisateur n'est ni admin ni propriétaire : superposer le filigrane
	if ( $is_protected && ! $is_admin && ! $is_owner ) {
		$filesize = filesize( $filepath );
		if ( $filesize <= 20 * 1024 * 1024 && function_exists( 'imagecreatefromstring' ) ) {
			$img = null;
			if ( 'image/jpeg' === $mime || 'image/jpg' === $mime ) {
				if ( function_exists( 'imagecreatefromjpeg' ) ) {
					$img = @imagecreatefromjpeg( $filepath );
				}
			} elseif ( 'image/png' === $mime ) {
				if ( function_exists( 'imagecreatefrompng' ) ) {
					$img = @imagecreatefrompng( $filepath );
				}
			} elseif ( 'image/webp' === $mime ) {
				if ( function_exists( 'imagecreatefromwebp' ) ) {
					$img = @imagecreatefromwebp( $filepath );
				}
			}

			if ( $img ) {
				$watermark_text = get_option( 'photovault_watermark_text', 'PHOTOVAULT' );
				$col = imagecolorallocatealpha( $img, 255, 255, 255, 80 );
				$w = imagesx( $img );
				$h = imagesy( $img );
				
				// Répéter le filigrane de protection
				for ( $x = 40; $x < $w; $x += 350 ) {
					for ( $y = 50; $y < $h; $y += 280 ) {
						imagestring( $img, 5, $x, $y, $watermark_text, $col );
					}
				}

				if ( 'image/png' === $mime ) {
					imagepng( $img );
				} elseif ( 'image/webp' === $mime ) {
					imagewebp( $img );
				} else {
					imagejpeg( $img );
				}
				imagedestroy( $img );
				exit;
			}
		}
	}

	readfile( $filepath );
	exit;
}


