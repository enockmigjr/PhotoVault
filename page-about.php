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
			<p class="text-gray-400 text-lg max-w-xl mx-auto">Protéger le talent créatif de chaque photographe à travers le monde.</p>
		</header>

		<div class="glass-effect p-8 sm:p-12 rounded-3xl border border-gray-800 shadow-xl space-y-8 text-gray-300 leading-relaxed">
			<section class="space-y-4">
				<h2 class="text-2xl font-bold text-white">Pourquoi PhotoVault ?</h2>
				<p>Nous pensons que vos photographies sont plus que de simples octets stockés sur un serveur. Elles représentent votre vision artistique, des heures de travail et votre gagne-pain. C'est pourquoi nous avons créé PhotoVault : un coffre-fort numérique alliant élégance et sécurité.</p>
			</section>

			<section class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-800/80 pt-8">
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Sécurité d'abord</h3>
					<p class="text-sm text-gray-400">Chaque photo stockée sur PhotoVault bénéficie de filigranes dynamiques, de restrictions de téléchargement et d'une gestion intelligente des droits.</p>
				</div>
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Portfolio élégant</h3>
					<p class="text-sm text-gray-400">Partagez vos galeries avec vos clients de manière simple et raffinée sans aucun compromis sur l'expérience esthétique.</p>
				</div>
			</section>

			<section class="text-center pt-8 border-t border-gray-800/80 space-y-4">
				<h3 class="text-xl font-bold text-white">Prêt à sécuriser votre portfolio ?</h3>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl inline-block transition-all shadow-lg cursor-pointer">
					Créer mon compte
				</a>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
