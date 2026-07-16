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

$current_user   = wp_get_current_user();
$user_id        = (int) $current_user->ID;
$avatar_id      = get_user_meta( $user_id, 'photovault_avatar_id', true );
$avatar_url     = $avatar_id ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : get_avatar_url( $user_id, array( 'size' => 160 ) );
$status         = isset( $_GET['profile'] ) ? sanitize_key( wp_unslash( $_GET['profile'] ) ) : '';
$verify         = isset( $_GET['verify'] ) ? sanitize_key( wp_unslash( $_GET['verify'] ) ) : '';
$email_change   = isset( $_GET['email_change'] ) ? sanitize_key( wp_unslash( $_GET['email_change'] ) ) : '';
$email_verified = function_exists( 'identity_security_kit_is_email_verified' ) ? identity_security_kit_is_email_verified( $user_id ) : true;
$phone          = function_exists( 'identity_security_kit_phone_meta_key' ) ? (string) get_user_meta( $user_id, identity_security_kit_phone_meta_key(), true ) : '';
$phone_verified = function_exists( 'identity_security_kit_is_phone_verified' ) ? identity_security_kit_is_phone_verified( $user_id ) : false;
$pending_email  = function_exists( 'identity_security_kit_get_pending_email_change' ) ? identity_security_kit_get_pending_email_change( $user_id ) : null;
$mfa_methods    = function_exists( 'identity_security_kit_get_user_mfa_methods' ) ? identity_security_kit_get_user_mfa_methods( $user_id ) : array();
$gallery_density = function_exists( 'photovault_get_user_preference' ) ? photovault_get_user_preference( $user_id, 'gallery_density' ) : 'editorial';
$reduce_motion   = function_exists( 'photovault_get_user_preference' ) ? photovault_get_user_preference( $user_id, 'reduce_motion' ) : '0';
$dashboard_landing = function_exists( 'photovault_get_user_preference' ) ? photovault_get_user_preference( $user_id, 'dashboard_landing' ) : 'overview';
$dashboard_landing_labels = array(
	'overview'  => __( 'Apercu', 'photovault' ),
	'favorites' => __( 'Favoris', 'photovault' ),
	'access'    => __( 'Collections', 'photovault' ),
	'bookings'  => __( 'Reservations', 'photovault' ),
);
$dashboard_landing_label = isset( $dashboard_landing_labels[ $dashboard_landing ] ) ? $dashboard_landing_labels[ $dashboard_landing ] : $dashboard_landing_labels['overview'];

