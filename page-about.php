<?php
/**
 * Template Name: PhotoVault About
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-20 bg-[#0b0f19] min-h-screen">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<header class="text-center space-y-4">
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">Notre <span class="text-indigo-500">Mission</span></h1>
			<p class="text-gray-400 text-lg max-w-xl mx-auto">Exposer et protéger les créations visuelles avec élégance et sécurité.</p>
		</header>

		<div class="glass-effect p-8 sm:p-12 rounded-3xl border border-gray-800 shadow-xl space-y-8 text-gray-300 leading-relaxed">
			<section class="space-y-4">
				<h2 class="text-2xl font-bold text-white">Pourquoi PhotoVault ?</h2>
				<p>Nous pensons que les œuvres photographiques méritent un écrin d'exposition d'exception. PhotoVault a été développé comme un coffre-fort numérique alliant design de portfolio premium et sécurité avancée pour protéger la propriété artistique.</p>
			</section>

			<section class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-800/80 pt-8">
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Sécurité d'abord</h3>
					<p class="text-sm text-gray-400">Toutes les images exposées sur la galerie bénéficient de filigranes dynamiques côté serveur et de restrictions de téléchargement adaptées.</p>
				</div>
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Portfolio élégant</h3>
					<p class="text-sm text-gray-400">Les clients disposent d'un accès sécurisé et privé pour consulter leurs projets commandés en direct et avec une qualité visuelle irréprochable.</p>
				</div>
			</section>

			<section class="text-center pt-8 border-t border-gray-800/80 space-y-4">
				<h3 class="text-xl font-bold text-white">Prêt à explorer la collection ?</h3>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl inline-block transition-all shadow-lg cursor-pointer text-center">
					Créer un compte client
				</a>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
