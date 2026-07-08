<?php
/**
 * Template Name: PhotoVault Contact
 *
 * @package PhotoVault
 */

$message = '';
$error   = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['photovault_contact_nonce'] ) ) {
	if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['photovault_contact_nonce'] ) ), 'photovault_contact_action' ) ) {
		$name         = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
		$email        = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$request_type = isset( $_POST['request_type'] ) ? sanitize_key( wp_unslash( $_POST['request_type'] ) ) : 'general';
		$subject      = isset( $_POST['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_subject'] ) ) : '';
		$collection   = isset( $_POST['collection_name'] ) ? sanitize_text_field( wp_unslash( $_POST['collection_name'] ) ) : '';
		$content      = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

		if ( empty( $name ) || empty( $email ) || empty( $content ) || ! is_email( $email ) ) {
			$error = __( 'Veuillez remplir tous les champs obligatoires avec une adresse e-mail valide.', 'photovault' );
		} elseif ( 'access' === $request_type && function_exists( 'photovault_create_access_request' ) ) {
			$result = photovault_create_access_request(
				array(
					'name'       => $name,
					'email'      => $email,
					'subject'    => $subject ? $subject : __( 'Demande d acces protege', 'photovault' ),
					'collection' => $collection,
					'message'    => $content,
				)
			);

			if ( is_wp_error( $result ) ) {
				$error = $result->get_error_message();
			} else {
				$message = __( 'Votre demande d acces a ete enregistree. Elle sera examinee manuellement.', 'photovault' );
			}
		} else {
			$to      = get_option( 'admin_email' );
			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $name . ' <' . $email . '>' );
			$body    = sprintf(
				'<p><strong>Type:</strong> %1$s</p><p><strong>Sujet:</strong> %2$s</p><p><strong>Collection:</strong> %3$s</p><p><strong>Message:</strong><br>%4$s</p>',
				esc_html( $request_type ),
				esc_html( $subject ),
				esc_html( $collection ),
				nl2br( esc_html( $content ) )
			);

			wp_mail( $to, 'PhotoVault Contact: ' . $subject, $body, $headers );
			$message = __( 'Votre message a ete envoye avec succes.', 'photovault' );
		}
	} else {
		$error = __( 'Echec de la verification de securite.', 'photovault' );
	}
}

get_header();
?>

<div class="py-20 bg-[#0d0c0b] min-h-screen">
	<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<header class="text-center space-y-4">
			<p class="text-xs font-bold uppercase tracking-[0.28em] text-indigo-300">Contact & acces</p>
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">Parlez-nous de votre demande</h1>
			<p class="text-gray-300 text-lg max-w-2xl mx-auto">Acces a une collection protegee, reservation de shooting, licence, tirage ou question generale: chaque demande arrive au bon endroit.</p>
		</header>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<div class="space-y-6 md:col-span-1">
				<div class="glass-effect p-6 rounded-2xl border border-gray-800 space-y-4">
					<h3 class="text-lg font-bold text-white">Archives protegees</h3>
					<p class="text-sm text-gray-300 leading-relaxed">Les demandes d'acces sont enregistrees, examinees manuellement et conservees dans l'espace admin PhotoVault.</p>
				</div>
				<div class="glass-effect p-6 rounded-2xl border border-gray-800 space-y-4">
					<h3 class="text-lg font-bold text-white">Contact direct</h3>
					<p class="text-sm text-gray-300">support@photovault.local<br>Porto-Novo / Cotonou<br>Sur rendez-vous</p>
				</div>
			</div>

			<div class="md:col-span-2">
				<form class="glass-effect p-8 rounded-3xl border border-gray-800 shadow-xl space-y-4" action="" method="POST">
					<?php wp_nonce_field( 'photovault_contact_action', 'photovault_contact_nonce' ); ?>

					<?php if ( ! empty( $error ) ) : ?>
						<div class="bg-red-900/30 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm text-center">
							<?php echo esc_html( $error ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $message ) ) : ?>
						<div class="bg-emerald-900/30 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-lg text-sm text-center">
							<?php echo esc_html( $message ); ?>
						</div>
					<?php endif; ?>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="name" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Nom / Prenom</label>
							<input id="name" name="contact_name" type="text" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label for="email" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Adresse e-mail</label>
							<input id="email" name="email" type="email" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
						</div>
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="request_type" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Type de demande</label>
							<select id="request_type" name="request_type" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
								<option value="access">Acces a une collection protegee</option>
								<option value="shooting">Reservation shooting</option>
								<option value="license">Licence ou tirage</option>
								<option value="general">Question generale</option>
							</select>
						</div>
						<div>
							<label for="collection_name" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Collection / oeuvre</label>
							<input id="collection_name" name="collection_name" type="text" class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Nom de la serie, oeuvre ou projet">
						</div>
					</div>

					<div>
						<label for="subject" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Sujet</label>
						<input id="subject" name="contact_subject" type="text" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div>
						<label for="message" class="block text-xs font-semibold text-gray-300 uppercase mb-1">Message</label>
						<textarea id="message" name="contact_message" rows="6" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Precisez votre besoin, le contexte, les dates ou les droits souhaites."></textarea>
					</div>

					<div class="pt-2">
						<button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-center block transition-all shadow-lg cursor-pointer">
							Envoyer la demande
						</button>
					</div>
				</form>
			</div>
		</div>

		<section class="grid grid-cols-1 md:grid-cols-3 gap-5 border-t border-gray-900 pt-10">
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Demande d'acces</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Precisez la collection, le projet ou la serie protegee que vous souhaitez consulter.</p>
			</div>
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Reservation shooting</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Indiquez la date souhaitee, le lieu, le type de portraits et l'ambiance recherchee.</p>
			</div>
			<div class="p-6 rounded-3xl bg-gray-950/40 border border-gray-800">
				<h3 class="text-lg font-bold text-white mb-2">Licence ou tirage</h3>
				<p class="text-sm text-gray-300 leading-relaxed">Mentionnez l'oeuvre, le format, l'usage prevu et le delai de livraison attendu.</p>
			</div>
		</section>
	</div>
</div>

<?php get_footer(); ?>