<?php
/**
 * Template Name: PhotoVault Contact
 *
 * @package PhotoVault
 */

$notice        = '';
$notice_type   = '';
$name          = '';
$email         = '';
$request_type  = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : 'general';
$subject       = '';
$collection    = '';
$content       = '';
$request_types = photovault_get_contact_request_types();
$collections   = get_terms( array( 'taxonomy' => 'media_folder', 'hide_empty' => false ) );
$collections   = is_wp_error( $collections ) ? array() : $collections;
if ( ! isset( $request_types[ $request_type ] ) ) {
	$request_type = 'general';
}

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['photovault_contact_nonce'] ) ) {
	if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_contact_nonce'] ) ), 'photovault_contact_action' ) ) {
		$name         = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
		$email        = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$request_type = isset( $_POST['request_type'] ) ? sanitize_key( wp_unslash( $_POST['request_type'] ) ) : 'general';
		$subject      = isset( $_POST['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_subject'] ) ) : '';
		$collection   = isset( $_POST['collection_name'] ) ? sanitize_text_field( wp_unslash( $_POST['collection_name'] ) ) : '';
		$content      = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

		if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $content ) || ! is_email( $email ) || ! isset( $request_types[ $request_type ] ) ) {
			$notice      = __( 'Veuillez remplir les champs obligatoires avec une adresse e-mail valide.', 'photovault' );
			$notice_type = 'error';
		} elseif ( 'access' === $request_type && function_exists( 'photovault_create_access_request' ) ) {
			$folder = get_term_by( 'name', $collection, 'media_folder' );
			$result = $folder && ! is_wp_error( $folder )
				? photovault_create_access_request( array( 'name' => $name, 'email' => $email, 'subject' => $subject, 'collection' => $folder->name, 'message' => $content ) )
				: new WP_Error( 'invalid_collection', __( 'Sélectionnez une collection PhotoVault existante.', 'photovault' ) );
			$notice      = is_wp_error( $result ) ? $result->get_error_message() : __( 'Votre demande d’accès a été enregistrée et sera examinée manuellement.', 'photovault' );
			$notice_type = is_wp_error( $result ) ? 'error' : 'success';
		} elseif ( function_exists( 'photovault_rate_limit' ) && ! photovault_rate_limit( 'contact_message', 5, HOUR_IN_SECONDS ) ) {
			$notice      = __( 'Veuillez patienter avant d’envoyer un nouveau message.', 'photovault' );
			$notice_type = 'error';
		} else {
			$sent = photovault_send_contact_notification( array( 'name' => $name, 'email' => $email, 'request_type' => $request_type, 'subject' => $subject, 'collection' => $collection, 'message' => $content ) );
			$notice      = $sent ? __( 'Votre message a été envoyé avec succès.', 'photovault' ) : __( 'Le message n’a pas pu être envoyé. Veuillez réessayer plus tard.', 'photovault' );
			$notice_type = $sent ? 'success' : 'error';
		}
	} else {
		$notice      = __( 'La vérification de sécurité a échoué. Actualisez la page puis réessayez.', 'photovault' );
		$notice_type = 'error';
	}
}

