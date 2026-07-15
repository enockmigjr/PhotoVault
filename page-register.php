<?php
/**
 * Template Name: PhotoVault Register
 *
 * @package PhotoVault
 */

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/dashboard/' ) );
	exit;
}

$register_status = isset( $_GET['register'] ) ? sanitize_key( wp_unslash( $_GET['register'] ) ) : '';
$error_code      = isset( $_GET['err'] ) ? sanitize_key( wp_unslash( $_GET['err'] ) ) : '';
$error_messages  = array(
	'fields_required'             => __( 'Remplissez tous les champs obligatoires.', 'photovault' ),
	'invalid_email'               => __( 'Cette adresse e-mail est invalide.', 'photovault' ),
	'phone_required'              => __( 'Le numéro de téléphone international est obligatoire.', 'photovault' ),
	'phone_country_code_required' => __( 'Ajoutez le préfixe international, par exemple +229.', 'photovault' ),
	'phone_invalid'               => __( 'Ce numéro de téléphone international est invalide.', 'photovault' ),
	'phone_exists'                => __( 'Ce numéro est déjà associé à un compte.', 'photovault' ),
	'phone_save_failed'           => __( 'Le numéro de téléphone n’a pas pu être enregistré.', 'photovault' ),
	'weak_password'               => __( 'Choisissez un mot de passe d’au moins 8 caractères.', 'photovault' ),
	'password_mismatch'           => __( 'Les deux mots de passe ne correspondent pas.', 'photovault' ),
	'email_exists'                => __( 'Cette adresse e-mail est déjà utilisée.', 'photovault' ),
	'username_exists'             => __( 'Ce nom d’utilisateur est déjà pris.', 'photovault' ),
	'failed'                      => __( 'L’inscription n’a pas abouti. Réessayez dans quelques instants.', 'photovault' ),
);
$error_message   = 'failed' === $register_status ? ( $error_messages[ $error_code ] ?? __( 'L’inscription n’a pas abouti.', 'photovault' ) ) : '';

get_header();
?>

<main class="pv-auth-shell pv-auth-shell--register">
	<section class="pv-auth-context" aria-labelledby="register-context-title">
		<div>
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'PhotoVault / Accès', 'photovault' ); ?></p>
			<h1 id="register-context-title" class="pv-auth-context__title"><?php esc_html_e( 'Entrer dans une archive pensée pour durer.', 'photovault' ); ?></h1>
		</div>
		<p class="pv-auth-context__copy"><?php esc_html_e( 'Un compte permet de demander l’accès aux collections privées, conserver des favoris et recevoir vos livrables.', 'photovault' ); ?></p>
	</section>

	<section class="pv-auth-form-wrap" aria-labelledby="register-title">
		<div class="w-full max-w-xl">
			<p class="pv-auth-eyebrow"><?php esc_html_e( 'Créer votre espace', 'photovault' ); ?></p>
			<h2 id="register-title" class="mt-3 text-4xl font-bold text-white"><?php esc_html_e( 'Rejoindre PhotoVault', 'photovault' ); ?></h2>
			<p class="mt-3 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Vos informations servent uniquement à sécuriser et personnaliser votre accès.', 'photovault' ); ?></p>

			<?php if ( $error_message ) : ?>
				<div class="pv-auth-notice is-error" role="alert" data-pv-toast>
					<span><?php echo esc_html( $error_message ); ?></span>
					<button type="button" class="pv-auth-notice__close" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg></button>
				</div>
			<?php endif; ?>

			<form class="mt-9 space-y-5" action="<?php echo esc_url( home_url( '/register/' ) ); ?>" method="post">
				<?php wp_nonce_field( 'photovault_register_action', 'photovault_register_nonce' ); ?>
				<div class="grid gap-5 sm:grid-cols-2">
					<div><label for="first_name" class="pv-auth-label"><?php esc_html_e( 'Prénom', 'photovault' ); ?></label><input id="first_name" name="first_name" type="text" autocomplete="given-name" required class="pv-auth-input" placeholder="Aïcha"></div>
					<div><label for="last_name" class="pv-auth-label"><?php esc_html_e( 'Nom', 'photovault' ); ?></label><input id="last_name" name="last_name" type="text" autocomplete="family-name" required class="pv-auth-input" placeholder="Mensah"></div>
				</div>
				<div><label for="username" class="pv-auth-label"><?php esc_html_e( 'Nom d’utilisateur', 'photovault' ); ?></label><input id="username" name="username" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" required class="pv-auth-input" placeholder="aicha.mensah"></div>
				<div><label for="email" class="pv-auth-label"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></label><input id="email" name="email" type="email" autocomplete="email" required class="pv-auth-input" placeholder="aicha@exemple.com"></div>
				<div>
					<label for="phone" class="pv-auth-label"><?php esc_html_e( 'Téléphone international', 'photovault' ); ?></label>
					<input id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" required class="pv-auth-input" aria-describedby="phone-help" placeholder="+229 01 23 45 67 89">
					<p id="phone-help" class="mt-2 text-xs leading-5 text-gray-500"><?php esc_html_e( 'Incluez le préfixe pays. Ce numéro ne remplace pas votre identifiant.', 'photovault' ); ?></p>
				</div>
				<div class="grid gap-5 sm:grid-cols-2">
					<div>
						<label for="password" class="pv-auth-label"><?php esc_html_e( 'Mot de passe', 'photovault' ); ?></label>
						<div class="relative"><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required class="pv-auth-input pr-12" placeholder="8 caractères minimum"><button type="button" class="pv-password-toggle" aria-label="<?php esc_attr_e( 'Afficher le mot de passe', 'photovault' ); ?>" aria-pressed="false" data-pv-password-toggle="password"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" /><circle cx="12" cy="12" r="2.5" /></svg></button></div>
					</div>
					<div>
						<label for="password_confirm" class="pv-auth-label"><?php esc_html_e( 'Confirmation', 'photovault' ); ?></label>
						<div class="relative"><input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" minlength="8" required class="pv-auth-input pr-12" placeholder="Répétez le mot de passe"><button type="button" class="pv-password-toggle" aria-label="<?php esc_attr_e( 'Afficher la confirmation', 'photovault' ); ?>" aria-pressed="false" data-pv-password-toggle="password_confirm"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" /><circle cx="12" cy="12" r="2.5" /></svg></button></div>
					</div>
				</div>
				<button type="submit" class="pv-auth-submit"><?php esc_html_e( 'Créer mon espace', 'photovault' ); ?></button>
			</form>

			<p class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400"><?php esc_html_e( 'Vous avez déjà un compte ?', 'photovault' ); ?> <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="font-bold text-white hover:text-indigo-400"><?php esc_html_e( 'Se connecter', 'photovault' ); ?></a></p>
		</div>
	</section>
</main>

<?php get_footer(); ?>
