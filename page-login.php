<?php
/**
 * Template Name: PhotoVault Login
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	if ( current_user_can( 'manage_options' ) ) {
		wp_redirect( home_url( '/dashboard/' ) );
	} else {
		wp_redirect( get_post_type_archive_link( 'media_item' ) );
	}
	exit;
}

get_header();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-[#0d0c0b]">
	<div class="max-w-md w-full space-y-8 glass-effect p-8 rounded-2xl shadow-xl transition-all-300 hover:border-gray-700">
		<div>
			<h2 class="mt-6 text-center text-4xl font-extrabold text-white tracking-tight">
				Photo<span class="text-indigo-500">Vault</span>
			</h2>
			<p class="mt-2 text-center text-sm text-gray-300">
				Connectez-vous à votre espace client
			</p>
		</div>

		<?php if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) : ?>
			<div class="bg-red-900/30 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm text-center">
				Identifiants incorrects. Veuillez réessayer.
			</div>
		<?php endif; ?>

		<form class="mt-8 space-y-6" action="<?php echo esc_url( home_url( '/login/' ) ); ?>" method="POST">
			<?php wp_nonce_field( 'photovault_login_action', 'photovault_login_nonce' ); ?>
			
			<div class="rounded-md space-y-4">
				<div>
					<label for="username" class="block text-sm font-medium text-gray-200 mb-1">Identifiant ou E-mail</label>
					<input id="username" name="log" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="photographe@example.com">
				</div>
				<div>
					<div class="flex justify-between items-center mb-1">
						<label for="password" class="block text-sm font-medium text-gray-200">Mot de passe</label>
						<a href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Oublié ?</a>
					</div>
					<input id="password" name="pwd" type="password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="••••••••">
				</div>
			</div>

			<div class="flex items-center justify-between">
				<div class="flex items-center">
					<input id="rememberme" name="rememberme" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-800 rounded bg-gray-900/50">
					<label for="rememberme" class="ml-2 block text-sm text-gray-200">Se souvenir de moi</label>
				</div>
			</div>

			<div>
				<button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
					Connexion
				</button>
			</div>
		</form>

		<div class="text-center mt-4">
			<span class="text-sm text-gray-300">Nouveau sur la plateforme ?</span>
			<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 ml-1 transition-colors">Créer un compte</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
