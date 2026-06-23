<?php
/**
 * Template pour les archives de taxonomies personnalisées (Dossiers, Catégories) - taxonomy.php.
 *
 * @package PhotoVault
 */

get_header();

$term = get_queried_object();
?>

<div class="py-12 bg-[#0b0f19] min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<header class="mb-10 pb-6 border-b border-gray-900 flex justify-between items-end">
			<div>
				<span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">Classification</span>
				<h1 class="text-4xl font-extrabold text-white mt-1"><?php echo esc_html( $term->name ); ?></h1>
				<?php if ( $term->description ) : ?>
					<p class="text-gray-400 mt-2 text-sm max-w-xl"><?php echo esc_html( $term->description ); ?></p>
				<?php endif; ?>
			</div>
			
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="text-sm font-semibold text-gray-400 hover:text-white transition-colors flex items-center">
				<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
				Toutes les galeries
			</a>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'templates/media-card' ); ?>
				<?php endwhile; ?>
			</div>

			<!-- Pagination -->
			<div class="mt-12 flex justify-center">
				<?php 
				echo paginate_links( array(
					'prev_text' => '&larr; Précédent',
					'next_text' => 'Suivant &rarr;',
					'type'      => 'list',
				) );
				?>
			</div>
		<?php else : ?>
			<div class="text-center py-20 glass-effect rounded-2xl">
				<svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
				<p class="text-gray-500 mt-4">Aucun média public n'a encore été affecté à cette classification.</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
