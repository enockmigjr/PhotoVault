<?php
/**
 * Landing Page principale (front-page.php) de PhotoVault.
 *
 * @package PhotoVault
 */

get_header();

// Récupérer 4 médias publics récents pour la section vitrine.
$featured_media = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
) );
?>

<!-- Section Hero -->
<section class="relative overflow-hidden py-24 sm:py-32 bg-[#0b0f19]">
	<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-indigo-950/20 via-transparent to-transparent pointer-events-none"></div>
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
		<h1 class="text-5xl sm:text-7xl font-extrabold text-white tracking-tight leading-none mb-6">
			Sécurisez et partagez<br>vos <span class="text-indigo-500">créations visuelles</span>
		</h1>
		<p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-400 mb-10">
			La galerie de portfolio de niveau professionnel conçue spécifiquement pour exposer et protéger les créations visuelles de l'artiste.
		</p>
		<div class="flex flex-col sm:flex-row justify-center items-center gap-4 max-w-md mx-auto sm:max-w-none">
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-indigo-500/20 border border-indigo-400/20 cursor-pointer text-center">
				Commencer gratuitement
			</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="w-full sm:w-auto px-8 py-4 bg-gray-900 hover:bg-gray-800 text-gray-300 font-semibold rounded-xl border border-gray-800 transition-all cursor-pointer text-center">
				Explorer les galeries
			</a>
		</div>
	</div>
</section>

<!-- Section Fonctionnalités -->
<section class="py-20 bg-[#080c14] border-t border-gray-900">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="text-center mb-16">
			<h2 class="text-3xl sm:text-4xl font-extrabold text-white">Une gestion de médias haut de gamme</h2>
			<p class="text-gray-400 mt-2">Découvrez les outils conçus pour protéger votre propriété intellectuelle.</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<!-- Visibilité -->
			<div class="glass-effect p-8 rounded-2xl transition-all hover:border-gray-800/80">
				<div class="p-3 bg-indigo-600/10 text-indigo-400 rounded-xl w-12 h-12 flex items-center justify-center mb-6">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
				</div>
				<h3 class="text-xl font-bold text-white mb-3">Contrôle de Visibilité</h3>
				<p class="text-gray-400 text-sm leading-relaxed">Explorez des clichés artistiques publics ou accédez en tant que client à vos projets et photos privées partagées.</p>
			</div>

			<!-- Protection -->
			<div class="glass-effect p-8 rounded-2xl transition-all hover:border-gray-800/80">
				<div class="p-3 bg-emerald-600/10 text-emerald-400 rounded-xl w-12 h-12 flex items-center justify-center mb-6">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
				</div>
				<h3 class="text-xl font-bold text-white mb-3">Protection Avancée</h3>
				<p class="text-gray-400 text-sm leading-relaxed">Toutes les images protégées 🔒 empêchent le clic droit et le téléchargement et comportent un filigrane de sécurité.</p>
			</div>

			<!-- Organisation -->
			<div class="glass-effect p-8 rounded-2xl transition-all hover:border-gray-800/80">
				<div class="p-3 bg-purple-600/10 text-purple-400 rounded-xl w-12 h-12 flex items-center justify-center mb-6">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
				</div>
				<h3 class="text-xl font-bold text-white mb-3">Dossiers & Catégories</h3>
				<p class="text-gray-400 text-sm leading-relaxed">Des dossiers thématiques et catégories clairs pour simplifier la navigation et la recherche.</p>
			</div>
		</div>
	</div>
</section>

<!-- Section Galerie d'Exemple -->
<section class="py-20 bg-[#0b0f19]">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="text-center mb-16">
			<h2 class="text-3xl sm:text-4xl font-extrabold text-white">Dernières publications</h2>
			<p class="text-gray-400 mt-2">Découvrez les dernières créations visuelles ajoutées à la collection.</p>
		</div>

		<?php if ( $featured_media->have_posts() ) : ?>
			<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
				<?php while ( $featured_media->have_posts() ) : $featured_media->the_post(); ?>
					<?php get_template_part( 'templates/media-card' ); ?>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="text-center py-10 glass-effect rounded-2xl">
				<p class="text-gray-500">Aucun média public disponible pour le moment.</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- Section CTA Inscription -->
<section class="py-20 bg-indigo-950/20 border-t border-b border-indigo-900/30">
	<div class="max-w-5xl mx-auto px-4 text-center">
		<h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight mb-6">Prêt à sublimer votre flux de travail ?</h2>
		<p class="text-lg text-gray-400 mb-10 max-w-2xl mx-auto">Créez un compte client pour explorer la galerie complète et accéder à vos dossiers privés sécurisés.</p>
		<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="inline-block w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg cursor-pointer text-center">
			Créer un compte maintenant
		</a>
	</div>
</section>

<?php get_footer(); ?>
