<?php
/**
 * Template Name: PhotoVault Login
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/dashboard/' ) );
	exit;
}

$verify          = isset( $_GET['verify'] ) ? sanitize_key( wp_unslash( $_GET['verify'] ) ) : '';
$login_status    = isset( $_GET['login'] ) ? sanitize_key( wp_unslash( $_GET['login'] ) ) : '';
$redirect_to     = isset( $_GET['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' ) : '';
$verify_messages = array(
	'success' => array( 'type' => 'success', 'text' => __( 'Votre adresse e-mail est vérifiée. Vous pouvez maintenant vous connecter.', 'photovault' ) ),
	'invalid' => array( 'type' => 'error', 'text' => __( 'Ce lien de vérification est invalide.', 'photovault' ) ),
	'expired' => array( 'type' => 'error', 'text' => __( 'Ce lien a expiré. Connectez-vous puis demandez un nouveau lien depuis votre profil.', 'photovault' ) ),
);
$notice          = isset( $verify_messages[ $verify ] ) ? $verify_messages[ $verify ] : null;
if ( 'failed' === $login_status ) {
	$notice = array( 'type' => 'error', 'text' => __( 'L’identifiant ou le mot de passe est incorrect.', 'photovault' ) );
}

get_header();
?>

<main class="pv-auth-shell">
	<section class="pv-auth-context" aria-labelledby="login-context-title">
		<div>
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'PhotoVault / Espace privé', 'photovault' ); ?></p>
			<h1 id="login-context-title" class="pv-auth-context__title"><?php esc_html_e( 'Retrouver les images qui vous sont confiées.', 'photovault' ); ?></h1>
		</div>
		<p class="pv-auth-context__copy"><?php esc_html_e( 'Votre espace rassemble vos collections autorisées, vos favoris, vos téléchargements et le suivi de vos séances.', 'photovault' ); ?></p>
	</section>

	<section class="pv-auth-form-wrap" aria-labelledby="login-title">
		<div class="w-full max-w-md">
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'Accès sécurisé', 'photovault' ); ?></p>
			<h2 id="login-title" class="mt-3 text-4xl font-bold text-white"><?php esc_html_e( 'Connexion', 'photovault' ); ?></h2>
			<p class="mt-3 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Utilisez votre identifiant ou votre adresse e-mail.', 'photovault' ); ?></p>

			<?php if ( $notice ) : ?>
				<div class="pv-auth-notice <?php echo 'success' === $notice['type'] ? 'is-success' : 'is-error'; ?>" role="<?php echo 'error' === $notice['type'] ? 'alert' : 'status'; ?>" data-pv-toast>
					<span><?php echo esc_html( $notice['text'] ); ?></span>
					<button type="button" class="pv-auth-notice__close" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg></button>
				</div>
			<?php endif; ?>

			<form class="mt-9 space-y-6" action="<?php echo esc_url( home_url( '/login/' ) ); ?>" method="post">
				<?php wp_nonce_field( 'photovault_login_action', 'photovault_login_nonce' ); ?>
				<?php if ( $redirect_to ) : ?><input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>"><?php endif; ?>
				<div>
					<label for="username" class="pv-auth-label"><?php esc_html_e( 'Identifiant ou e-mail', 'photovault' ); ?></label>
					<input id="username" name="log" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" required class="pv-auth-input" placeholder="vous@exemple.com">
				</div>
				<div>
					<div class="mb-2 flex items-center justify-between gap-4">
						<label for="password" class="pv-auth-label mb-0"><?php esc_html_e( 'Mot de passe', 'photovault' ); ?></label>
						<a href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300"><?php esc_html_e( 'Mot de passe oublié ?', 'photovault' ); ?></a>
					</div>
					<div class="relative">
						<input id="password" name="pwd" type="password" autocomplete="current-password" required class="pv-auth-input pr-12" placeholder="••••••••">
						<button type="button" class="pv-password-toggle" aria-label="<?php esc_attr_e( 'Afficher le mot de passe', 'photovault' ); ?>" aria-pressed="false" data-pv-password-toggle="password"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" /><circle cx="12" cy="12" r="2.5" /></svg></button>
					</div>
				</div>
				<label class="flex min-h-11 items-center gap-3 text-sm text-gray-300">
					<input id="rememberme" name="rememberme" type="checkbox" class="h-4 w-4 border-gray-700 bg-gray-950 text-indigo-500">
					<span><?php esc_html_e( 'Rester connecté sur cet appareil', 'photovault' ); ?></span>
				</label>
				<button type="submit" class="pv-auth-submit"><?php esc_html_e( 'Ouvrir mon espace', 'photovault' ); ?></button>
			</form>

			<p class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400"><?php esc_html_e( 'Pas encore de compte ?', 'photovault' ); ?> <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>" class="font-bold text-white hover:text-indigo-400"><?php esc_html_e( 'Créer un compte', 'photovault' ); ?></a></p>
		</div>
	</section>
</main>

<?php get_footer(); ?>
