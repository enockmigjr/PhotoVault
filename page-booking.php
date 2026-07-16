<?php
/**
 * Template Name: PhotoVault Shooting Booking
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( add_query_arg( 'redirect_to', home_url( '/booking/' ), home_url( '/login/' ) ) );
	exit;
}

$user     = wp_get_current_user();
$types    = function_exists( 'photovault_get_shooting_types' ) ? photovault_get_shooting_types() : array();
$selected = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
$selected = array_key_exists( $selected, $types ) ? $selected : '';
$status   = isset( $_GET['booking'] ) ? sanitize_key( wp_unslash( $_GET['booking'] ) ) : '';
$messages = array(
	'shooting_invalid_date'          => __( 'Choisissez une date valide dans les deux prochaines années.', 'photovault' ),
	'shooting_invalid_type'          => __( 'Choisissez un type de shooting.', 'photovault' ),
	'shooting_invalid_fields'        => __( 'Complétez les champs obligatoires avec suffisamment de détails.', 'photovault' ),
	'shooting_duplicate'             => __( 'Une demande active existe déjà pour ce type et cette date.', 'photovault' ),
	'shooting_contact_mismatch'      => __( 'Utilisez l’adresse e-mail vérifiée de votre compte.', 'photovault' ),
	'shooting_identity_unverified'   => __( 'Vérifiez votre adresse e-mail dans votre profil avant de réserver.', 'photovault' ),
	'rate_limited'                   => __( 'Trop de demandes ont été envoyées. Réessayez dans une heure.', 'photovault' ),
);
$phone = function_exists( 'identity_security_kit_phone_meta_key' ) ? (string) get_user_meta( $user->ID, identity_security_kit_phone_meta_key(), true ) : '';

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-white">
	<header class="border-b border-white/10 px-5 pb-16 pt-20 sm:px-8 lg:pb-20 lg:pt-28">
		<div class="mx-auto max-w-5xl"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Création sur mesure', 'photovault' ); ?></p><h1 class="mt-5 max-w-3xl font-serif text-4xl leading-tight sm:text-5xl"><?php esc_html_e( 'Commençons par l’intention, la lumière et le temps.', 'photovault' ); ?></h1><p class="mt-6 max-w-2xl text-base leading-8 text-gray-400"><?php esc_html_e( 'Cette demande ouvre la conversation. La date devient définitive après confirmation du studio.', 'photovault' ); ?></p></div>
	</header>

	<section class="px-5 py-16 sm:px-8 lg:py-20">
		<div class="mx-auto grid max-w-6xl gap-14 lg:grid-cols-[0.8fr_1.4fr]">
			<aside><p class="text-xs font-bold uppercase text-gray-500"><?php esc_html_e( 'Le parcours', 'photovault' ); ?></p><ol class="mt-6 space-y-6 border-l border-white/15 pl-6 text-sm leading-6 text-gray-400"><li><strong class="block text-white">01 / <?php esc_html_e( 'Demande', 'photovault' ); ?></strong><?php esc_html_e( 'Vous partagez le contexte, le lieu et la date envisagée.', 'photovault' ); ?></li><li><strong class="block text-white">02 / <?php esc_html_e( 'Échange', 'photovault' ); ?></strong><?php esc_html_e( 'Le studio précise la direction artistique et la disponibilité.', 'photovault' ); ?></li><li><strong class="block text-white">03 / <?php esc_html_e( 'Confirmation', 'photovault' ); ?></strong><?php esc_html_e( 'La réservation confirmée apparaît dans votre espace.', 'photovault' ); ?></li></ol></aside>

			<div>
				<?php if ( isset( $messages[ $status ] ) ) : ?><div id="booking-error" class="mb-7 flex items-start justify-between gap-4 border border-red-400/30 bg-red-400/10 px-5 py-4 text-sm text-red-100" role="alert" data-pv-toast><span><?php echo esc_html( $messages[ $status ] ); ?></span><button type="button" class="pv-header-icon h-8 w-8 shrink-0" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close>&times;</button></div><?php endif; ?>

				<?php if ( ! $types ) : ?>
					<p class="border-y border-white/10 py-8 text-gray-400"><?php esc_html_e( 'Le module de réservation est temporairement indisponible.', 'photovault' ); ?></p>
				<?php else : ?>
					<form class="pv-public-form grid gap-6 sm:grid-cols-2" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" <?php echo isset( $messages[ $status ] ) ? 'aria-describedby="booking-error"' : ''; ?>>
						<input type="hidden" name="action" value="photovault_create_shooting">
						<?php wp_nonce_field( 'photovault_create_shooting', 'photovault_shooting_nonce' ); ?>
						<label class="sm:col-span-2"><span><?php esc_html_e( 'Type de shooting', 'photovault' ); ?></span><select name="shooting_type" required><option value=""><?php esc_html_e( 'Choisir', 'photovault' ); ?></option><?php foreach ( $types as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
						<label><span><?php esc_html_e( 'Date souhaitée', 'photovault' ); ?></span><input name="shooting_date" type="date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" max="<?php echo esc_attr( wp_date( 'Y-m-d', strtotime( '+2 years' ) ) ); ?>" required></label>
						<label><span><?php esc_html_e( 'Lieu envisagé', 'photovault' ); ?></span><input name="shooting_location" maxlength="160" autocomplete="street-address" required></label>
						<label><span><?php esc_html_e( 'Nom de contact', 'photovault' ); ?></span><input name="shooting_contact_name" maxlength="120" autocomplete="name" value="<?php echo esc_attr( $user->display_name ); ?>" required></label>
						<label><span><?php esc_html_e( 'E-mail vérifié du compte', 'photovault' ); ?></span><input name="shooting_contact_email" type="email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" readonly required></label>
						<label class="sm:col-span-2"><span><?php esc_html_e( 'Téléphone', 'photovault' ); ?> <small class="font-normal text-gray-500"><?php esc_html_e( '(facultatif, format international)', 'photovault' ); ?></small></span><input name="shooting_contact_phone" type="tel" inputmode="tel" maxlength="16" pattern="\+[1-9][0-9]{7,14}" autocomplete="tel" value="<?php echo esc_attr( $phone ); ?>"></label>
						<label class="sm:col-span-2"><span><?php esc_html_e( 'Intention et contexte', 'photovault' ); ?></span><textarea class="min-h-40" name="shooting_message" minlength="10" maxlength="2000" placeholder="<?php esc_attr_e( 'Personnes, ambiance, usage des images, contraintes ou références utiles.', 'photovault' ); ?>" required></textarea></label>
						<div class="flex flex-col gap-4 border-t border-white/10 pt-6 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between"><p class="max-w-lg text-xs leading-5 text-gray-500"><?php esc_html_e( 'Ces informations sont utilisées uniquement pour préparer et suivre votre demande de shooting.', 'photovault' ); ?></p><button class="pv-public-submit shrink-0" type="submit"><?php esc_html_e( 'Envoyer la demande', 'photovault' ); ?></button></div>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
