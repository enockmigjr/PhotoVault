<?php
/**
 * Template Name: PhotoVault About
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-20 bg-[#0d0c0b] min-h-screen">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<header class="text-center space-y-4">
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">À Propos de <span class="text-indigo-500">l'Artiste</span></h1>
			<p class="text-gray-300 text-lg max-w-xl mx-auto">Paul M., artisan de l'image et créateur de compositions visuelles intemporelles.</p>
		</header>

		<div class="glass-effect p-8 sm:p-12 rounded-3xl border border-gray-800 shadow-xl space-y-8 text-gray-200 leading-relaxed">
			<section class="space-y-4">
				<h2 class="text-2xl font-bold text-white text-indigo-400">La Vision Artistique</h2>
				<p>Ce portfolio est une galerie d'exposition numérique confidentielle. Mon travail s'articule autour de la capture de l'instant brut, du portrait corporate de caractère et de la photographie d'art en édition limitée. Pour mes clients réguliers, cette plateforme sert d'espace de livraison sécurisé. Pour les amateurs d'art et collectionneurs, elle offre un accès privilégié à mes compositions.</p>
			</section>

			<section class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-800/80 pt-8">
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Accès Libre & HD</h3>
					<p class="text-sm text-gray-300">Créez un compte client gratuit pour explorer mes galeries publiques et télécharger directement mes clichés autorisés en haute résolution. Ils sont disponibles pour votre plaisir visuel ou votre usage personnel.</p>
				</div>
				<div>
					<h3 class="text-lg font-bold text-white mb-2">Œuvres Protégées & Prestations</h3>
					<p class="text-sm text-gray-300">Les clichés d'art protégés comportent un filigrane de sécurité. Si vous souhaitez en acquérir une licence commerciale, commander des tirages physiques numérotés ou réserver une séance photo sur mesure (Portrait, Corporate ou Reportage), contactez-moi pour souscrire à un forfait ou obtenir un devis personnalisé.</p>
				</div>
			</section>


			<section class="border-t border-gray-800/80 pt-8 space-y-4">
				<h3 class="text-xl font-bold text-white">Processus de livraison</h3>
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
					<div class="p-4 rounded-2xl bg-gray-950/40 border border-gray-800"><span class="block text-indigo-400 font-bold mb-1">Selection</span><p class="text-gray-300">Les images sont organisees par projet, dossier client et categorie.</p></div>
					<div class="p-4 rounded-2xl bg-gray-950/40 border border-gray-800"><span class="block text-indigo-400 font-bold mb-1">Validation</span><p class="text-gray-300">Les apercus permettent de parcourir une serie sans charger les originaux.</p></div>
					<div class="p-4 rounded-2xl bg-gray-950/40 border border-gray-800"><span class="block text-indigo-400 font-bold mb-1">Livraison</span><p class="text-gray-300">Les fichiers HD sont remis uniquement lorsque le telechargement est autorise.</p></div>
				</div>
			</section>
			<section class="text-center pt-8 border-t border-gray-800/80 space-y-4">
				<h3 class="text-xl font-bold text-white">Prêt à explorer la galerie ?</h3>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl inline-block transition-all shadow-lg cursor-pointer text-center">
					Créer mon compte client
				</a>
			</section>
		</div>
	</div>
</div>

<?php get_footer(); ?>
