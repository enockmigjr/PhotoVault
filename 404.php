<?php
/**
 * Not-found template.
 *
 * @package PhotoVault
 */

get_header();

$gallery_url = get_post_type_archive_link( 'media_item' );
?>
<main class="min-h-[78vh] bg-[#0d0c0b] text-gray-100">
	<section class="mx-auto grid min-h-[78vh] max-w-7xl items-center gap-12 px-5 py-20 sm:px-8 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,28rem)] lg:py-28" aria-labelledby="not-found-title">
		<div class="max-w-3xl">
			<p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Archive introuvable / 404', 'photovault' ); ?></p>
			<h1 id="not-found-title" class="mt-6 font-serif text-5xl leading-[1.04] text-white sm:text-7xl"><?php esc_html_e( 'Cette image a quitté le cadre.', 'photovault' ); ?></h1>
			<p class="mt-7 max-w-2xl text-base leading-8 text-gray-400 sm:text-lg"><?php esc_html_e( 'Le lien a peut-être changé ou la ressource n’est plus publiée. Reprenez votre exploration depuis les archives ou recherchez un titre, un lieu ou une histoire.', 'photovault' ); ?></p>
			<div class="mt-10 flex flex-wrap gap-3">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="pv-header-cta min-h-12 px-6"><?php esc_html_e( 'Retour à l’accueil', 'photovault' ); ?></a>
				<?php if ( $gallery_url ) : ?><a href="<?php echo esc_url( $gallery_url ); ?>" class="inline-flex min-h-12 items-center border border-white/15 px-6 text-sm font-bold text-gray-200 transition hover:border-amber-200/60 hover:text-amber-100"><?php esc_html_e( 'Explorer la galerie', 'photovault' ); ?></a><?php endif; ?>
			</div>
		</div>

		<aside class="border-l border-white/10 pl-6 sm:pl-8" aria-label="<?php esc_attr_e( 'Rechercher dans PhotoVault', 'photovault' ); ?>">
			<p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Retrouver une trace', 'photovault' ); ?></p>
			<p class="mt-4 font-serif text-2xl leading-snug text-white"><?php esc_html_e( 'Les archives restent accessibles par mot-clé.', 'photovault' ); ?></p>
			<div class="mt-7"><?php get_search_form(); ?></div>
			<a class="mt-8 inline-flex text-xs font-extrabold uppercase text-amber-200 hover:text-white" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Lire les carnets', 'photovault' ); ?><span class="ml-2" aria-hidden="true">&rarr;</span></a>
		</aside>
	</section>
</main>
<?php get_footer(); ?>
