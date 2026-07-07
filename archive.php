<?php
/**
 * Template pour les archives par défaut (archive.php).
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-12 bg-[#0d0c0b] min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<header class="mb-10 pb-6 border-b border-gray-900">
			<h1 class="text-4xl font-extrabold text-white">
				<?php the_archive_title(); ?>
			</h1>
			<?php the_archive_description( '<p class="text-gray-300 mt-2 text-sm max-w-xl">', '</p>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="glass-effect rounded-2xl overflow-hidden shadow-lg border border-gray-800/80 p-6 flex flex-col justify-between">
						<div>
							<div class="aspect-video w-full rounded-xl overflow-hidden mb-4 bg-gray-950 border border-gray-800/70">
								<?php photovault_render_post_visual( 'medium_large', 'w-full h-full object-cover' ); ?>
							</div>
							<h2 class="text-xl font-bold text-white mb-2">
								<a href="<?php the_permalink(); ?>" class="hover:text-indigo-400 transition-colors"><?php the_title(); ?></a>
							</h2>
							<p class="text-xs text-gray-300 mb-4"><?php echo get_the_date(); ?> | Par <?php the_author(); ?></p>
							<div class="text-gray-300 text-sm leading-relaxed mb-6">
								<?php the_excerpt(); ?>
							</div>
						</div>
						<a href="<?php the_permalink(); ?>" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center">
							Lire l'article &rarr;
						</a>
					</article>
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
				<p class="text-gray-300">Aucun article trouvé dans cette archive.</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