$messages = array(
	'success'                       => array( 'type' => 'success', 'text' => __( 'Profil mis a jour.', 'photovault' ) ),
	'identity_updated'              => array( 'type' => 'success', 'text' => __( 'Vos informations publiques ont ete mises a jour.', 'photovault' ) ),
	'avatar_updated'                => array( 'type' => 'success', 'text' => __( 'Votre photo de profil a ete mise a jour.', 'photovault' ) ),
	'phone_updated'                 => array( 'type' => 'success', 'text' => __( 'Votre numero a ete enregistre. Une nouvelle verification est requise.', 'photovault' ) ),
	'password_updated'              => array( 'type' => 'success', 'text' => __( 'Votre mot de passe a ete modifie et les autres sessions ont ete fermees.', 'photovault' ) ),
	'preferences_updated'           => array( 'type' => 'success', 'text' => __( 'Vos preferences d experience ont ete enregistrees.', 'photovault' ) ),
	'display_name_required'         => array( 'type' => 'error', 'text' => __( 'Le nom affiche est obligatoire.', 'photovault' ) ),
	'avatar_required'               => array( 'type' => 'error', 'text' => __( 'Choisissez une image avant de continuer.', 'photovault' ) ),
	'invalid_email'                 => array( 'type' => 'error', 'text' => __( 'Adresse e-mail invalide.', 'photovault' ) ),
	'email_exists'                  => array( 'type' => 'error', 'text' => __( 'Cette adresse e-mail est deja utilisee.', 'photovault' ) ),
	'email_change_invalid'          => array( 'type' => 'error', 'text' => __( 'La nouvelle adresse e-mail est invalide.', 'photovault' ) ),
	'email_change_unchanged'        => array( 'type' => 'error', 'text' => __( 'La nouvelle adresse est identique a l adresse actuelle.', 'photovault' ) ),
	'email_change_delivery_failed'  => array( 'type' => 'error', 'text' => __( 'Le message de confirmation n a pas pu etre envoye.', 'photovault' ) ),
	'email_change_rate_limited'     => array( 'type' => 'error', 'text' => __( 'Veuillez patienter avant une nouvelle demande.', 'photovault' ) ),
	'current_password_invalid'      => array( 'type' => 'error', 'text' => __( 'Le mot de passe actuel est incorrect.', 'photovault' ) ),
	'phone_required'                => array( 'type' => 'error', 'text' => __( 'Le numero de telephone international est obligatoire.', 'photovault' ) ),
	'phone_country_code_required'   => array( 'type' => 'error', 'text' => __( 'Ajoutez le prefixe pays au numero de telephone.', 'photovault' ) ),
	'phone_invalid'                 => array( 'type' => 'error', 'text' => __( 'Numero de telephone international invalide.', 'photovault' ) ),
	'phone_validation_unavailable'  => array( 'type' => 'error', 'text' => __( 'La validation des numeros est temporairement indisponible.', 'photovault' ) ),
	'phone_exists'                  => array( 'type' => 'error', 'text' => __( 'Ce numero est deja associe a un autre compte.', 'photovault' ) ),
	'phone_save_failed'             => array( 'type' => 'error', 'text' => __( 'Le numero n a pas pu etre enregistre.', 'photovault' ) ),
	'weak_password'                 => array( 'type' => 'error', 'text' => __( 'Le nouveau mot de passe ne respecte pas la longueur minimale.', 'photovault' ) ),
	'pwd_mismatch'                  => array( 'type' => 'error', 'text' => __( 'Les nouveaux mots de passe ne correspondent pas.', 'photovault' ) ),
	'file_too_large'                => array( 'type' => 'error', 'text' => __( 'L avatar est trop volumineux.', 'photovault' ) ),
	'invalid_upload'                => array( 'type' => 'error', 'text' => __( 'Le fichier envoye est invalide.', 'photovault' ) ),
	'invalid_file_type'             => array( 'type' => 'error', 'text' => __( 'Type d image non autorise.', 'photovault' ) ),
	'invalid_image'                 => array( 'type' => 'error', 'text' => __( 'Le fichier envoye n est pas une image valide.', 'photovault' ) ),
	'image_too_large'               => array( 'type' => 'error', 'text' => __( 'Les dimensions de l image sont trop grandes.', 'photovault' ) ),
	'avatar_upload_failed'          => array( 'type' => 'error', 'text' => __( 'L avatar n a pas pu etre enregistre.', 'photovault' ) ),
	'failed'                        => array( 'type' => 'error', 'text' => __( 'La modification a echoue.', 'photovault' ) ),
);
$verify_messages = array(
	'pending'          => array( 'type' => 'info', 'text' => __( 'Un lien de verification vient d etre envoye.', 'photovault' ) ),
	'success'          => array( 'type' => 'success', 'text' => __( 'Adresse e-mail verifiee.', 'photovault' ) ),
	'invalid'          => array( 'type' => 'error', 'text' => __( 'Lien de verification invalide.', 'photovault' ) ),
	'expired'          => array( 'type' => 'error', 'text' => __( 'Le lien de verification a expire.', 'photovault' ) ),
	'deferred'         => array( 'type' => 'error', 'text' => __( 'Le message de verification n a pas pu etre envoye.', 'photovault' ) ),
	'resent'           => array( 'type' => 'success', 'text' => __( 'Un nouveau lien de verification vient d etre envoye.', 'photovault' ) ),
	'rate_limited'     => array( 'type' => 'info', 'text' => __( 'Veuillez patienter avant de demander un nouveau lien.', 'photovault' ) ),
	'already_verified' => array( 'type' => 'success', 'text' => __( 'Votre adresse e-mail est deja verifiee.', 'photovault' ) ),
);
$email_change_messages = array(
	'pending'              => array( 'type' => 'info', 'text' => __( 'Confirmez la nouvelle adresse depuis le message recu.', 'photovault' ) ),
	'confirmed'            => array( 'type' => 'success', 'text' => __( 'Votre nouvelle adresse e-mail est confirmee.', 'photovault' ) ),
	'cancelled'            => array( 'type' => 'success', 'text' => __( 'La demande de changement a ete annulee.', 'photovault' ) ),
	'email_change_expired' => array( 'type' => 'error', 'text' => __( 'La demande de changement a expire.', 'photovault' ) ),
	'email_change_invalid' => array( 'type' => 'error', 'text' => __( 'Le lien de changement est invalide ou deja utilise.', 'photovault' ) ),
);

