<?php
/**
 * Template Name: PhotoVault Profile
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/login/' ) );
	exit;
}

$current_user = wp_get_current_user();
$avatar_id    = get_user_meta( $current_user->ID, 'photovault_avatar_id', true );
$avatar_url   = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $current_user->ID, array( 'size' => 160 ) );
$status       = isset( $_GET['profile'] ) ? sanitize_key( wp_unslash( $_GET['profile'] ) ) : '';
$messages     = array(
	'success'                  => array( 'type' => 'success', 'text' => __( 'Profil mis a jour.', 'photovault' ) ),
	'invalid_email'            => array( 'type' => 'error', 'text' => __( 'Adresse e-mail invalide.', 'photovault' ) ),
	'email_exists'             => array( 'type' => 'error', 'text' => __( 'Cette adresse e-mail est deja utilisee.', 'photovault' ) ),
	'current_password_invalid' => array( 'type' => 'error', 'text' => __( 'Mot de passe actuel incorrect.', 'photovault' ) ),
	'weak_password'            => array( 'type' => 'error', 'text' => __( 'Le nouveau mot de passe doit contenir au moins 8 caracteres.', 'photovault' ) ),
	'pwd_mismatch'             => array( 'type' => 'error', 'text' => __( 'Les nouveaux mots de passe ne correspondent pas.', 'photovault' ) ),
	'file_too_large'           => array( 'type' => 'error', 'text' => __( 'L\'avatar est trop volumineux.', 'photovault' ) ),
	'invalid_file_type'        => array( 'type' => 'error', 'text' => __( 'Type d\'image non autorise.', 'photovault' ) ),
	'invalid_image'            => array( 'type' => 'error', 'text' => __( 'Le fichier envoye n\'est pas une image valide.', 'photovault' ) ),
	'image_too_large'          => array( 'type' => 'error', 'text' => __( 'Les dimensions de l\'image sont trop grandes.', 'photovault' ) ),
	'avatar_upload_failed'     => array( 'type' => 'error', 'text' => __( 'L\'avatar n\'a pas pu etre enregistre.', 'photovault' ) ),
	'failed'                   => array( 'type' => 'error', 'text' => __( 'La mise a jour du profil a echoue.', 'photovault' ) ),
);

get_header();
?>

<main class="min-h-screen bg-[#0d0c0b] px-4 py-12 text-white sm:px-6 lg:px-8">
	<div class="mx-auto max-w-5xl space-y-8">
		<header class="flex flex-col gap-6 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
			<div>
				<p class="text-xs font-bold uppercase tracking-[0.28em] text-amber-300">Espace personnel</p>
				<h1 class="mt-3 text-4xl font-black tracking-tight md:text-5xl">Mon profil</h1>
				<p class="mt-3 max-w-2xl text-sm leading-7 text-gray-300">Gerez vos informations, votre avatar et vos parametres d'acces avec une validation serveur complete.</p>
			</div>
			<a class="inline-flex items-center justify-center rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-white transition hover:border-amber-300 hover:text-amber-200" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
				<?php esc_html_e( 'Retour au tableau de bord', 'photovault' ); ?>
			</a>
		</header>

		<?php if ( $status && isset( $messages[ $status ] ) ) : ?>
			<?php $notice = $messages[ $status ]; ?>
			<div class="rounded-xl border px-5 py-4 text-sm <?php echo 'success' === $notice['type'] ? 'border-emerald-400/40 bg-emerald-500/10 text-emerald-100' : 'border-red-400/40 bg-red-500/10 text-red-100'; ?>">
				<?php echo esc_html( $notice['text'] ); ?>
			</div>
		<?php endif; ?>

		<section class="grid gap-8 lg:grid-cols-[320px,1fr]">
			<aside class="rounded-2xl border border-white/10 bg-white/[0.04] p-6">
				<img class="h-40 w-40 rounded-full object-cover ring-1 ring-white/20" src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>">
				<h2 class="mt-6 text-2xl font-bold"><?php echo esc_html( $current_user->display_name ? $current_user->display_name : $current_user->user_login ); ?></h2>
				<p class="mt-2 text-sm text-gray-400"><?php echo esc_html( $current_user->user_email ); ?></p>
				<p class="mt-6 text-xs uppercase tracking-[0.2em] text-gray-500">Identite</p>
				<p class="mt-2 text-sm text-gray-300">Compte WordPress securise par Identity Security Kit.</p>
			</aside>

			<form class="rounded-2xl border border-white/10 bg-white/[0.04] p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="POST" enctype="multipart/form-data">
				<?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?>

				<div class="grid gap-5 sm:grid-cols-2">
					<div class="sm:col-span-2">
						<label class="mb-2 block text-sm font-semibold text-gray-200" for="email"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></label>
						<input class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-white outline-none transition focus:border-amber-300" id="email" name="email" type="email" required value="<?php echo esc_attr( $current_user->user_email ); ?>">
					</div>

					<div class="sm:col-span-2">
						<label class="mb-2 block text-sm font-semibold text-gray-200" for="bio"><?php esc_html_e( 'Bio', 'photovault' ); ?></label>
						<textarea class="min-h-32 w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-white outline-none transition focus:border-amber-300" id="bio" name="bio"><?php echo esc_textarea( $current_user->description ); ?></textarea>
					</div>

					<div class="sm:col-span-2">
						<label class="mb-2 block text-sm font-semibold text-gray-200" for="profile_avatar"><?php esc_html_e( 'Avatar', 'photovault' ); ?></label>
						<input class="block w-full cursor-pointer rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-sm text-gray-300 file:mr-4 file:rounded-full file:border-0 file:bg-amber-300 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-black" id="profile_avatar" name="profile_avatar" type="file" accept="image/jpeg,image/png,image/webp">
						<p class="mt-2 text-xs text-gray-500"><?php esc_html_e( 'JPG, PNG ou WebP. Validation serveur obligatoire avant enregistrement.', 'photovault' ); ?></p>
					</div>
				</div>

				<details class="mt-8 rounded-2xl border border-white/10 bg-black/20 p-5">
					<summary class="cursor-pointer text-sm font-bold text-amber-200"><?php esc_html_e( 'Modifier le mot de passe', 'photovault' ); ?></summary>
					<div class="mt-5 grid gap-5 sm:grid-cols-3">
						<div>
							<label class="mb-2 block text-sm font-semibold text-gray-200" for="current_password"><?php esc_html_e( 'Mot de passe actuel', 'photovault' ); ?></label>
							<input class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-white outline-none transition focus:border-amber-300" id="current_password" name="current_password" type="password" autocomplete="current-password">
						</div>
						<div>
							<label class="mb-2 block text-sm font-semibold text-gray-200" for="password"><?php esc_html_e( 'Nouveau mot de passe', 'photovault' ); ?></label>
							<input class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-white outline-none transition focus:border-amber-300" id="password" name="password" type="password" autocomplete="new-password">
						</div>
						<div>
							<label class="mb-2 block text-sm font-semibold text-gray-200" for="password_confirm"><?php esc_html_e( 'Confirmation', 'photovault' ); ?></label>
							<input class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3 text-white outline-none transition focus:border-amber-300" id="password_confirm" name="password_confirm" type="password" autocomplete="new-password">
						</div>
					</div>
				</details>

				<div class="mt-8 flex justify-end">
					<button class="rounded-full bg-amber-300 px-6 py-3 text-sm font-black text-black transition hover:bg-amber-200" type="submit">
						<?php esc_html_e( 'Enregistrer les modifications', 'photovault' ); ?>
					</button>
				</div>
			</form>
		</section>
	</div>
</main>

<?php get_footer(); ?>