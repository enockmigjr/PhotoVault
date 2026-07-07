<?php
/**
 * Fonctions utilitaires, statistiques et système de protection des médias pour PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Récupérer les statistiques des médias d'un photographe (ou globales pour les administrateurs).
 *
 * @param int $user_id ID de l'utilisateur.
 * @return array Tableau de statistiques.
 */
function photovault_get_photographer_stats( $user_id = 0 ) {
	$cache_key = $user_id > 0 ? 'pv_stats_' . $user_id : 'pv_stats_global';
	$stats = get_transient( $cache_key );

	if ( false === $stats ) {
		$stats = array(
			'total'      => 0,
			'public'     => 0,
			'private'    => 0,
			'protected'  => 0,
			'folders'    => 0,
			'categories' => 0,
			'downloads'  => 0,
			'views'      => 0,
		);

		// Compter le nombre de dossiers
		$folders = get_terms( array(
			'taxonomy'   => 'media_folder',
			'hide_empty' => false,
		) );
		$stats['folders'] = ! is_wp_error( $folders ) ? count( $folders ) : 0;

		// Compter le nombre de catégories
		$categories = get_terms( array(
			'taxonomy'   => 'media_category',
			'hide_empty' => false,
		) );
		$stats['categories'] = ! is_wp_error( $categories ) ? count( $categories ) : 0;

		// Requête globale ou par utilisateur
		$args_all = array(
			'post_type'      => 'media_item',
			'post_status'    => array( 'publish', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		);
		
		// Si un ID utilisateur est spécifié et n'est pas un administrateur, on filtre par auteur
		if ( $user_id > 0 && ! user_can( $user_id, 'manage_options' ) ) {
			$args_all['author'] = $user_id;
		}
		
		$query_all = new WP_Query( $args_all );
		
		if ( $query_all->have_posts() ) {
			$stats['total'] = $query_all->post_count;
			
			foreach ( $query_all->posts as $pid ) {
				$status = get_post_status( $pid );
				if ( 'private' === $status ) {
					$stats['private']++;
				} else {
					$stats['public']++;
				}
				
				if ( get_post_meta( $pid, 'is_protected', true ) === '1' ) {
					$stats['protected']++;
				}
				
				$stats['downloads'] += (int) get_post_meta( $pid, 'photovault_downloads_count', true );
				$stats['views'] += (int) get_post_meta( $pid, 'photovault_views_count', true );
			}
		}
		
		set_transient( $cache_key, $stats, 300 ); // Cache pendant 5 minutes
	}

	return $stats;
}


/**
 * Afficher une illustration coherente lorsqu'un article n'a pas d'image a la une.
 */
function photovault_render_post_visual( $size = 'medium_large', $image_class = 'w-full h-full object-cover' ) {
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( $size, array( 'class' => $image_class ) );
		return;
	}
	?>
	<div class="pv-post-placeholder w-full h-full flex items-center justify-center">
		<div class="pv-post-placeholder__mark" aria-hidden="true">
			<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 7h3l2-2h6l2 2h3v12H4V7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 13a3 3 0 106 0 3 3 0 00-6 0z"></path></svg>
		</div>
		<div class="pv-post-placeholder__lines" aria-hidden="true"><span></span><span></span><span></span></div>
	</div>
	<?php
}
/**
 * Invalider le cache des statistiques sur sauvegarde, modification ou suppression de média.
 */
function photovault_clean_stats_cache( $post_id ) {
	if ( 'media_item' === get_post_type( $post_id ) ) {
		delete_transient( 'pv_stats_global' );
		$post = get_post( $post_id );
		if ( $post ) {
			delete_transient( 'pv_stats_' . $post->post_author );
		}
	}
}
add_action( 'save_post', 'photovault_clean_stats_cache' );
add_action( 'before_delete_post', 'photovault_clean_stats_cache' );

/**
 * Injecter le script JS de protection anti-téléchargement si nécessaire.
 */
function photovault_inject_protection_script() {
	if ( ! is_singular( 'media_item' ) ) {
		return;
	}

	$media_id = get_the_ID();
	$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';
	$post = get_post( $media_id );
	$is_admin = current_user_can( 'manage_options' );
	$is_owner = $post && is_user_logged_in() && (int) $post->post_author === get_current_user_id();

	if ( ! $is_protected || $is_admin || $is_owner ) {
		return;
	}
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			function notify(message) {
				if (window.PhotoVaultProtectionNotice) {
					window.PhotoVaultProtectionNotice(message);
				}
			}

			document.addEventListener('contextmenu', function(e) {
				e.preventDefault();
				notify('Ce media est protege par PhotoVault. Le clic droit et la sauvegarde directe sont desactives.');
			}, false);

			document.addEventListener('keydown', function(e) {
				if ( (e.ctrlKey && ['s', 'u', 'c'].includes(e.key.toLowerCase())) ||
					 (e.ctrlKey && e.shiftKey && ['i', 'j'].includes(e.key.toLowerCase())) ||
					 e.key === 'F12' ) {
					e.preventDefault();
					notify('Raccourci desactive pour proteger ce media.');
				}
			});

			document.querySelectorAll('img').forEach(function(img) {
				img.setAttribute('draggable', 'false');
				img.addEventListener('dragstart', function(e) {
					e.preventDefault();
					notify('Le glisser-deposer est desactive sur ce media protege.');
				});
			});
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'photovault_inject_protection_script' );
