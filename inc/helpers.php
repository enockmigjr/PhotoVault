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
function photovault_get_photographer_stats( $user_id ) {
	$stats = array(
		'total'     => 0,
		'public'    => 0,
		'private'   => 0,
		'protected' => 0,
		'folders'   => 0,
	);

	// Compter le nombre de dossiers créés (ou total du système pour simplifier).
	$folders = get_terms( array(
		'taxonomy'   => 'media_folder',
		'hide_empty' => false,
	) );
	$stats['folders'] = ! is_wp_error( $folders ) ? count( $folders ) : 0;

	// Nombre total de médias (publics et privés).
	$args_all = array(
		'post_type'      => 'media_item',
		'post_status'    => array( 'publish', 'private' ),
		'author'         => $user_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);
	$query_all = new WP_Query( $args_all );
	$stats['total'] = $query_all->post_count;

	// Nombre de médias privés.
	$args_private = array(
		'post_type'      => 'media_item',
		'post_status'    => 'private',
		'author'         => $user_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);
	$query_private = new WP_Query( $args_private );
	$stats['private'] = $query_private->post_count;

	// Nombre de médias publics.
	$stats['public'] = $stats['total'] - $stats['private'];

	// Nombre de médias protégés.
	$args_protected = array(
		'post_type'      => 'media_item',
		'post_status'    => array( 'publish', 'private' ),
		'author'         => $user_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'is_protected',
				'value'   => '1',
				'compare' => '=',
			),
		),
	);
	$query_protected = new WP_Query( $args_protected );
	$stats['protected'] = $query_protected->post_count;

	return $stats;
}

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
