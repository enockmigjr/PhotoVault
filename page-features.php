<?php
/**
 * Template Name: PhotoVault Features
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="py-20 bg-[#0d0c0b] min-h-screen text-gray-200">
	<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
		<header class="max-w-3xl space-y-4">
			<span class="text-xs font-bold uppercase tracking-wider text-indigo-400">Fonctionnalites</span>
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight">Une galerie client pensee pour les images haute definition</h1>
			<p class="text-gray-400 text-lg leading-relaxed">PhotoVault organise vos collections, protege les apercus publics et reserve les fichiers originaux aux telechargements autorises.</p>
		</header>

		<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<div class="glass-effect p-7 rounded-3xl border border-gray-800/80 space-y-4">
				<div class="h-11 w-11 rounded-2xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4-4a2 2 0 012.8 0l1.2 1.2M14 14l1-1a2 2 0 012.8 0L20 15m-1-9H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2z"></path></svg>
				</div>
				<h2 class="text-xl font-bold text-white">Apercus legers</h2>
				<p class="text-sm text-gray-400 leading-relaxed">Les pages de galerie affichent des miniatures adaptees a la grille. Les originaux restent hors du navigateur pendant la navigation.</p>
			</div>

			<div class="glass-effect p-7 rounded-3xl border border-gray-800/80 space-y-4">
				<div class="h-11 w-11 rounded-2xl bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
				</div>
				<h2 class="text-xl font-bold text-white">Protection integree</h2>
				<p class="text-sm text-gray-400 leading-relaxed">Les medias proteges passent par un proxy qui controle les droits, applique le filigrane serveur et bloque le telechargement non autorise.</p>
			</div>

			<div class="glass-effect p-7 rounded-3xl border border-gray-800/80 space-y-4">
				<div class="h-11 w-11 rounded-2xl bg-amber-600/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
				</div>
				<h2 class="text-xl font-bold text-white">HD sur demande</h2>
				<p class="text-sm text-gray-400 leading-relaxed">Le fichier original n'est servi qu'au moment du telechargement, apres verification de la session et du statut de protection.</p>
			</div>
		</section>

		<section class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start border-t border-gray-900 pt-14">
			<div class="space-y-4">
				<h2 class="text-3xl font-extrabold text-white">Un flux simple pour chaque livraison</h2>
				<p class="text-gray-400 leading-relaxed">Importez vos images, classez-les par dossier client ou categorie, choisissez le niveau de protection, puis laissez PhotoVault presenter les apercus et gerer les droits d'acces.</p>
			</div>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
				<div class="p-5 rounded-2xl bg-gray-950/40 border border-gray-800"><strong class="block text-white mb-1">01. Import</strong><span class="text-gray-400">Ajout depuis l'admin WordPress.</span></div>
				<div class="p-5 rounded-2xl bg-gray-950/40 border border-gray-800"><strong class="block text-white mb-1">02. Classement</strong><span class="text-gray-400">Dossiers projets et categories.</span></div>
				<div class="p-5 rounded-2xl bg-gray-950/40 border border-gray-800"><strong class="block text-white mb-1">03. Consultation</strong><span class="text-gray-400">Apercus rapides et filigranes.</span></div>
				<div class="p-5 rounded-2xl bg-gray-950/40 border border-gray-800"><strong class="block text-white mb-1">04. Livraison</strong><span class="text-gray-400">Telechargement HD autorise.</span></div>
			</div>
		</section>
	</div>
</div>

<?php get_footer(); ?>