get_header();
?>
<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
	<header class="border-b border-white/10 py-20 sm:py-28">
		<div class="mx-auto grid max-w-[90rem] gap-10 px-5 sm:px-8 lg:grid-cols-12 lg:px-12"><div class="lg:col-span-8"><p class="text-xs font-extrabold uppercase text-amber-200"><?php esc_html_e( 'Contact / Accès', 'photovault' ); ?></p><h1 class="mt-5 max-w-5xl font-serif text-4xl leading-[1.04] text-white sm:text-5xl"><?php esc_html_e( 'Décrivez le projet, l’œuvre ou l’accès recherché.', 'photovault' ); ?></h1></div><p class="max-w-xl self-end text-base leading-8 text-gray-400 lg:col-span-4"><?php esc_html_e( 'Votre demande est orientée vers le bon parcours : collection protégée, shooting, licence, tirage ou question générale.', 'photovault' ); ?></p></div>
	</header>

	<section class="mx-auto grid max-w-[90rem] gap-12 px-5 py-16 sm:px-8 lg:grid-cols-12 lg:px-12 lg:py-24" aria-labelledby="contact-form-title">
		<aside class="lg:col-span-4">
			<p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Avant d’écrire', 'photovault' ); ?></p>
			<h2 class="mt-5 font-serif text-3xl leading-tight text-white"><?php esc_html_e( 'Quelques repères pour une réponse précise.', 'photovault' ); ?></h2>
			<dl class="mt-8 divide-y divide-white/10 border-y border-white/10">
				<div class="py-5"><dt class="text-sm font-bold text-white"><?php esc_html_e( 'Collection protégée', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Indiquez le nom de la série et la raison de votre demande.', 'photovault' ); ?></dd></div>
				<div class="py-5"><dt class="text-sm font-bold text-white"><?php esc_html_e( 'Shooting', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Précisez la date, le lieu, les personnes et l’usage prévu.', 'photovault' ); ?></dd></div>
				<div class="py-5"><dt class="text-sm font-bold text-white"><?php esc_html_e( 'Licence ou tirage', 'photovault' ); ?></dt><dd class="mt-2 text-sm leading-6 text-gray-400"><?php esc_html_e( 'Mentionnez l’œuvre, le format, la diffusion et le délai attendu.', 'photovault' ); ?></dd></div>
			</dl>
			<p class="mt-7 text-sm leading-6 text-gray-500"><?php echo esc_html( sprintf( __( 'Les notifications sont adressées à l’équipe du site (%s).', 'photovault' ), antispambot( get_option( 'admin_email' ) ) ) ); ?></p>
		</aside>

		<div class="lg:col-span-7 lg:col-start-6">
			<h2 id="contact-form-title" class="text-2xl font-bold text-white"><?php esc_html_e( 'Envoyer une demande', 'photovault' ); ?></h2>
			<?php if ( $notice ) : ?><div class="mt-6 flex items-start justify-between gap-4 border px-4 py-3 text-sm <?php echo 'success' === $notice_type ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-100' : 'border-red-500/30 bg-red-500/10 text-red-100'; ?>" role="<?php echo 'success' === $notice_type ? 'status' : 'alert'; ?>" data-pv-toast><span><?php echo esc_html( $notice ); ?></span><button type="button" class="pv-header-icon h-8 w-8 shrink-0" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close>&times;</button></div><?php endif; ?>
			<form class="pv-public-form mt-8" action="<?php echo esc_url( get_permalink() ); ?>" method="post">
				<?php wp_nonce_field( 'photovault_contact_action', 'photovault_contact_nonce' ); ?>
				<div class="grid gap-5 sm:grid-cols-2"><label><span><?php esc_html_e( 'Nom et prénom', 'photovault' ); ?></span><input name="contact_name" type="text" autocomplete="name" maxlength="140" required value="<?php echo esc_attr( $name ); ?>"></label><label><span><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></span><input name="email" type="email" autocomplete="email" maxlength="190" required value="<?php echo esc_attr( $email ); ?>"></label></div>
				<label><span><?php esc_html_e( 'Type de demande', 'photovault' ); ?></span><select name="request_type" required><?php foreach ( $request_types as $type_key => $type_label ) : ?><option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $request_type, $type_key ); ?>><?php echo esc_html( $type_label ); ?></option><?php endforeach; ?></select></label>
				<label><span><?php esc_html_e( 'Collection protégée', 'photovault' ); ?></span><select name="collection_name"><option value=""><?php esc_html_e( 'Sélectionner une collection', 'photovault' ); ?></option><?php foreach ( $collections as $folder ) : ?><option value="<?php echo esc_attr( $folder->name ); ?>" <?php selected( $collection, $folder->name ); ?>><?php echo esc_html( $folder->name ); ?></option><?php endforeach; ?></select></label>
				<label><span><?php esc_html_e( 'Sujet', 'photovault' ); ?></span><input name="contact_subject" type="text" maxlength="190" required value="<?php echo esc_attr( $subject ); ?>"></label>
				<label><span><?php esc_html_e( 'Message', 'photovault' ); ?></span><textarea name="contact_message" rows="7" minlength="10" maxlength="4000" required placeholder="<?php esc_attr_e( 'Contexte, dates, droits ou contraintes utiles.', 'photovault' ); ?>"><?php echo esc_textarea( $content ); ?></textarea></label>
				<button class="pv-public-submit" type="submit"><?php esc_html_e( 'Envoyer la demande', 'photovault' ); ?></button>
			</form>
		</div>
	</section>
</main>
<?php get_footer(); ?>
