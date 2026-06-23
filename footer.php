<?php
/**
 * Pied de page global du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_dashboard_template = is_page_template( 'page-dashboard.php' ) || 
                         is_page_template( 'page-my-media.php' ) || 
                         is_page_template( 'page-upload-media.php' ) || 
                         is_page_template( 'page-profile.php' );
?>

<?php if ( ! $is_dashboard_template ) : ?>
	<footer class="bg-[#070a12] border-t border-gray-900 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
			<div>
				<a href="<?php echo esc_url( home_url() ); ?>" class="text-xl font-extrabold text-white tracking-tight">
					Photo<span class="text-indigo-500">Vault</span>
				</a>
				<p class="text-xs text-gray-500 mt-2">&copy; <?php echo date( 'Y' ); ?> PhotoVault. Tous droits réservés.</p>
			</div>
			
			<div class="flex space-x-6 text-sm text-gray-400">
				<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="hover:text-white transition-colors">Offres</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="hover:text-white transition-colors">À propos</a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="hover:text-white transition-colors">Support</a>
			</div>
		</div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
