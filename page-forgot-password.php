<?php
/**
 * Template Name: PhotoVault Forgot Password
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_redirect( home_url( '/dashboard/' ) );
	exit;
}

$message = '';
$error   = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['photovault_forgot_nonce'] ) ) {
	if ( wp_verify_nonce( $_POST['photovault_forgot_nonce'], 'photovault_forgot_action' ) ) {
		$user_input = sanitize_text_field( $_POST['user_login'] );
		
		if ( empty( $user_input ) ) {
			$error = 'Veuillez saisir votre identifiant ou e-mail.';
		} else {
			$user_data = get_user_by( 'email', $user_input );
			if ( ! $user_data ) {
				$user_data = get_user_by( 'login', $user_input );
			}
			
			if ( $user_data ) {
				// Utiliser la fonction native de WordPress pour envoyer l'email de réinitialisation.
				$retrieve = retrieve_password( $user_data->user_login );
				if ( is_wp_error( $retrieve ) ) {
					$error = $retrieve->get_error_message();
				} else {
					$message = 'Un e-mail de réinitialisation a été envoyé à votre adresse.';
				}
			} else {
				$error = 'Aucun utilisateur trouvé avec ces informations.';
			}
		}
	} else {
		$error = 'Échec de la vérification de sécurité.';
	}
}

get_header();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-[#0b0f19]">
	<div class="max-w-md w-full space-y-8 glass-effect p-8 rounded-2xl shadow-xl transition-all-300 hover:border-gray-700">
		<div>
			<h2 class="mt-6 text-center text-4xl font-extrabold text-white tracking-tight">
				Mot de passe oublié
			</h2>
			<p class="mt-2 text-center text-sm text-gray-400">
				Saisissez vos informations pour réinitialiser votre mot de passe
			</p>
		</div>

		<?php if ( ! empty( $error ) ) : ?>
			<div class="bg-red-900/30 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm text-center">
				<?php echo esc_html( $error ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $message ) ) : ?>
			<div class="bg-emerald-900/30 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-lg text-sm text-center">
				<?php echo esc_html( $message ); ?>
			</div>
		<?php endif; ?>

		<form class="mt-8 space-y-6" action="" method="POST">
			<?php wp_nonce_field( 'photovault_forgot_action', 'photovault_forgot_nonce' ); ?>
			
			<div>
				<label for="user_login" class="block text-sm font-medium text-gray-300 mb-1">Identifiant ou E-mail</label>
				<input id="user_login" name="user_login" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="photographe@example.com">
			</div>

			<div>
				<button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
					Envoyer le lien de réinitialisation
				</button>
			</div>
		</form>

		<div class="text-center mt-4">
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Retour à la connexion</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
