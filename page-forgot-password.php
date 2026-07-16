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
$reset    = isset( $_GET['reset'] ) ? sanitize_key( wp_unslash( $_GET['reset'] ) ) : '';
$messages = array(
	'sent'            => array( 'type' => 'success', 'text' => __( 'Si un compte correspond, un e-mail de réinitialisation vient d’être envoyé.', 'photovault' ) ),
	'fields_required' => array( 'type' => 'error', 'text' => __( 'Saisissez votre identifiant ou votre adresse e-mail.', 'photovault' ) ),
	'security_failed' => array( 'type' => 'error', 'text' => __( 'La vérification de sécurité a échoué. Rechargez la page puis réessayez.', 'photovault' ) ),
);
$notice   = isset( $messages[ $status ] ) ? $messages[ $status ] : null;
if ( 'invalid' === $reset ) {
	$notice = array( 'type' => 'error', 'text' => __( 'Ce lien de réinitialisation est invalide ou a expiré. Demandez-en un nouveau.', 'photovault' ) );
}

get_header();
?>

<main class="pv-auth-shell">
	<section class="pv-auth-context" aria-labelledby="forgot-context-title">
		<div>
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'PhotoVault / Sécurité', 'photovault' ); ?></p>
			<h1 id="forgot-context-title" class="pv-auth-context__title"><?php esc_html_e( 'Reprendre l’accès sans exposer votre compte.', 'photovault' ); ?></h1>
		</div>
		<p class="pv-auth-context__copy"><?php esc_html_e( 'La réponse reste volontairement identique, qu’une adresse soit connue ou non.', 'photovault' ); ?></p>
	</section>

	<section class="pv-auth-form-wrap" aria-labelledby="forgot-title">
		<div class="w-full max-w-md">
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="inline-flex min-h-11 items-center text-sm font-semibold text-gray-400 hover:text-white"><span class="mr-2" aria-hidden="true">&larr;</span><?php esc_html_e( 'Retour à la connexion', 'photovault' ); ?></a>
			<p class="pv-auth-eyebrow mt-8"><?php esc_html_e( 'Récupération', 'photovault' ); ?></p>
			<h2 id="forgot-title" class="mt-3 text-4xl font-bold text-white"><?php esc_html_e( 'Mot de passe oublié', 'photovault' ); ?></h2>
			<p class="mt-3 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Nous enverrons un lien temporaire à l’adresse associée au compte.', 'photovault' ); ?></p>

			<?php if ( $notice ) : ?>
				<div class="pv-auth-notice <?php echo 'success' === $notice['type'] ? 'is-success' : 'is-error'; ?>" role="<?php echo 'error' === $notice['type'] ? 'alert' : 'status'; ?>" data-pv-toast>
					<span><?php echo esc_html( $notice['text'] ); ?></span>
					<button type="button" class="pv-auth-notice__close" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg></button>
				</div>
			<?php endif; ?>

			<form class="mt-9 space-y-6" action="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>" method="post">
				<?php wp_nonce_field( 'photovault_forgot_action', 'photovault_forgot_nonce' ); ?>
				<div>
					<label for="user_login" class="pv-auth-label"><?php esc_html_e( 'Identifiant ou e-mail', 'photovault' ); ?></label>
					<input id="user_login" name="user_login" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" required class="pv-auth-input" placeholder="vous@exemple.com">
				</div>
				<button type="submit" class="pv-auth-submit"><?php esc_html_e( 'Envoyer le lien sécurisé', 'photovault' ); ?></button>
			</form>
		</div>
	</section>
</main>

<?php get_footer(); ?>
