<?php
/**
 * Template Name: PhotoVault Pricing
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-20 bg-[#0b0f19] min-h-screen">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-12">
		<header class="space-y-4">
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">Des forfaits adaptés à vos <span class="text-indigo-500">ambitions</span></h1>
			<p class="text-gray-400 text-lg max-w-xl mx-auto">Choisissez le plan parfait pour stocker, exposer et sécuriser vos travaux photographiques.</p>
		</header>

		<!-- Grille des Offres -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8">
			<!-- Starter -->
			<div class="glass-effect p-8 rounded-3xl border border-gray-800 text-left flex flex-col justify-between shadow-xl">
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Starter</h3>
					<p class="text-xs text-gray-500 mb-6">Idéal pour débuter et partager vos premières galeries.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">0€</span>
						<span class="text-gray-500 text-sm ml-2">/ mois</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-400">
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Jusqu'à 100 médias</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Visibilité publique/privée</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Protection anti-copie basique</li>
						<li class="text-gray-600 line-through flex items-center"><span class="mr-2">&times;</span> Filigranes personnalisés</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="mt-8 w-full py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 text-white font-semibold rounded-xl text-center block transition-all cursor-pointer">Rejoindre</a>
			</div>

			<!-- Pro (Mis en avant) -->
			<div class="glass-effect p-8 rounded-3xl border-2 border-indigo-500 text-left flex flex-col justify-between shadow-2xl relative">
				<div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Le plus populaire</div>
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Pro</h3>
					<p class="text-xs text-gray-500 mb-6">Pour les photographes professionnels en activité.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">19€</span>
						<span class="text-gray-500 text-sm ml-2">/ mois</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-300">
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Médias illimités</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Visibilité et dossiers clients</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Protection + Filigrane dynamique</li>
						<li class="flex items-center"><span class="text-indigo-400 mr-2">&checkmark;</span> Support client prioritaire</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="mt-8 w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-center block transition-all shadow-lg cursor-pointer">Commencer Pro</a>
			</div>

			<!-- Studio -->
			<div class="glass-effect p-8 rounded-3xl border border-gray-800 text-left flex flex-col justify-between shadow-xl">
				<div>
					<h3 class="text-xl font-bold text-white mb-2">Studio</h3>
					<p class="text-xs text-gray-500 mb-6">Pour les agences et les équipes de créateurs.</p>
					<div class="flex items-baseline mb-8">
						<span class="text-4xl font-extrabold text-white">49€</span>
						<span class="text-gray-500 text-sm ml-2">/ mois</span>
					</div>
					<ul class="space-y-3 text-sm text-gray-400">
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Tout le plan Pro</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Gestion d'équipe (multi-comptes)</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Nom de domaine personnalisé</li>
						<li class="flex items-center"><span class="text-emerald-500 mr-2">&checkmark;</span> Stockage RAW haute performance</li>
					</ul>
				</div>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="mt-8 w-full py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 text-white font-semibold rounded-xl text-center block transition-all cursor-pointer">Contacter le studio</a>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
