<?php
/**
 * Public search results.
 *
 * @package PhotoVault
 */

global $wp_query;
$query_text = get_search_query();
$has_media  = false;

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28"><div class="mx-auto max-w-[90rem] px-5 sm:px-8 lg:px-12"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Recherche', 'photovault' ); ?></p><h1 class="mt-6 max-w-5xl font-serif text-5xl leading-[1.06] text-white sm:text-7xl"><?php echo esc_html( $query_text ? sprintf( __( 'Résultats pour « %s »', 'photovault' ), $query_text ) : __( 'Explorer les contenus', 'photovault' ) ); ?></h1><p class="mt-6 text-sm text-gray-400"><?php echo esc_html( sprintf( _n( '%d résultat', '%d résultats', (int) $wp_query->found_posts, 'photovault' ), (int) $wp_query->found_posts ) ); ?></p></div></header>
	<section class="mx-auto max-w-[90rem] px-5 py-14 sm:px-8 lg:px-12 lg:py-20">
		<div class="mb-10 max-w-lg"><?php get_search_form(); ?></div>
		<?php if ( have_posts() ) : ?>
			<div id="media-grid" class="grid gap-x-7 gap-y-12 md:grid-cols-2 xl:grid-cols-3">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php if ( 'media_item' === get_post_type() ) : $has_media = true; get_template_part( 'templates/media-card' ); else : get_template_part( 'templates/post-card', null, array( 'heading_tag' => 'h2', 'cta' => __( 'Voir le contenu', 'photovault' ) ) ); endif; ?>
				<?php endwhile; ?>
			</div>
			<?php if ( $wp_query->max_num_pages > 1 ) : ?><nav class="pv-pagination mt-16 border-t border-white/10 pt-8" aria-label="<?php esc_attr_e( 'Pagination des résultats', 'photovault' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'total' => $wp_query->max_num_pages, 'prev_text' => __( 'Précédent', 'photovault' ), 'next_text' => __( 'Suivant', 'photovault' ) ) ) ); ?></nav><?php endif; ?>
		<?php else : ?><div class="border-y border-white/10 py-20 text-center"><h2 class="font-serif text-3xl text-white"><?php esc_html_e( 'Aucun contenu ne correspond.', 'photovault' ); ?></h2><p class="mt-3 text-sm text-gray-400"><?php esc_html_e( 'Essayez un titre, une collection ou un autre mot-clé.', 'photovault' ); ?></p></div><?php endif; ?>
	</section>
</main>
<?php if ( $has_media ) : get_template_part( 'templates/gallery-lightbox' ); endif; ?>
<?php get_footer(); ?>
