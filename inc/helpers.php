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
	if ( is_singular( 'media_item' ) ) {
		$media_id = get_the_ID();
		$is_protected = get_post_meta( $media_id, 'is_protected', true ) === '1';

		if ( $is_protected ) {
			?>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					// Bloquer le clic droit sur tout le document
					document.addEventListener('contextmenu', function(e) {
						e.preventDefault();
						alert("Ce média est protégé par PhotoVault. Le téléchargement direct et le clic droit sont désactivés.");
					}, false);

					// Bloquer les raccourcis de sauvegarde et de capture
					document.addEventListener('keydown', function(e) {
						// Ctrl+S, Ctrl+U, Ctrl+Shift+I, F12
						if ( (e.ctrlKey && (e.key === 's' || e.key === 'u' || e.key === 'c')) || 
							 (e.ctrlKey && e.shiftKey && (e.key === 'i' || e.key === 'I' || e.key === 'j' || e.key === 'J')) || 
							 e.key === 'F12' ) {
							e.preventDefault();
							alert("Raccourci désactivé pour la sécurité du média.");
						}
					});

					// Empêcher le drag & drop des images
					const images = document.querySelectorAll('img');
					images.forEach(img => {
						img.addEventListener('dragstart', function(e) {
							e.preventDefault();
						});
					});
				});
			</script>
			<?php
		}
	}
}
add_action( 'wp_footer', 'photovault_inject_protection_script' );
