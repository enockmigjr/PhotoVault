<?php
/**
 * Template Name: PhotoVault Forgot Password
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/dashboard/' ) );
	exit;
}

$status   = isset( $_GET['forgot'] ) ? sanitize_key( wp_unslash( $_GET['forgot'] ) ) : '';
$messages = array(
	'sent'            => array(
		'type' => 'success',
		'text' => __( 'Si un compte correspond a ces informations, un e-mail de reinitialisation vient d\'etre envoye.', 'photovault' ),
	),
	'fields_required' => array(
		'type' => 'error',
		'text' => __( 'Veuillez saisir votre identifiant ou votre adresse e-mail.', 'photovault' ),
	),
	'security_failed' => array(
		'type' => 'error',
		'text' => __( 'Verification de securite impossible. Rechargez la page puis reessayez.', 'photovault' ),
	),
);

get_header();
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8 bg-[#0d0c0b]">
	<div class="max-w-md w-full space-y-8 glass-effect p-8 rounded-2xl shadow-xl transition-all-300 hover:border-gray-700">
		<div>
			<h2 class="mt-6 text-center text-4xl font-extrabold text-white tracking-tight">
				Mot de passe oublie
			</h2>
			<p class="mt-2 text-center text-sm text-gray-300">
				Saisissez vos informations pour recevoir un lien de reinitialisation securise.
			</p>
		</div>

		<?php if ( isset( $messages[ $status ] ) ) : ?>
			<?php $notice = $messages[ $status ]; ?>
			<div class="<?php echo 'success' === $notice['type'] ? 'bg-emerald-900/30 border-emerald-500 text-emerald-200' : 'bg-red-900/30 border-red-500 text-red-200'; ?> border px-4 py-3 rounded-lg text-sm text-center">
				<?php echo esc_html( $notice['text'] ); ?>
			</div>
		<?php endif; ?>

		<form class="mt-8 space-y-6" action="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>" method="POST">
			<?php wp_nonce_field( 'photovault_forgot_action', 'photovault_forgot_nonce' ); ?>

			<div>
				<label for="user_login" class="block text-sm font-medium text-gray-200 mb-1">Identifiant ou E-mail</label>
				<input id="user_login" name="user_login" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-gray-800 placeholder-gray-500 text-white bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="photographe@example.com">
			</div>

			<div>
				<button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all cursor-pointer">
					Envoyer le lien de reinitialisation
				</button>
			</div>
		</form>

		<div class="text-center mt-4">
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Retour a la connexion</a>
		</div>
	</div>
</div>

<?php get_footer(); ?>
