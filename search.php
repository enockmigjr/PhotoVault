<?php
/**
 * Template pour afficher les résultats de recherche (search.php).
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-12 bg-[#0b0f19] min-h-screen">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<header class="mb-10 pb-6 border-b border-gray-900">
			<h1 class="text-4xl font-extrabold text-white">
				Résultats de recherche pour : <span class="text-indigo-400">"<?php echo get_search_query(); ?>"</span>
			</h1>
			<p class="text-gray-400 mt-2 text-sm">
				<?php
				global $wp_query;
				printf( _n( '%d résultat trouvé.', '%d résultats trouvés.', $wp_query->found_posts, 'photovault' ), $wp_query->found_posts );
				?>
			</p>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="glass-effect rounded-2xl overflow-hidden shadow-lg border border-gray-800/80 p-6 flex flex-col justify-between">
						<div>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="aspect-video w-full rounded-xl overflow-hidden mb-4 bg-gray-950">
									<?php the_post_thumbnail( 'medium_large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
								</div>
							<?php endif; ?>
							<h2 class="text-xl font-bold text-white mb-2">
								<a href="<?php the_permalink(); ?>" class="hover:text-indigo-400 transition-colors"><?php the_title(); ?></a>
							</h2>
							<p class="text-xs text-gray-500 mb-4"><?php echo get_the_date(); ?> | Par <?php the_author(); ?></p>
							<div class="text-gray-400 text-sm leading-relaxed mb-6">
								<?php the_excerpt(); ?>
							</div>
						</div>
						<a href="<?php the_permalink(); ?>" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center">
							En savoir plus &rarr;
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
			<div class="text-center py-20 glass-effect rounded-2xl max-w-md mx-auto space-y-6">
				<p class="text-gray-500">Aucun résultat ne correspond à votre recherche. Veuillez réessayer avec d'autres mots-clés.</p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
