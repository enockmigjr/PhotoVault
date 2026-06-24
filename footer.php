<?php
/**
 * Pied de page global du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$is_dashboard_template = is_page_template( 'page-dashboard.php' );
?>

<?php if ( ! $is_dashboard_template ) : ?>
	<footer class="bg-[#070a12] text-gray-400 pt-16 pb-8 border-t border-gray-900 mt-auto font-sans">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
				
				<div class="space-y-4">
					<div class="text-white text-xl font-black tracking-wider uppercase">
						EXO<span class="text-indigo-500">PAUL</span>
					</div>
					<p class="text-xs leading-relaxed text-gray-500 font-medium">
						Une plateforme moderne pensée pour l'avenir de l'intégration et du développement web. Sécurisée, rapide et intuitive.
					</p>
				</div>

				<div>
					<h4 class="text-white font-bold mb-4 tracking-widest uppercase text-xs">Navigation</h4>
					<ul class="space-y-2 text-xs font-semibold">
						<li>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Accueil
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'fonctionnalites' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Fonctionnalités
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								À propos
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Contact
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Galerie
							</a>
						</li>
					</ul>
				</div>

				<div>
					<h4 class="text-white font-bold mb-4 tracking-widest uppercase text-xs">Légal</h4>
					<ul class="space-y-2 text-xs font-semibold">
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'mentions-legales' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Mentions Légales
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'politique-de-confidentialite' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Politique de Confidentialité
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'conditions-generales' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Conditions Générales
							</a>
						</li>
					</ul>
				</div>

				<div class="space-y-4">
					<h4 class="text-white font-bold mb-2 tracking-widest uppercase text-xs">Restez connecté</h4>
					<p class="text-xs text-gray-500 font-medium font-medium">Inscrivez-vous pour suivre nos dernières mises à jour.</p>
					<form class="flex flex-col sm:flex-row gap-2" onsubmit="alert('Inscription à la newsletter simulée avec succès !'); return false;">
						<input type="email" placeholder="Votre email..." class="bg-gray-950/40 text-white placeholder-gray-600 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-gray-800 w-full" required>
						<button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-md cursor-pointer">
							OK
						</button>
					</form>
				</div>

			</div>

			<div class="border-t border-gray-900 pt-8 mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] text-gray-600">
				<div>
					&copy; <?php echo date('Y'); ?> <span class="text-gray-500 font-bold">EXO PAUL</span>. Tous droits réservés.
				</div>
				<div>
					Développé par <a href="https://github.com/enockmigjr" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-indigo-400 font-bold transition-colors">Enok Junior MIGNANWANDE</a>
				</div>
			</div>

		</div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
