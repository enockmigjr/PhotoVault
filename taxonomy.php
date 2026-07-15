<?php
/**
 * Media taxonomy archive.
 *
 * @package PhotoVault
 */

$term = get_queried_object();
global $wp_query;

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28"><div class="mx-auto grid max-w-[90rem] gap-8 px-5 sm:px-8 lg:grid-cols-12 lg:px-12"><div class="lg:col-span-8"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Collection / Classification', 'photovault' ); ?></p><h1 class="mt-6 font-serif text-5xl text-white sm:text-7xl"><?php echo esc_html( $term->name ); ?></h1></div><div class="self-end lg:col-span-4"><?php if ( $term->description ) : ?><p class="text-sm leading-7 text-gray-400"><?php echo esc_html( $term->description ); ?></p><?php endif; ?><a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="mt-5 inline-flex min-h-11 items-center text-sm font-bold text-white hover:text-amber-200"><span class="mr-2" aria-hidden="true">&larr;</span><?php esc_html_e( 'Toutes les œuvres', 'photovault' ); ?></a></div></div></header>
	<section class="mx-auto max-w-[90rem] px-5 py-14 sm:px-8 lg:px-12 lg:py-20"><p class="mb-8 border-b border-white/10 pb-5 text-xs font-bold uppercase text-gray-500"><?php echo esc_html( sprintf( _n( '%d œuvre visible', '%d œuvres visibles', (int) $wp_query->found_posts, 'photovault' ), (int) $wp_query->found_posts ) ); ?></p>
		<?php if ( have_posts() ) : ?><div id="media-grid" class="columns-1 gap-6 sm:columns-2 lg:columns-3 xl:columns-4"><?php while ( have_posts() ) : the_post(); get_template_part( 'templates/media-card' ); endwhile; ?></div><?php if ( $wp_query->max_num_pages > 1 ) : ?><nav class="pv-pagination mt-16 border-t border-white/10 pt-8" aria-label="<?php esc_attr_e( 'Pagination de la collection', 'photovault' ); ?>"><?php echo wp_kses_post( paginate_links() ); ?></nav><?php endif; ?>
		<?php else : ?><div class="border-y border-white/10 py-20 text-center"><h2 class="font-serif text-3xl text-white"><?php esc_html_e( 'Cette classification attend sa première œuvre.', 'photovault' ); ?></h2></div><?php endif; ?>
	</section>
</main>
<?php get_template_part( 'templates/gallery-lightbox' ); ?>
<?php get_footer(); ?>
