<?php
/**
 * Default public page.
 *
 * @package PhotoVault
 */

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<?php while ( have_posts() ) : the_post(); ?>
		<article>
			<header class="border-b border-white/10 py-20 sm:py-28"><div class="mx-auto max-w-5xl px-5 sm:px-8"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'PhotoVault / Informations', 'photovault' ); ?></p><h1 class="mt-6 font-serif text-5xl leading-[1.06] text-white sm:text-7xl"><?php the_title(); ?></h1></div></header>
			<?php if ( has_post_thumbnail() ) : ?><div class="mx-auto mt-14 aspect-[16/7] max-w-[90rem] overflow-hidden border border-white/10"><?php the_post_thumbnail( 'large', array( 'class' => 'h-full w-full object-cover' ) ); ?></div><?php endif; ?>
			<div class="pv-editorial-content mx-auto max-w-3xl px-5 py-14 text-base leading-8 text-gray-300 sm:px-8 sm:py-20"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
