<?php
/**
 * Template Name: PhotoVault Register
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_redirect( home_url( '/dashboard/' ) );
	exit;
}

get_header();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-[#0b0f19]">
	<div class="max-w-md w-full space-y-8 glass-effect p-8 rounded-2xl shadow-xl transition-all-300 hover:border-gray-700">
		<div>
			<h2 class="mt-6 text-center text-4xl font-extrabold text-white tracking-tight">
				Rejoindre <span class="text-indigo-500">PhotoVault</span>
			</h2>
			<p class="mt-2 text-center text-sm text-gray-400">
				Créez votre profil de photographe professionnel
			</p>
		</div>

		<?php if ( isset( $_GET['register'] ) && 'failed' === $_GET['register'] ) : ?>
			<div class="bg-red-900/30 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm text-center">
				<?php echo isset( $_GET['msg'] ) ? esc_html( urldecode( $_GET['msg'] ) ) : esc_html__( 'Erreur d\'inscription.', 'photovault' ); ?>
			</div>
		<?php endif; ?>

		<form class="mt-8 space-y-4" action="<?php echo esc_url( home_url( '/register/' ) ); ?>" method="POST">
			<?php wp_nonce_field( 'photovault_register_action', 'photovault_register_nonce' ); ?>
			
			<div class="grid grid-cols-2 gap-4">
				<div>
					<label for="first_name" class="block text-sm font-medium text-gray-300 mb-1">Prénom</label>
					<input id="first_name" name="first_name" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Jean">
				</div>
				<div>
					<label for="last_name" class="block text-sm font-medium text-gray-300 mb-1">Nom</label>
					<input id="last_name" name="last_name" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Dupont">
				</div>
			</div>

			<div>
				<label for="username" class="block text-sm font-medium text-gray-300 mb-1">Pseudo / Nom d'utilisateur</label>
				<input id="username" name="username" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="jeandupont">
			</div>

			<div>
				<label for="email" class="block text-sm font-medium text-gray-300 mb-1">Adresse E-mail</label>
				<input id="email" name="email" type="email" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="jean.dupont@example.com">
			</div>

			<div class="grid grid-cols-2 gap-4">
				<div>
					<label for="password" class="block text-sm font-medium text-gray-300 mb-1">Mot de passe</label>
					<input id="password" name="password" type="password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="••••••••">
				</div>
				<div>
					<label for="password_confirm" class="block text-sm font-medium text-gray-300 mb-1">Confirmation</label>
					<input id="password_confirm" name="password_confirm" type="password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="••••••••">
				</div>
			</div>

			<div class="pt-2">
				<button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
					Créer mon compte
				</button>
			</div>
		</form>

		<div class="text-center mt-4">
			<span class="text-sm text-gray-400">Déjà inscrit ?</span>
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 ml-1 transition-colors">Connexion</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
