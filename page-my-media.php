<?php
/**
 * Template Name: PhotoVault My Media
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/login/' ) );
	exit;
}

$current_user_id = get_current_user_id();
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$search_query = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

$args = array(
	'post_type'      => 'media_item',
	'post_status'    => array( 'publish', 'private' ),
	'author'         => $current_user_id,
	'posts_per_page' => 12,
	'paged'          => $paged,
	's'              => $search_query
);

$query = new WP_Query( $args );
get_header();
?>

<div class="flex min-h-screen bg-[#0b0f19]">
	<!-- Barre latérale -->
	<?php get_template_part( 'templates/dashboard-sidebar' ); ?>

	<!-- Contenu Principal -->
	<main class="flex-1 p-10 overflow-y-auto">
		<div class="max-w-6xl mx-auto">
			<header class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
				<div>
					<h2 class="text-3xl font-extrabold text-white">Ma Médiathèque</h2>
					<p class="text-gray-400 mt-1">Gérez, éditez et supprimez vos médias téléversés.</p>
				</div>
				<a href="<?php echo esc_url( home_url( '/upload-media/' ) ); ?>" class="mt-4 md:mt-0 px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all cursor-pointer flex items-center">
					<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
					Ajouter un média
				</a>
			</header>

			<!-- Barre de recherche interne -->
			<div class="mb-6 flex justify-between items-center gap-4">
				<form method="GET" class="relative flex-1 max-w-md">
					<input type="text" name="search" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Rechercher parmi mes médias..." class="w-full pl-10 pr-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
					<svg class="absolute left-3 top-3.5 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
				</form>
			</div>

			<!-- Liste / Grille de Médias -->
			<?php if ( $query->have_posts() ) : ?>
				<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
					<?php while ( $query->have_posts() ) : $query->the_post(); 
						$media_id = get_the_ID();
						$is_protected = get_post_meta( $media_id, 'is_protected', true );
						$is_private = 'private' === get_post_status( $media_id );
						$delete_url = add_query_arg( array( 'action' => 'delete_media', 'media_id' => $media_id, '_wpnonce' => wp_create_nonce( 'delete_media_' . $media_id ) ), home_url( '/my-media/' ) );
					?>
						<div class="glass-effect rounded-2xl overflow-hidden shadow-lg transition-all-300 hover:scale-[1.02] border border-gray-800/80 group">
							<div class="relative aspect-square bg-gray-950 overflow-hidden">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover transition-all duration-500 group-hover:scale-105' ) ); ?>
								<?php endif; ?>
								
								<!-- Badges -->
								<div class="absolute top-3 left-3 flex flex-col gap-2 z-20">
									<?php if ( $is_protected ) : ?>
										<span class="bg-indigo-600 text-white text-xs font-semibold px-2.5 py-1 rounded-full flex items-center shadow-lg border border-indigo-400/30">🔒 Protégé</span>
									<?php endif; ?>
									<?php if ( $is_private ) : ?>
										<span class="bg-gray-800 text-gray-300 text-xs font-semibold px-2.5 py-1 rounded-full flex items-center shadow-lg border border-gray-700">👁️ Privé</span>
									<?php endif; ?>
								</div>
							</div>
							<div class="p-4 flex flex-col justify-between">
								<h3 class="text-sm font-semibold text-white truncate"><?php the_title(); ?></h3>
								<div class="mt-4 flex items-center justify-between text-xs border-t border-gray-800/80 pt-3">
									<a href="<?php the_permalink(); ?>" class="text-indigo-400 hover:text-indigo-300 font-medium">Voir</a>
									<a href="<?php echo esc_url( $delete_url ); ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce média ?');" class="text-red-400 hover:text-red-300 font-medium cursor-pointer">Supprimer</a>
								</div>
							</div>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>

				<!-- Pagination -->
				<div class="mt-10 flex justify-center">
					<?php 
					echo paginate_links( array(
						'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
						'format'    => '?paged=%#%',
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $query->max_num_pages,
						'prev_text' => '&larr; Précédent',
						'next_text' => 'Suivant &rarr;',
						'type'      => 'list',
						'class'     => 'pagination'
					) );
					?>
				</div>

			<?php else : ?>
				<div class="text-center py-16 glass-effect rounded-2xl">
					<svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
					<h3 class="mt-4 text-lg font-semibold text-white">Aucun média trouvé</h3>
					<p class="mt-1 text-sm text-gray-500">Commencez par téléverser votre première photo dans votre médiathèque.</p>
				</div>
			<?php endif; ?>
		</div>
	</main>
</div>

<?php get_footer(); ?>
