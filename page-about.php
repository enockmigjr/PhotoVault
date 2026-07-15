<?php
/**
 * Template Name: PhotoVault About
 *
 * @package PhotoVault
 */

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12">
			<div class="lg:col-span-8">
				<p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'À propos / Démarche', 'photovault' ); ?></p>
				<h1 class="mt-7 max-w-5xl font-serif text-5xl leading-[1.04] text-white sm:text-7xl"><?php esc_html_e( 'Photographier ce que le temps laisse derrière lui.', 'photovault' ); ?></h1>
			</div>
			<p class="max-w-xl self-end text-base leading-8 text-gray-400 lg:col-span-4"><?php esc_html_e( 'PhotoVault réunit un travail photographique, ses récits et un espace de livraison confidentiel. Chaque image y conserve son contexte, ses droits et sa juste qualité d’affichage.', 'photovault' ); ?></p>
		</div>
	</header>

	<section class="mx-auto grid max-w-[90rem] gap-12 px-5 py-16 sm:px-8 lg:grid-cols-12 lg:px-12 lg:py-24" aria-labelledby="about-manifesto">
		<div class="lg:col-span-4"><p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( '01 / Manifeste', 'photovault' ); ?></p><h2 id="about-manifesto" class="mt-5 font-serif text-4xl leading-tight text-white"><?php esc_html_e( 'Une archive n’est pas un simple catalogue.', 'photovault' ); ?></h2></div>
		<div class="space-y-7 text-base leading-8 text-gray-300 lg:col-span-7 lg:col-start-6">
			<p><?php esc_html_e( 'Le travail présenté ici observe la mémoire, les présences et les transformations d’un territoire. La lumière ne sert pas seulement à montrer : elle situe un moment, révèle une distance et donne une place au silence.', 'photovault' ); ?></p>
			<p><?php esc_html_e( 'Les séries publiques peuvent être parcourues librement. Les commandes, travaux inédits et archives personnelles restent protégés et ne deviennent accessibles qu’après autorisation. Cette séparation fait partie de la démarche autant que de la sécurité.', 'photovault' ); ?></p>
			<p><?php esc_html_e( 'Sur les pages d’exploration, PhotoVault ne charge que des aperçus adaptés à l’écran. Le fichier haute définition demeure à l’écart jusqu’à un téléchargement explicitement permis.', 'photovault' ); ?></p>
		</div>
	</section>

	<section class="border-y border-white/10">
		<div class="mx-auto grid max-w-[90rem] divide-y divide-white/10 px-5 sm:px-8 md:grid-cols-3 md:divide-x md:divide-y-0 lg:px-12">
			<div class="py-10 md:pr-8"><span class="text-xs font-extrabold text-amber-200">01</span><h3 class="mt-5 text-xl font-bold text-white"><?php esc_html_e( 'Observer', 'photovault' ); ?></h3><p class="mt-3 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Explorer les séries par collection, catégorie, année et niveau d’accès.', 'photovault' ); ?></p></div>
			<div class="py-10 md:px-8"><span class="text-xs font-extrabold text-amber-200">02</span><h3 class="mt-5 text-xl font-bold text-white"><?php esc_html_e( 'Protéger', 'photovault' ); ?></h3><p class="mt-3 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Présenter des aperçus filigranés sans exposer les originaux ni les collections confidentielles.', 'photovault' ); ?></p></div>
			<div class="py-10 md:pl-8"><span class="text-xs font-extrabold text-amber-200">03</span><h3 class="mt-5 text-xl font-bold text-white"><?php esc_html_e( 'Transmettre', 'photovault' ); ?></h3><p class="mt-3 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Livrer les fichiers autorisés, documenter leur accès et préserver l’histoire de chaque projet.', 'photovault' ); ?></p></div>
		</div>
	</section>

	<section class="mx-auto grid max-w-[90rem] items-end gap-10 px-5 py-20 sm:px-8 lg:grid-cols-12 lg:px-12 lg:py-28">
		<div class="lg:col-span-8"><p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Continuer', 'photovault' ); ?></p><h2 class="mt-5 font-serif text-4xl leading-tight text-white sm:text-6xl"><?php esc_html_e( 'Regarder les archives ou construire les prochaines images.', 'photovault' ); ?></h2></div>
		<div class="flex flex-wrap gap-3 lg:col-span-4 lg:justify-end"><a class="pv-header-cta min-h-12 px-6" href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>"><?php esc_html_e( 'Explorer la galerie', 'photovault' ); ?></a><a class="inline-flex min-h-12 items-center border border-white/15 px-6 text-sm font-bold hover:border-amber-200/60 hover:text-amber-100" href="<?php echo esc_url( home_url( '/booking/' ) ); ?>"><?php esc_html_e( 'Réserver un shooting', 'photovault' ); ?></a></div>
	</section>
</main>
<?php get_footer(); ?>
