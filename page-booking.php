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
	'shooting_invalid_date'   => __( 'Choisissez une date valide dans les deux prochaines annees.', 'photovault' ),
	'shooting_invalid_type'   => __( 'Choisissez un type de shooting.', 'photovault' ),
	'shooting_invalid_fields' => __( 'Completez tous les champs obligatoires avec suffisamment de details.', 'photovault' ),
	'shooting_duplicate'      => __( 'Une demande active existe deja pour ce type et cette date.', 'photovault' ),
	'shooting_contact_mismatch' => __( 'Utilisez l adresse e-mail verifiee de votre compte.', 'photovault' ),
	'shooting_identity_unverified' => __( 'Verifiez votre adresse e-mail dans votre profil avant de reserver.', 'photovault' ),
	'rate_limited'            => __( 'Trop de demandes ont ete envoyees. Reessayez dans une heure.', 'photovault' ),
);
$phone = function_exists( 'identity_security_kit_phone_meta_key' ) ? (string) get_user_meta( $user->ID, identity_security_kit_phone_meta_key(), true ) : '';

get_header();
?>

<main class="min-h-screen bg-[#0d0c0b] text-white">
	<section class="border-b border-white/10 px-4 pb-16 pt-24 sm:px-8 lg:pb-20 lg:pt-32">
		<div class="mx-auto max-w-6xl"><p class="text-xs font-black uppercase tracking-[0.24em] text-amber-300"><?php esc_html_e( 'Creation sur mesure', 'photovault' ); ?></p><h1 class="mt-5 max-w-4xl font-serif text-4xl leading-tight sm:text-6xl"><?php esc_html_e( 'Commencons par l intention, la lumiere et le temps.', 'photovault' ); ?></h1><p class="mt-6 max-w-2xl text-base leading-8 text-gray-400"><?php esc_html_e( 'Cette demande ouvre la conversation. La date devient definitive apres confirmation du studio.', 'photovault' ); ?></p></div>
	</section>
	<section class="px-4 py-16 sm:px-8 lg:py-20"><div class="mx-auto grid max-w-6xl gap-14 lg:grid-cols-[0.8fr,1.4fr]">
		<aside><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Le parcours', 'photovault' ); ?></p><ol class="mt-6 space-y-6 border-l border-white/15 pl-6 text-sm leading-6 text-gray-400"><li><strong class="block text-white">01 - <?php esc_html_e( 'Demande', 'photovault' ); ?></strong><?php esc_html_e( 'Vous partagez le contexte, le lieu et la date envisagee.', 'photovault' ); ?></li><li><strong class="block text-white">02 - <?php esc_html_e( 'Echange', 'photovault' ); ?></strong><?php esc_html_e( 'Le studio precise la direction artistique et la disponibilite.', 'photovault' ); ?></li><li><strong class="block text-white">03 - <?php esc_html_e( 'Confirmation', 'photovault' ); ?></strong><?php esc_html_e( 'La reservation confirmee apparait dans votre espace.', 'photovault' ); ?></li></ol></aside>
		<div>
			<?php if ( isset( $messages[ $status ] ) ) : ?><div id="booking-error" class="mb-7 border-l-4 border-red-400 bg-red-400/10 px-5 py-4 text-sm text-red-100" role="alert"><?php echo esc_html( $messages[ $status ] ); ?></div><?php endif; ?>
			<?php if ( ! $types ) : ?><p class="border-y border-white/10 py-8 text-gray-400"><?php esc_html_e( 'Le module de reservation est temporairement indisponible.', 'photovault' ); ?></p><?php else : ?>
			<form class="grid gap-6 sm:grid-cols-2" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" <?php echo isset( $messages[ $status ] ) ? 'aria-describedby="booking-error"' : ''; ?>><input type="hidden" name="action" value="photovault_create_shooting"><?php wp_nonce_field( 'photovault_create_shooting', 'photovault_shooting_nonce' ); ?>
				<label class="block sm:col-span-2"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Type de shooting', 'photovault' ); ?></span><select class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_type" required><option value=""><?php esc_html_e( 'Choisir', 'photovault' ); ?></option><?php foreach ( $types as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
				<label class="block"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Date souhaitee', 'photovault' ); ?></span><input class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_date" type="date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" max="<?php echo esc_attr( wp_date( 'Y-m-d', strtotime( '+2 years' ) ) ); ?>" required></label>
				<label class="block"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Lieu envisage', 'photovault' ); ?></span><input class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_location" maxlength="160" autocomplete="street-address" required></label>
				<label class="block"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Nom de contact', 'photovault' ); ?></span><input class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_contact_name" maxlength="120" autocomplete="name" value="<?php echo esc_attr( $user->display_name ); ?>" required></label>
				<label class="block"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'E-mail verifie du compte', 'photovault' ); ?></span><input class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-gray-300" name="shooting_contact_email" type="email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" readonly required></label>
				<label class="block sm:col-span-2"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Telephone', 'photovault' ); ?> <span class="font-normal text-gray-500"><?php esc_html_e( '(facultatif, format international)', 'photovault' ); ?></span></span><input class="w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_contact_phone" maxlength="16" pattern="\+[1-9][0-9]{7,14}" autocomplete="tel" value="<?php echo esc_attr( $phone ); ?>"></label>
				<label class="block sm:col-span-2"><span class="mb-2 block text-sm font-bold"><?php esc_html_e( 'Intention et contexte', 'photovault' ); ?></span><textarea class="min-h-40 w-full border border-white/15 bg-[#151412] px-4 py-3 text-white" name="shooting_message" minlength="10" maxlength="2000" placeholder="<?php esc_attr_e( 'Personnes, ambiance, usage des images, contraintes ou references utiles.', 'photovault' ); ?>" required></textarea></label>
				<div class="sm:col-span-2 flex flex-col gap-4 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between"><p class="max-w-lg text-xs leading-5 text-gray-500"><?php esc_html_e( 'En envoyant cette demande, vous autorisez PhotoVault a utiliser ces informations uniquement pour organiser le shooting.', 'photovault' ); ?></p><button class="rounded-md bg-amber-300 px-6 py-3 text-sm font-black text-black" type="submit"><?php esc_html_e( 'Envoyer la demande', 'photovault' ); ?></button></div>
			</form><?php endif; ?>
		</div>
	</div></section>
</main>

<?php get_footer(); ?>
