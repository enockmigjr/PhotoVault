<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[#0d0c0b] text-gray-100 min-h-screen flex flex-col justify-between' ); ?>>
<?php wp_body_open(); ?>

<?php
// Ne pas afficher le menu public sur les templates de dashboard
$is_dashboard_template = is_page_template( 'page-dashboard.php' );
?>

<?php if ( ! $is_dashboard_template ) : ?>
	<header class="glass-effect sticky top-0 z-50 transition-all duration-300">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between items-center h-20">
				<!-- Logo -->
				<div class="flex-shrink-0">
					<a href="<?php echo esc_url( home_url() ); ?>" class="text-2xl font-extrabold text-white tracking-tight">
						Photo<span class="text-indigo-500">Vault</span>
					</a>
				</div>

				<!-- Navigation publique -->
				<nav class="hidden md:flex space-x-8 items-center">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="text-sm font-medium text-gray-200 hover:text-white transition-colors">Explorer</a>
					<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="text-sm font-medium text-gray-200 hover:text-white transition-colors">Tarifs</a>
					<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="text-sm font-medium text-gray-200 hover:text-white transition-colors">À Propos</a>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="text-sm font-medium text-gray-200 hover:text-white transition-colors">Contact</a>
				</nav>

				<!-- Boutons Connexion / Dashboard -->
				<div class="hidden md:flex items-center space-x-4">
					<?php if ( is_user_logged_in() ) : ?>
						<?php if ( current_user_can( 'manage_options' ) ) : ?>
							<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg border border-indigo-400/20 cursor-pointer">
								Dashboard
							</a>
						<?php endif; ?>
						<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="text-sm font-medium text-gray-300 hover:text-red-400 transition-colors">
							Déconnexion
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="text-sm font-semibold text-gray-200 hover:text-white transition-colors">
							Connexion
						</a>
						<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg border border-indigo-400/20 cursor-pointer">
							Rejoindre
						</a>
					<?php endif; ?>
				</div>

				<!-- Bouton Hamburger mobile -->
				<div class="flex md:hidden">
					<button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-300 hover:text-white hover:bg-gray-800/50 focus:outline-none transition-all cursor-pointer">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="hamburger-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
						<svg class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="close-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
					</button>
				</div>
			</div>
		</div>

	</header>

	<!-- Menu Mobile (placé en dehors du header pour éviter les conflits de contenant de position:fixed) -->
	<div id="mobile-menu" class="hidden md:hidden fixed inset-x-0 bottom-0 top-20 z-[999] overflow-y-auto flex flex-col bg-[#0d0c0b] border-t border-gray-900 px-6 pt-6 pb-8 space-y-6 shadow-2xl transition-all duration-300 transform scale-y-95 origin-top opacity-0">
		<nav class="flex flex-col space-y-3">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="px-4 py-3 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-gray-800/40 transition-all">Explorer</a>
			<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="px-4 py-3 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-gray-800/40 transition-all">Tarifs</a>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="px-4 py-3 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-gray-800/40 transition-all">À Propos</a>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="px-4 py-3 rounded-xl text-sm font-semibold text-gray-200 hover:text-white hover:bg-gray-800/40 transition-all">Contact</a>
		</nav>
		
		<div class="border-t border-gray-900 pt-4 flex flex-col gap-3 px-4">
			<?php if ( is_user_logged_in() ) : ?>
				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="w-full py-3 text-center text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg shadow-indigo-500/20 border border-indigo-400/20">
						Dashboard
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="w-full py-3 text-center text-sm font-semibold text-gray-300 hover:text-red-400 hover:bg-red-950/20 rounded-xl transition-all border border-gray-800/50">
					Déconnexion
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="w-full py-3 text-center text-sm font-bold text-gray-200 hover:text-white rounded-xl transition-all">
					Connexion
				</a>
				<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="w-full py-3 text-center text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg shadow-indigo-500/20 border border-indigo-400/20">
					Rejoindre
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
