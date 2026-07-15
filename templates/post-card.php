<?php
/**
 * Shared editorial card for posts and public pages.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading_tag = isset( $args['heading_tag'] ) && in_array( $args['heading_tag'], array( 'h2', 'h3' ), true ) ? $args['heading_tag'] : 'h2';
$cta         = isset( $args['cta'] ) ? sanitize_text_field( $args['cta'] ) : __( 'Lire le carnet', 'photovault' );
$categories  = 'post' === get_post_type() ? get_the_category() : array();
$label       = $categories ? $categories[0]->name : ( 'page' === get_post_type() ? __( 'Page', 'photovault' ) : __( 'Journal', 'photovault' ) );
?>
<article <?php post_class( 'group min-w-0' ); ?>>
	<a href="<?php the_permalink(); ?>" class="block aspect-[4/3] overflow-hidden rounded-md border border-white/10 bg-gray-950" aria-hidden="true" tabindex="-1">
		<?php photovault_render_post_visual( 'medium_large', 'h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]' ); ?>
	</a>
	<div class="pt-5">
		<div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase text-gray-500">
			<span><?php echo esc_html( $label ); ?></span>
			<?php if ( 'post' === get_post_type() ) : ?><span aria-hidden="true">/</span><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><?php endif; ?>
		</div>
		<<?php echo tag_escape( $heading_tag ); ?> class="mt-3 text-2xl font-bold leading-8 text-white"><a href="<?php the_permalink(); ?>" class="transition hover:text-amber-200"><?php the_title(); ?></a></<?php echo tag_escape( $heading_tag ); ?>>
		<p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-400"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<a href="<?php the_permalink(); ?>" class="mt-5 inline-flex min-h-11 items-center text-sm font-bold text-gray-200 transition hover:text-amber-200"><?php echo esc_html( $cta ); ?> <span class="ml-2" aria-hidden="true">&rarr;</span></a>
	</div>
</article>
