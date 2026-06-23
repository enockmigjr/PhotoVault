<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[#0b0f19] text-gray-100 min-h-screen flex flex-col justify-between' ); ?>>
<?php wp_body_open(); ?>

<?php
// Ne pas afficher le menu public sur les templates de dashboard
$is_dashboard_template = is_page_template( 'page-dashboard.php' ) || 
                         is_page_template( 'page-my-media.php' ) || 
                         is_page_template( 'page-upload-media.php' ) || 
                         is_page_template( 'page-profile.php' );
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
					<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Explorer</a>
					<a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Tarifs</a>
					<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">À Propos</a>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Contact</a>
				</nav>

				<!-- Boutons Connexion / Dashboard -->
				<div class="hidden md:flex items-center space-x-4">
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg border border-indigo-400/20 cursor-pointer">
							Dashboard
						</a>
						<a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>" class="text-sm font-medium text-gray-400 hover:text-red-400 transition-colors">
							Déconnexion
						</a>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="text-sm font-semibold text-gray-300 hover:text-white transition-colors">
							Connexion
						</a>
						<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition-all shadow-lg border border-indigo-400/20 cursor-pointer">
							Rejoindre
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>
<?php endif; ?>
