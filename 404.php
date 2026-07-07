<?php
/**
 * Template de page introuvable (404.php) de PhotoVault.
 *
 * @package PhotoVault
 */

get_header();
?>

<div class="min-h-[85vh] flex flex-col items-center justify-center text-center px-4 bg-[#0d0c0b] relative overflow-hidden">
	<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-indigo-950/20 via-transparent to-transparent pointer-events-none"></div>
	
	<div class="glass-effect p-12 rounded-3xl max-w-lg w-full space-y-6 shadow-2xl relative z-10 border border-gray-800">
		<h1 class="text-8xl font-extrabold text-indigo-500 tracking-tight leading-none">404</h1>
		<h2 class="text-2xl font-extrabold text-white">Cliché introuvable</h2>
		<p class="text-gray-300 text-sm max-w-sm mx-auto leading-relaxed">
			La page que vous essayez d'afficher a peut-être été déplacée, supprimée ou n'existe tout simplement pas.
		</p>
		
		<div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
			<a href="<?php echo esc_url( home_url() ); ?>" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all shadow-lg cursor-pointer">
				Retour à l'accueil
			</a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-gray-200 font-semibold rounded-xl border border-gray-800 transition-all cursor-pointer">
				Explorer la galerie
			</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
