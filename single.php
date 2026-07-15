<?php
/**
 * Editorial post detail.
 *
 * @package PhotoVault
 */

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<?php while ( have_posts() ) : the_post(); ?>
		<article>
			<header class="mx-auto max-w-5xl px-5 pb-12 pt-20 text-center sm:px-8 sm:pb-16 sm:pt-28">
				<a href="<?php echo esc_url( home_url( '/journal/' ) ); ?>" class="inline-flex min-h-11 items-center text-xs font-extrabold uppercase text-amber-200 hover:text-white"><span class="mr-2" aria-hidden="true">&larr;</span><?php esc_html_e( 'Carnets visuels', 'photovault' ); ?></a>
				<h1 class="mt-6 font-serif text-5xl leading-[1.06] text-white sm:text-7xl"><?php the_title(); ?></h1>
				<div class="mt-7 flex flex-wrap items-center justify-center gap-2 text-xs font-semibold uppercase text-gray-500"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><span aria-hidden="true">/</span><span><?php echo esc_html( get_the_author() ); ?></span><?php if ( get_the_category() ) : ?><span aria-hidden="true">/</span><span><?php echo esc_html( get_the_category()[0]->name ); ?></span><?php endif; ?></div>
			</header>

			<div class="mx-auto aspect-[16/9] max-w-[90rem] overflow-hidden border-y border-white/10 bg-gray-950 sm:border sm:px-0"><?php photovault_render_post_visual( 'large', 'h-full w-full object-cover' ); ?></div>

			<div class="pv-editorial-content mx-auto max-w-3xl px-5 py-14 text-base leading-8 text-gray-300 sm:px-8 sm:py-20"><?php the_content(); ?></div>

			<?php if ( comments_open() || get_comments_number() ) : ?><section class="mx-auto max-w-3xl border-t border-white/10 px-5 py-14 sm:px-8" aria-label="<?php esc_attr_e( 'Commentaires', 'photovault' ); ?>"><?php comments_template(); ?></section><?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
