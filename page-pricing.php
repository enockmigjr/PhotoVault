<?php
/**
 * Template Name: PhotoVault Pricing
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-20 bg-[#0d0c0b] min-h-screen">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-12">
		<header class="space-y-4">
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">Des prestations adaptées à vos <span class="text-indigo-500">projets</span></h1>
			<p class="text-gray-300 text-lg max-w-xl mx-auto">Découvrez nos formules pour vos séances photo privées ou pour acquérir nos tirages en édition limitée.</p>
		</header>

		<!-- Grille des Offres -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
			<!-- Starter -->
			<div class="glass-effect p-8 rounded-3xl border border-gray-800 text-left flex flex-col justify-between shadow-xl">
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Séance Portrait</h3>
					<p class="text-xs text-gray-300 mb-6">Idéal pour vos portraits professionnels et profils personnels.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">149€</span>
						<span class="text-gray-300 text-sm ml-2">/ séance</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-300">
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> 1 heure de shooting en studio</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> 10 photos retouchées HD</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Galerie privée en ligne (6 mois)</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Téléchargement direct inclus</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mt-8 w-full py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 text-white font-semibold rounded-xl text-center block transition-all cursor-pointer">Réserver la séance</a>
			</div>

			<!-- Pro (Mis en avant) -->
			<div class="glass-effect p-8 rounded-3xl border-2 border-indigo-500 text-left flex flex-col justify-between shadow-2xl relative">
				<div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Le plus populaire</div>
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Shooting Corporate</h3>
					<p class="text-xs text-gray-300 mb-6">Pour les entreprises et les professionnels en quête d'image de marque.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">399€</span>
						<span class="text-gray-300 text-sm ml-2">/ séance</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-200">
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Demi-journée de reportage/shooting</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> 50 photos retouchées HD</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Galerie privée sécurisée (1 an)</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Droits d'utilisation commerciale</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mt-8 w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-center block transition-all shadow-lg cursor-pointer">Réserver la séance</a>
			</div>

			<!-- Studio -->
			<div class="glass-effect p-8 rounded-3xl border border-gray-800 text-left flex flex-col justify-between shadow-xl">
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Reportage Événementiel</h3>
					<p class="text-xs text-gray-300 mb-6">Pour vos événements importants, mariages et lancements.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">999€</span>
						<span class="text-gray-300 text-sm ml-2">/ événement</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-300">
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Journée complète de présence</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Photos illimitées en ligne</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Galerie privée partagée invités</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Clé USB & tirages physiques</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mt-8 w-full py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 text-white font-semibold rounded-xl text-center block transition-all cursor-pointer">Demander un devis</a>
			</div>
		</div>
		<section class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left border-t border-gray-900 pt-10">
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Galerie incluse</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Chaque prestation comprend un espace en ligne pour consulter les apercus et retrouver les livrables.</p>
			</div>
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Retouches et selection</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Les forfaits peuvent etre ajustes selon le volume d'images finales et le niveau de retouche souhaite.</p>
			</div>
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Droits d'utilisation</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Les usages personnels, commerciaux ou editoriaux sont precises avant livraison des fichiers HD.</p>
			</div>
		</section>
	</div>
</div>

<?php get_footer(); ?>
