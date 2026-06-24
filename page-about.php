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
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">À Propos de <span class="text-indigo-500">PhotoVault</span></h1>
			<p class="text-gray-400 text-lg max-w-xl mx-auto">L'espace d'exposition photographique et de services exclusifs de l'artiste.</p>
		</header>

		<div class="glass-effect p-8 sm:p-12 rounded-3xl border border-gray-800 shadow-xl space-y-8 text-gray-300 leading-relaxed">
			<section class="space-y-4">
				<h2 class="text-2xl font-bold text-white">Le Concept</h2>
				<p>PhotoVault est le portfolio photographique officiel de l'artiste propriétaire. Conçu comme une galerie d'art numérique premium, cet espace permet aux visiteurs de créer un compte client pour explorer la collection complète de créations visuelles et télécharger gratuitement les œuvres libres en haute définition.</p>
			</section>

			<section class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-800/80 pt-8">
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Accès Libre & HD</h3>
					<p class="text-sm text-gray-400">En vous connectant comme client, accédez aux créations publiques autorisées et téléchargez-les gratuitement en haute résolution pour votre usage.</p>
				</div>
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Collections Exclusives</h3>
					<p class="text-sm text-gray-400">Les œuvres exclusives et protégées comportent un filigrane de sécurité. Pour lever les restrictions et télécharger les originaux, ou pour réserver un shooting personnalisé, contactez le service pour prendre un abonnement.</p>
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
