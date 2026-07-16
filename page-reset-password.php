<?php
/**
 * Template Name: PhotoVault Reset Password
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/dashboard/' ) );
	exit;
}

$key    = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$login  = isset( $_GET['login'] ) ? sanitize_user( wp_unslash( $_GET['login'] ) ) : '';
$status = isset( $_GET['reset'] ) ? sanitize_key( wp_unslash( $_GET['reset'] ) ) : '';
$user   = $key && $login ? check_password_reset_key( $key, $login ) : new WP_Error( 'invalid_key' );
$valid  = $user instanceof WP_User;
$min_password_length = function_exists( 'identity_security_kit_get_min_password_length' ) ? identity_security_kit_get_min_password_length() : 12;
$messages = array(
	'weak_password'   => __( 'Choisissez un mot de passe plus long et difficile à deviner.', 'photovault' ),
	'password_mismatch' => __( 'Les deux mots de passe ne correspondent pas.', 'photovault' ),
	'security_failed' => __( 'La vérification de sécurité a échoué. Rechargez la page puis réessayez.', 'photovault' ),
);
$notice = isset( $messages[ $status ] ) ? $messages[ $status ] : '';

get_header();
?>

<main class="pv-auth-shell">
	<section class="pv-auth-context" aria-labelledby="reset-context-title">
		<div>
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'PhotoVault / Sécurité', 'photovault' ); ?></p>
			<h1 id="reset-context-title" class="pv-auth-context__title"><?php esc_html_e( 'Protéger la suite de votre histoire.', 'photovault' ); ?></h1>
		</div>
		<p class="pv-auth-context__copy"><?php esc_html_e( 'Ce lien est temporaire et ne peut servir qu’une fois. Choisissez un mot de passe unique que vous n’utilisez sur aucun autre service.', 'photovault' ); ?></p>
	</section>

	<section class="pv-auth-form-wrap" aria-labelledby="reset-title">
		<div class="w-full max-w-md">
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'Récupération protégée', 'photovault' ); ?></p>
			<h2 id="reset-title" class="mt-3 text-4xl font-bold text-white"><?php esc_html_e( 'Nouveau mot de passe', 'photovault' ); ?></h2>

			<?php if ( ! $valid ) : ?>
				<div class="pv-auth-notice is-error" role="alert"><span><?php esc_html_e( 'Ce lien est invalide ou a expiré.', 'photovault' ); ?></span></div>
				<a class="pv-auth-submit mt-8" href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>"><?php esc_html_e( 'Demander un nouveau lien', 'photovault' ); ?></a>
			<?php else : ?>
				<p class="mt-3 text-sm leading-6 text-gray-400"><?php echo esc_html( sprintf( __( 'Utilisez au moins %d caractères, idéalement une phrase de passe.', 'photovault' ), $min_password_length ) ); ?></p>
				<?php if ( $notice ) : ?><div class="pv-auth-notice is-error" role="alert" data-pv-toast><span><?php echo esc_html( $notice ); ?></span><button type="button" class="pv-auth-notice__close" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close>&times;</button></div><?php endif; ?>
				<form class="mt-9 space-y-6" action="<?php echo esc_url( add_query_arg( array( 'key' => $key, 'login' => $login ), home_url( '/reset-password/' ) ) ); ?>" method="post">
					<?php wp_nonce_field( 'photovault_reset_action', 'photovault_reset_nonce' ); ?>
					<input type="hidden" name="rp_key" value="<?php echo esc_attr( $key ); ?>">
					<input type="hidden" name="rp_login" value="<?php echo esc_attr( $login ); ?>">
					<div><label for="reset-password" class="pv-auth-label"><?php esc_html_e( 'Nouveau mot de passe', 'photovault' ); ?></label><input id="reset-password" name="password" type="password" autocomplete="new-password" minlength="<?php echo esc_attr( $min_password_length ); ?>" required class="pv-auth-input"></div>
					<div><label for="reset-password-confirm" class="pv-auth-label"><?php esc_html_e( 'Confirmer le mot de passe', 'photovault' ); ?></label><input id="reset-password-confirm" name="password_confirm" type="password" autocomplete="new-password" minlength="<?php echo esc_attr( $min_password_length ); ?>" required class="pv-auth-input"></div>
					<button type="submit" class="pv-auth-submit"><?php esc_html_e( 'Enregistrer le nouveau mot de passe', 'photovault' ); ?></button>
				</form>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
