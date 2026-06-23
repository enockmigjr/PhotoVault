<?php
/**
 * Template pour l'affichage détaillé d'un article de blog standard (single.php).
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-12 bg-[#0b0f19] min-h-screen">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="glass-effect p-8 sm:p-12 rounded-3xl border border-gray-800 shadow-2xl space-y-8">
					<header class="space-y-4">
						<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 uppercase tracking-wider flex items-center">
							&larr; Retour au blog
						</a>
						<h1 class="text-3xl sm:text-5xl font-extrabold text-white leading-tight mt-2"><?php the_title(); ?></h1>
						<p class="text-sm text-gray-500">
							Publié le <?php echo get_the_date(); ?> | Par <?php the_author(); ?>
						</p>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="aspect-video w-full rounded-2xl overflow-hidden bg-gray-950 border border-gray-800">
							<?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover' ) ); ?>
						</div>
					<?php endif; ?>

					<div class="prose prose-invert max-w-none text-gray-300 leading-relaxed text-base space-y-6">
						<?php the_content(); ?>
					</div>

					<!-- Section commentaires -->
					<?php if ( comments_open() || get_comments_number() ) : ?>
						<div class="border-t border-gray-800/80 pt-8 mt-12">
							<?php comments_template(); ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
