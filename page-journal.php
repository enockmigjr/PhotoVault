<?php
/**
 * Template Name: Carnets visuels
 *
 * @package PhotoVault
 */

get_header();

$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 9,
		'paged'               => $paged,
		'ignore_sticky_posts' => false,
	)
);
?>

<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-8">
				<p class="text-xs font-extrabold uppercase text-indigo-400"><?php esc_html_e( 'Journal / Carnets visuels', 'photovault' ); ?></p>
				<h1 class="mt-7 max-w-4xl font-serif text-5xl leading-[1.05] text-white sm:text-7xl"><?php esc_html_e( 'Regarder plus longtemps. Écrire après l’image.', 'photovault' ); ?></h1>
			</div>
			<p class="max-w-xl self-end text-sm leading-7 text-gray-400 lg:col-span-4"><?php esc_html_e( 'Notes d’atelier, récits de territoire et réflexions sur la lumière, la mémoire et la construction des séries photographiques.', 'photovault' ); ?></p>
		</div>
	</header>

	<section class="mx-auto max-w-[90rem] px-5 py-16 sm:px-8 lg:px-12 lg:py-24" aria-labelledby="journal-list-title">
		<div class="mb-10 flex flex-col gap-4 border-b border-white/10 pb-7 sm:flex-row sm:items-end sm:justify-between">
			<div>
				<p class="text-xs font-bold uppercase text-gray-500"><?php echo esc_html( sprintf( _n( '%d article publié', '%d articles publiés', (int) $posts->found_posts, 'photovault' ), (int) $posts->found_posts ) ); ?></p>
				<h2 id="journal-list-title" class="mt-2 text-2xl font-bold text-white"><?php esc_html_e( 'Dernières publications', 'photovault' ); ?></h2>
			</div>
			<?php get_search_form(); ?>
		</div>

		<?php if ( $posts->have_posts() ) : ?>
			<div class="grid gap-x-7 gap-y-12 md:grid-cols-2 xl:grid-cols-3">
				<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
					<article <?php post_class( 'group min-w-0' ); ?>>
						<a href="<?php the_permalink(); ?>" class="block aspect-[4/3] overflow-hidden rounded-md border border-white/10 bg-gray-950" aria-hidden="true" tabindex="-1">
							<?php photovault_render_post_visual( 'medium_large', 'h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]' ); ?>
						</a>
						<div class="pt-5">
							<div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase text-gray-500">
								<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<span aria-hidden="true">/</span>
								<span><?php echo esc_html( get_the_author() ); ?></span>
							</div>
							<h3 class="mt-3 text-2xl font-bold leading-8 text-white"><a href="<?php the_permalink(); ?>" class="transition hover:text-indigo-400"><?php the_title(); ?></a></h3>
							<p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-400"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							<a href="<?php the_permalink(); ?>" class="mt-5 inline-flex min-h-11 items-center text-sm font-bold text-gray-200 transition hover:text-indigo-400"><?php esc_html_e( 'Lire le carnet', 'photovault' ); ?> <span class="ml-2" aria-hidden="true">&rarr;</span></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<nav class="mt-16 border-t border-white/10 pt-8" aria-label="<?php esc_attr_e( 'Pagination du journal', 'photovault' ); ?>">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $posts->max_num_pages,
							'current'   => $paged,
							'prev_text' => __( 'Précédent', 'photovault' ),
							'next_text' => __( 'Suivant', 'photovault' ),
						)
					)
				);
				?>
			</nav>
		<?php else : ?>
			<div class="border-y border-white/10 py-20 text-center">
				<h2 class="font-serif text-3xl text-white"><?php esc_html_e( 'Le premier carnet est en préparation.', 'photovault' ); ?></h2>
				<p class="mt-3 text-sm text-gray-400"><?php esc_html_e( 'Les prochaines notes d’atelier apparaîtront ici.', 'photovault' ); ?></p>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
wp_reset_postdata();
get_footer();
