<?php
/**
 * Shared editorial post listing.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$listing_query = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : $GLOBALS['wp_query'];
$eyebrow       = isset( $args['eyebrow'] ) ? sanitize_text_field( $args['eyebrow'] ) : __( 'Journal / Carnets visuels', 'photovault' );
$title         = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : __( 'Regarder plus longtemps. Écrire après l’image.', 'photovault' );
$copy          = isset( $args['copy'] ) ? sanitize_text_field( $args['copy'] ) : __( 'Notes d’atelier, récits de territoire et réflexions sur la lumière, la mémoire et la construction des séries photographiques.', 'photovault' );
$empty_title   = isset( $args['empty_title'] ) ? sanitize_text_field( $args['empty_title'] ) : __( 'Le premier carnet est en préparation.', 'photovault' );
$paged         = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-8"><p class="text-xs font-extrabold uppercase text-amber-200"><?php echo esc_html( $eyebrow ); ?></p><h1 class="mt-7 max-w-4xl font-serif text-5xl leading-[1.05] text-white sm:text-7xl"><?php echo esc_html( $title ); ?></h1></div>
			<p class="max-w-xl self-end text-sm leading-7 text-gray-400 lg:col-span-4"><?php echo esc_html( $copy ); ?></p>
		</div>
	</header>

	<section class="mx-auto max-w-[90rem] px-5 py-16 sm:px-8 lg:px-12 lg:py-24" aria-labelledby="post-list-title">
		<div class="mb-10 flex flex-col gap-4 border-b border-white/10 pb-7 sm:flex-row sm:items-end sm:justify-between">
			<div><p class="text-xs font-bold uppercase text-gray-500"><?php echo esc_html( sprintf( _n( '%d publication', '%d publications', (int) $listing_query->found_posts, 'photovault' ), (int) $listing_query->found_posts ) ); ?></p><h2 id="post-list-title" class="mt-2 text-2xl font-bold text-white"><?php esc_html_e( 'Dernières publications', 'photovault' ); ?></h2></div>
			<?php get_search_form(); ?>
		</div>

		<?php if ( $listing_query->have_posts() ) : ?>
			<div class="grid gap-x-7 gap-y-12 md:grid-cols-2 xl:grid-cols-3">
				<?php while ( $listing_query->have_posts() ) : $listing_query->the_post(); get_template_part( 'templates/post-card', null, array( 'heading_tag' => 'h3' ) ); endwhile; ?>
			</div>
			<?php if ( $listing_query->max_num_pages > 1 ) : ?><nav class="pv-pagination mt-16 border-t border-white/10 pt-8" aria-label="<?php esc_attr_e( 'Pagination des publications', 'photovault' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'total' => $listing_query->max_num_pages, 'current' => $paged, 'prev_text' => __( 'Précédent', 'photovault' ), 'next_text' => __( 'Suivant', 'photovault' ) ) ) ); ?></nav><?php endif; ?>
		<?php else : ?>
			<div class="border-y border-white/10 py-20 text-center"><h2 class="font-serif text-3xl text-white"><?php echo esc_html( $empty_title ); ?></h2><p class="mt-3 text-sm text-gray-400"><?php esc_html_e( 'Les prochaines publications apparaîtront ici.', 'photovault' ); ?></p></div>
		<?php endif; ?>
	</section>
</main>