$notice = null;
if ( $status && isset( $messages[ $status ] ) ) {
	$notice = $messages[ $status ];
} elseif ( $verify && isset( $verify_messages[ $verify ] ) ) {
	$notice = $verify_messages[ $verify ];
} elseif ( $email_change && isset( $email_change_messages[ $email_change ] ) ) {
	$notice = $email_change_messages[ $email_change ];
}

get_header();
?>

<div class="min-h-screen bg-[#0d0c0b] text-white lg:flex">
	<?php get_template_part( 'templates/dashboard-sidebar', null, array( 'section' => 'profile' ) ); ?>
	<main class="min-w-0 flex-1 px-4 py-8 sm:px-8 lg:px-10 lg:py-10">
		<div class="mx-auto max-w-5xl">
			<header class="mb-10 flex flex-col gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
				<div>
					<p class="text-xs font-bold uppercase tracking-[0.24em] text-amber-300"><?php esc_html_e( 'Compte', 'photovault' ); ?></p>
					<h1 class="mt-3 text-3xl font-black sm:text-4xl"><?php esc_html_e( 'Informations et securite', 'photovault' ); ?></h1>
				</div>
				<a class="inline-flex items-center justify-center rounded-md border border-white/15 px-5 py-3 text-sm font-bold text-gray-200 transition hover:border-amber-300 hover:text-amber-200" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>"><?php esc_html_e( 'Retour au Dashboard', 'photovault' ); ?></a>
			</header>

			<?php if ( $notice ) : ?>
				<div class="fixed right-4 top-24 z-[100] flex w-[min(420px,calc(100vw-2rem))] items-start gap-4 border px-5 py-4 shadow-2xl <?php echo 'success' === $notice['type'] ? 'border-emerald-400/40 bg-[#10221b] text-emerald-100' : ( 'info' === $notice['type'] ? 'border-amber-300/40 bg-[#28200f] text-amber-100' : 'border-red-400/40 bg-[#2a1212] text-red-100' ); ?>" role="<?php echo 'error' === $notice['type'] ? 'alert' : 'status'; ?>" data-pv-toast>
					<p class="min-w-0 flex-1 text-sm leading-6"><?php echo esc_html( $notice['text'] ); ?></p>
					<button class="inline-flex h-8 w-8 shrink-0 items-center justify-center border border-current/25" type="button" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg></button>
				</div>
			<?php endif; ?>

			<?php if ( ! $email_verified ) : ?>
				<section class="mb-10 flex flex-col gap-4 border-l-4 border-amber-300 bg-amber-300/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
					<div><h2 class="font-bold text-amber-100"><?php esc_html_e( 'Adresse e-mail a verifier', 'photovault' ); ?></h2><p class="mt-1 text-sm text-amber-100/75"><?php echo esc_html( $current_user->user_email ); ?></p></div>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="identity_security_kit_resend_email_verification"><?php wp_nonce_field( 'identity_security_kit_resend_email_verification' ); ?><button class="text-sm font-bold text-amber-200 underline underline-offset-4" type="submit"><?php esc_html_e( 'Renvoyer le lien', 'photovault' ); ?></button></form>
				</section>
			<?php endif; ?>

			<section class="border-b border-white/10 pb-10" aria-labelledby="profile-identity-title">
				<div class="flex flex-col gap-7 sm:flex-row sm:items-center">
					<div class="relative shrink-0"><img class="h-28 w-28 rounded-full object-cover ring-1 ring-white/20" src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>"><button class="absolute -bottom-1 -right-1 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-[#22201d] text-amber-200 shadow-xl" type="button" data-profile-open="profile-avatar-dialog" aria-label="<?php esc_attr_e( 'Modifier la photo', 'photovault' ); ?>"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h3l2-2h6l2 2h3v12H4V7z"/><circle cx="12" cy="13" r="3"/></svg></button></div>
					<div class="min-w-0 flex-1"><h2 id="profile-identity-title" class="text-2xl font-bold"><?php echo esc_html( $current_user->display_name ?: $current_user->user_login ); ?></h2><p class="mt-2 break-all text-sm text-gray-400"><?php echo esc_html( $current_user->user_email ); ?></p><p class="mt-3 max-w-2xl text-sm leading-6 text-gray-300"><?php echo esc_html( $current_user->description ?: __( 'Aucune biographie renseignee.', 'photovault' ) ); ?></p></div>
					<button class="inline-flex shrink-0 items-center gap-2 text-sm font-bold text-amber-200" type="button" data-profile-open="profile-identity-dialog"><?php esc_html_e( 'Modifier', 'photovault' ); ?><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L8 18l-4 1 1-4L16.5 3.5z"/></svg></button>
				</div>
			</section>

			<section class="py-10" aria-labelledby="profile-contact-title">
				<div class="mb-4"><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Connexion', 'photovault' ); ?></p><h2 id="profile-contact-title" class="mt-2 text-2xl font-bold"><?php esc_html_e( 'Coordonnees et acces', 'photovault' ); ?></h2></div>
				<div class="divide-y divide-white/10 border-y border-white/10">
					<div class="grid gap-4 py-6 sm:grid-cols-[180px,1fr,auto] sm:items-center"><span class="text-sm font-semibold text-gray-400"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></span><div><strong class="break-all text-sm"><?php echo esc_html( $current_user->user_email ); ?></strong><span class="ml-2 text-xs font-bold <?php echo $email_verified ? 'text-emerald-300' : 'text-amber-200'; ?>"><?php echo esc_html( $email_verified ? __( 'Verifiee', 'photovault' ) : __( 'A verifier', 'photovault' ) ); ?></span><?php if ( $pending_email ) : ?><p class="mt-2 text-xs text-amber-200"><?php echo esc_html( sprintf( __( 'Changement en attente vers %s', 'photovault' ), $pending_email['email'] ) ); ?></p><?php endif; ?></div><button class="text-left text-sm font-bold text-amber-200" type="button" data-profile-open="profile-email-dialog"><?php esc_html_e( 'Modifier', 'photovault' ); ?></button></div>
					<div class="grid gap-4 py-6 sm:grid-cols-[180px,1fr,auto] sm:items-center"><span class="text-sm font-semibold text-gray-400"><?php esc_html_e( 'Telephone', 'photovault' ); ?></span><div><strong class="text-sm"><?php echo esc_html( $phone ?: __( 'Non renseigne', 'photovault' ) ); ?></strong><?php if ( $phone ) : ?><span class="ml-2 text-xs font-bold <?php echo $phone_verified ? 'text-emerald-300' : 'text-amber-200'; ?>"><?php echo esc_html( $phone_verified ? __( 'Verifie', 'photovault' ) : __( 'A verifier', 'photovault' ) ); ?></span><?php endif; ?></div><button class="text-left text-sm font-bold text-amber-200" type="button" data-profile-open="profile-phone-dialog"><?php echo esc_html( $phone ? __( 'Modifier', 'photovault' ) : __( 'Ajouter', 'photovault' ) ); ?></button></div>
					<div class="grid gap-4 py-6 sm:grid-cols-[180px,1fr,auto] sm:items-center"><span class="text-sm font-semibold text-gray-400"><?php esc_html_e( 'Mot de passe', 'photovault' ); ?></span><span class="text-sm text-gray-300"><?php esc_html_e( 'Derniere protection du compte', 'photovault' ); ?></span><button class="text-left text-sm font-bold text-amber-200" type="button" data-profile-open="profile-password-dialog"><?php esc_html_e( 'Modifier', 'photovault' ); ?></button></div>
				</div>
			</section>

			<section class="border-t border-white/10 py-10" aria-labelledby="profile-preferences-title">
				<div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Experience', 'photovault' ); ?></p><h2 id="profile-preferences-title" class="mt-2 text-2xl font-bold"><?php esc_html_e( 'Preferences personnelles', 'photovault' ); ?></h2><p class="mt-3 text-sm text-gray-400"><?php echo esc_html( sprintf( __( 'Galerie %1$s - animations %2$s - ouverture sur %3$s', 'photovault' ), 'compact' === $gallery_density ? __( 'compacte', 'photovault' ) : __( 'editoriale', 'photovault' ), '1' === $reduce_motion ? __( 'reduites', 'photovault' ) : __( 'actives', 'photovault' ), $dashboard_landing_label ) ); ?></p></div><button class="text-left text-sm font-bold text-amber-200" type="button" data-profile-open="profile-preferences-dialog"><?php esc_html_e( 'Personnaliser', 'photovault' ); ?></button></div>
			</section>

			<?php if ( shortcode_exists( 'identity_security_mfa' ) ) : ?>
				<section class="pv-profile-mfa border-t border-white/10 py-10">
					<div class="mb-6 flex items-end justify-between gap-5"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Securite', 'photovault' ); ?></p><h2 class="mt-2 text-2xl font-bold"><?php esc_html_e( 'Double authentification', 'photovault' ); ?></h2></div><span class="text-xs font-bold <?php echo $mfa_methods ? 'text-emerald-300' : 'text-amber-200'; ?>"><?php echo esc_html( $mfa_methods ? sprintf( __( '%d methode(s) active(s)', 'photovault' ), count( $mfa_methods ) ) : __( 'Aucune methode active', 'photovault' ) ); ?></span></div>
					<?php echo do_shortcode( '[identity_security_mfa]' ); ?>
				</section>
			<?php endif; ?>
		</div>
	</main>
</div>

<dialog id="profile-avatar-dialog" aria-labelledby="profile-avatar-dialog-title" class="m-auto w-[min(560px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="post" enctype="multipart/form-data"><input type="hidden" name="profile_action" value="avatar"><?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-avatar-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Photo de profil', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div><input class="mt-7 block w-full border border-white/15 bg-black/30 px-4 py-3 text-sm file:mr-4 file:border-0 file:bg-amber-300 file:px-4 file:py-2 file:font-bold file:text-black" name="profile_avatar" type="file" accept="image/jpeg,image/png,image/webp" required><div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Enregistrer', 'photovault' ); ?></button></div></form>
</dialog>

<dialog id="profile-identity-dialog" aria-labelledby="profile-identity-dialog-title" class="m-auto w-[min(620px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="post"><input type="hidden" name="profile_action" value="identity"><?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-identity-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Informations publiques', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div><label class="mt-7 block text-sm font-semibold" for="profile-display-name"><?php esc_html_e( 'Nom affiche', 'photovault' ); ?></label><input id="profile-display-name" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="display_name" maxlength="250" required value="<?php echo esc_attr( $current_user->display_name ); ?>"><label class="mt-5 block text-sm font-semibold" for="profile-bio"><?php esc_html_e( 'Biographie', 'photovault' ); ?></label><textarea id="profile-bio" class="mt-2 min-h-32 w-full border border-white/15 bg-black/30 px-4 py-3" name="bio" maxlength="1000"><?php echo esc_textarea( $current_user->description ); ?></textarea><div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Enregistrer', 'photovault' ); ?></button></div></form>
</dialog>

<dialog id="profile-phone-dialog" aria-labelledby="profile-phone-dialog-title" class="m-auto w-[min(560px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="post"><input type="hidden" name="profile_action" value="phone"><?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-phone-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Numero de telephone', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div><label class="mt-7 block text-sm font-semibold" for="profile-phone"><?php esc_html_e( 'Numero avec prefixe pays', 'photovault' ); ?></label><input id="profile-phone" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="phone" type="tel" inputmode="tel" autocomplete="tel" required value="<?php echo esc_attr( $phone ); ?>" placeholder="+229 01 23 45 67 89"><div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Enregistrer', 'photovault' ); ?></button></div></form>
</dialog>

<dialog id="profile-email-dialog" aria-labelledby="profile-email-dialog-title" class="m-auto w-[min(580px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="post"><input type="hidden" name="profile_action" value="email"><?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-email-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div><label class="mt-7 block text-sm font-semibold" for="profile-new-email"><?php esc_html_e( 'Nouvelle adresse', 'photovault' ); ?></label><input id="profile-new-email" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="new_email" type="email" autocomplete="email" required><label class="mt-5 block text-sm font-semibold" for="profile-email-password"><?php esc_html_e( 'Mot de passe actuel', 'photovault' ); ?></label><input id="profile-email-password" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="email_current_password" type="password" autocomplete="current-password" required><div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Envoyer la confirmation', 'photovault' ); ?></button></div></form>
	<?php if ( $pending_email ) : ?><form class="border-t border-white/10 px-6 py-5 sm:px-8" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="identity_security_kit_cancel_email_change"><?php wp_nonce_field( 'identity_security_kit_cancel_email_change' ); ?><button class="text-sm font-bold text-red-300 underline underline-offset-4" type="submit"><?php esc_html_e( 'Annuler le changement en attente', 'photovault' ); ?></button></form><?php endif; ?>
</dialog>

<dialog id="profile-preferences-dialog" aria-labelledby="profile-preferences-dialog-title" class="m-auto w-[min(620px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="photovault_save_preferences"><?php wp_nonce_field( 'photovault_save_preferences' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-preferences-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Preferences personnelles', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div>
		<label class="mt-7 block text-sm font-semibold" for="profile-gallery-density"><?php esc_html_e( 'Densite de la galerie', 'photovault' ); ?></label><select id="profile-gallery-density" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="gallery_density"><option value="editorial" <?php selected( $gallery_density, 'editorial' ); ?>><?php esc_html_e( 'Editoriale et aeree', 'photovault' ); ?></option><option value="compact" <?php selected( $gallery_density, 'compact' ); ?>><?php esc_html_e( 'Compacte, plus d oeuvres', 'photovault' ); ?></option></select>
		<label class="mt-6 flex items-start gap-3 text-sm text-gray-300"><input class="mt-1" type="checkbox" name="reduce_motion" value="1" <?php checked( $reduce_motion, '1' ); ?>><span><strong class="block text-white"><?php esc_html_e( 'Reduire les animations', 'photovault' ); ?></strong><small class="mt-1 block text-gray-500"><?php esc_html_e( 'Limite les transitions et mouvements decoratifs sur votre session.', 'photovault' ); ?></small></span></label>
		<label class="mt-6 block text-sm font-semibold" for="profile-dashboard-landing"><?php esc_html_e( 'Ouverture du Dashboard', 'photovault' ); ?></label><select id="profile-dashboard-landing" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="dashboard_landing"><option value="overview" <?php selected( $dashboard_landing, 'overview' ); ?>><?php esc_html_e( 'Apercu', 'photovault' ); ?></option><option value="favorites" <?php selected( $dashboard_landing, 'favorites' ); ?>><?php esc_html_e( 'Favoris', 'photovault' ); ?></option><option value="access" <?php selected( $dashboard_landing, 'access' ); ?>><?php esc_html_e( 'Collections', 'photovault' ); ?></option><option value="bookings" <?php selected( $dashboard_landing, 'bookings' ); ?>><?php esc_html_e( 'Reservations', 'photovault' ); ?></option></select>
		<div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Enregistrer', 'photovault' ); ?></button></div></form>
</dialog>

<dialog id="profile-password-dialog" aria-labelledby="profile-password-dialog-title" class="m-auto w-[min(620px,calc(100vw-2rem))] border border-white/15 bg-[#171614] p-0 text-white shadow-2xl backdrop:bg-black/75">
	<form class="p-6 sm:p-8" action="<?php echo esc_url( home_url( '/profile/' ) ); ?>" method="post"><input type="hidden" name="profile_action" value="password"><?php wp_nonce_field( 'photovault_profile_action', 'photovault_profile_nonce' ); ?><div class="flex items-center justify-between gap-5"><h2 id="profile-password-dialog-title" class="text-xl font-bold"><?php esc_html_e( 'Modifier le mot de passe', 'photovault' ); ?></h2><button class="inline-flex h-9 w-9 items-center justify-center border border-white/15" type="button" data-profile-close aria-label="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>">&times;</button></div><label class="mt-7 block text-sm font-semibold" for="profile-current-password"><?php esc_html_e( 'Mot de passe actuel', 'photovault' ); ?></label><input id="profile-current-password" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="current_password" type="password" autocomplete="current-password" required><label class="mt-5 block text-sm font-semibold" for="profile-new-password"><?php esc_html_e( 'Nouveau mot de passe', 'photovault' ); ?></label><input id="profile-new-password" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="password" type="password" autocomplete="new-password" required><label class="mt-5 block text-sm font-semibold" for="profile-confirm-password"><?php esc_html_e( 'Confirmation', 'photovault' ); ?></label><input id="profile-confirm-password" class="mt-2 w-full border border-white/15 bg-black/30 px-4 py-3" name="password_confirm" type="password" autocomplete="new-password" required><div class="mt-8 flex justify-end gap-3"><button class="border border-white/15 px-5 py-2.5 text-sm font-bold" type="button" data-profile-close><?php esc_html_e( 'Annuler', 'photovault' ); ?></button><button class="bg-amber-300 px-5 py-2.5 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Modifier le mot de passe', 'photovault' ); ?></button></div></form>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
	document.querySelectorAll('[data-profile-open]').forEach(function(button) {
		button.addEventListener('click', function() {
			const dialog = document.getElementById(button.getAttribute('data-profile-open'));
			if (dialog && typeof dialog.showModal === 'function') {
				dialog.pvReturnFocus = button;
				dialog.showModal();
			}
		});
	});
	document.querySelectorAll('dialog').forEach(function(dialog) {
		const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
		dialog.querySelectorAll('[data-profile-close]').forEach(function(button) {
			button.addEventListener('click', function() { dialog.close(); });
		});
		dialog.addEventListener('keydown', function(event) {
			if (event.key !== 'Tab') return;
			const controls = Array.from(dialog.querySelectorAll(focusableSelector)).filter(function(control) {
				return control.getClientRects().length > 0;
			});
			if (!controls.length) {
				event.preventDefault();
				dialog.focus();
				return;
			}
			const first = controls[0];
			const last = controls[controls.length - 1];
			if (event.shiftKey && (document.activeElement === first || !dialog.contains(document.activeElement))) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		});
		dialog.addEventListener('close', function() {
			if (dialog.pvReturnFocus && dialog.pvReturnFocus.isConnected) dialog.pvReturnFocus.focus();
			dialog.pvReturnFocus = null;
		});
		dialog.addEventListener('click', function(event) {
			const rect = dialog.getBoundingClientRect();
			if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
		});
	});
	const toast = document.querySelector('[data-pv-toast]');
	if (toast) {
		const dismiss = function() {
			toast.remove();
			const url = new URL(window.location.href);
			['profile', 'verify', 'email_change'].forEach(function(key) { url.searchParams.delete(key); });
			window.history.replaceState({}, '', url.pathname + url.search + url.hash);
		};
		const close = toast.querySelector('[data-pv-toast-close]');
		if (close) close.addEventListener('click', dismiss);
		window.setTimeout(dismiss, 7000);
	}
});
</script>

<?php get_footer(); ?>
