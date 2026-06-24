<?php
/**
 * Template Name: PhotoVault Contact
 *
 * @package PhotoVault
 */

$message = '';
$error   = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['photovault_contact_nonce'] ) ) {
		if ( wp_verify_nonce( $_POST['photovault_contact_nonce'], 'photovault_contact_action' ) ) {
			$name    = sanitize_text_field( $_POST['contact_name'] );
			$email   = sanitize_email( $_POST['email'] );
			$subject = sanitize_text_field( $_POST['contact_subject'] );
			$content = sanitize_textarea_field( $_POST['contact_message'] );

		if ( empty( $name ) || empty( $email ) || empty( $content ) ) {
			$error = 'Veuillez remplir tous les champs obligatoires.';
		} else {
			// Envoyer un email de support (silencieux pour la démo)
			$to      = get_option( 'admin_email' );
			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $name . ' <' . $email . '>' );
			$body    = "<p><strong>Sujet:</strong> {$subject}</p><p><strong>Message:</strong><br>{$content}</p>";
			
			@wp_mail( $to, 'PhotoVault Contact: ' . $subject, $body, $headers );
			$message = 'Votre message a été envoyé avec succès ! (Simulation de succès - Aucun SMTP requis).';
		}
	} else {
		$error = 'Échec de la vérification de sécurité.';
	}
}

get_header();
?>

<div class="py-20 bg-[#0d0c0b] min-h-screen">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
		<header class="text-center space-y-4">
			<h1 class="text-4xl sm:text-6xl font-extrabold text-white">Contactez le <span class="text-indigo-500">Support</span></h1>
			<p class="text-gray-400 text-lg max-w-xl mx-auto">Une question ? Un problème technique ? Notre équipe est là pour vous aider.</p>
		</header>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
			<!-- Infos -->
			<div class="space-y-6 md:col-span-1">
				<div class="glass-effect p-6 rounded-2xl border border-gray-800 space-y-4">
					<h3 class="text-lg font-bold text-white">Nos Bureaux</h3>
					<p class="text-sm text-gray-400">PhotoVault Inc.<br>42 Rue de l'Art Visuel<br>75001 Paris, France</p>
				</div>
				<div class="glass-effect p-6 rounded-2xl border border-gray-800 space-y-4">
					<h3 class="text-lg font-bold text-white">Support Direct</h3>
					<p class="text-sm text-gray-400">support@photovault.local<br>+33 (0)1 23 45 67 89</p>
				</div>
			</div>

			<!-- Formulaire -->
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

					<div class="grid grid-cols-2 gap-4">
						<div>
							<label for="name" class="block text-xs font-semibold text-gray-400 uppercase mb-1">Nom / Prénom</label>
							<input id="name" name="contact_name" type="text" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
						</div>
						<div>
							<label for="email" class="block text-xs font-semibold text-gray-400 uppercase mb-1">Adresse E-mail</label>
							<input id="email" name="email" type="email" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
						</div>
					</div>

					<div>
						<label for="subject" class="block text-xs font-semibold text-gray-400 uppercase mb-1">Sujet</label>
						<input id="subject" name="contact_subject" type="text" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
					</div>

					<div>
						<label for="message" class="block text-xs font-semibold text-gray-400 uppercase mb-1">Message</label>
						<textarea id="message" name="contact_message" rows="5" required class="w-full px-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
					</div>

					<div class="pt-2">
						<button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-center block transition-all shadow-lg cursor-pointer">
							Envoyer le message
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
