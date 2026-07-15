<?php
/**
 * Global public footer.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_dashboard_surface = function_exists( 'photovault_is_dashboard_surface' ) ? photovault_is_dashboard_surface() : is_page_template( 'page-dashboard.php' );
$newsletter_status    = isset( $_GET['newsletter'] ) ? sanitize_key( wp_unslash( $_GET['newsletter'] ) ) : '';
$privacy_url          = get_privacy_policy_url();
$legal_page           = get_page_by_path( 'mentions-legales' );
$terms_page           = get_page_by_path( 'conditions-generales' );
?>

<?php if ( ! $is_dashboard_surface ) : ?>
	<footer class="mt-auto border-t border-white/10 bg-[#090808] text-gray-300">
		<div class="mx-auto max-w-[90rem] px-5 py-16 sm:px-8 lg:px-12 lg:py-20">
			<div class="grid gap-14 lg:grid-cols-12 lg:gap-10">
				<div class="lg:col-span-5">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex text-2xl font-extrabold text-white" aria-label="<?php esc_attr_e( 'PhotoVault, accueil', 'photovault' ); ?>">Photo<span class="text-amber-200">Vault</span></a>
					<p class="mt-6 max-w-md font-serif text-2xl leading-9 text-gray-200"><?php esc_html_e( 'Des archives visuelles pour ce qui mérite de rester.', 'photovault' ); ?></p>
					<p class="mt-5 max-w-lg text-sm leading-7 text-gray-400"><?php esc_html_e( 'Portfolio officiel, collections protégées et créations photographiques sur mesure entre Porto-Novo, Cotonou et les territoires documentés.', 'photovault' ); ?></p>
				</div>

				<div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-7">
					<div>
						<h2 class="pv-footer-heading"><?php esc_html_e( 'Explorer', 'photovault' ); ?></h2>
						<ul class="mt-5 space-y-3 text-sm">
							<li><a class="pv-footer-link" href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>"><?php esc_html_e( 'Galerie', 'photovault' ); ?></a></li>
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'Carnets visuels', 'photovault' ); ?></a></li>
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'À propos', 'photovault' ); ?></a></li>
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'photovault' ); ?></a></li>
						</ul>
					</div>

					<div>
						<h2 class="pv-footer-heading"><?php esc_html_e( 'Services', 'photovault' ); ?></h2>
						<ul class="mt-5 space-y-3 text-sm">
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/booking/' ) ); ?>"><?php esc_html_e( 'Réserver un shooting', 'photovault' ); ?></a></li>
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>"><?php esc_html_e( 'Tarifs', 'photovault' ); ?></a></li>
							<?php if ( is_user_logged_in() ) : ?>
								<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>"><?php esc_html_e( 'Mon espace', 'photovault' ); ?></a></li>
							<?php else : ?>
								<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Créer un compte', 'photovault' ); ?></a></li>
							<?php endif; ?>
						</ul>
					</div>

					<div class="col-span-2 sm:col-span-1">
						<h2 class="pv-footer-heading"><?php esc_html_e( 'Informations', 'photovault' ); ?></h2>
						<ul class="mt-5 space-y-3 text-sm">
							<li><a class="pv-footer-link" href="<?php echo esc_url( home_url( '/fonctionnalites/' ) ); ?>"><?php esc_html_e( 'Fonctionnement', 'photovault' ); ?></a></li>
							<?php if ( $privacy_url ) : ?><li><a class="pv-footer-link" href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Confidentialité', 'photovault' ); ?></a></li><?php endif; ?>
							<?php if ( $legal_page instanceof WP_Post ) : ?><li><a class="pv-footer-link" href="<?php echo esc_url( get_permalink( $legal_page ) ); ?>"><?php esc_html_e( 'Mentions légales', 'photovault' ); ?></a></li><?php endif; ?>
							<?php if ( $terms_page instanceof WP_Post ) : ?><li><a class="pv-footer-link" href="<?php echo esc_url( get_permalink( $terms_page ) ); ?>"><?php esc_html_e( 'Conditions générales', 'photovault' ); ?></a></li><?php endif; ?>
						</ul>
					</div>
				</div>
			</div>

			<div class="mt-16 grid gap-8 border-t border-white/10 pt-10 lg:grid-cols-12 lg:items-end">
				<div class="lg:col-span-5">
					<p class="pv-footer-heading"><?php esc_html_e( 'Lettre des archives', 'photovault' ); ?></p>
					<p class="mt-3 max-w-md text-sm leading-6 text-gray-400"><?php esc_html_e( 'Nouvelles séries, carnets d’atelier et invitations privées, avec votre consentement.', 'photovault' ); ?></p>
				</div>
				<div class="lg:col-span-7">
					<?php if ( $newsletter_status ) : ?>
						<div class="mb-4 flex items-start justify-between gap-4 border px-4 py-3 text-sm <?php echo in_array( $newsletter_status, array( 'subscribed', 'confirmed', 'unsubscribed' ), true ) ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-100' : ( 'confirmation_required' === $newsletter_status ? 'border-amber-400/30 bg-amber-400/10 text-amber-100' : 'border-red-500/30 bg-red-500/10 text-red-100' ); ?>" role="status" data-pv-toast>
							<span><?php echo esc_html( in_array( $newsletter_status, array( 'subscribed', 'confirmed' ), true ) ? __( 'Votre inscription est enregistrée.', 'photovault' ) : ( 'confirmation_required' === $newsletter_status ? __( 'Consultez votre e-mail pour confirmer votre inscription.', 'photovault' ) : ( 'unsubscribed' === $newsletter_status ? __( 'Votre désinscription est enregistrée.', 'photovault' ) : __( 'L’opération n’a pas abouti. Vérifiez les informations puis réessayez.', 'photovault' ) ) ) ); ?></span>
							<button type="button" class="pv-header-icon h-8 w-8 shrink-0" aria-label="<?php esc_attr_e( 'Fermer la notification', 'photovault' ); ?>" data-pv-toast-close><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" /></svg></button>
						</div>
					<?php endif; ?>

					<?php if ( function_exists( 'newsletter_campaign_kit_handle_subscribe' ) ) : ?>
						<form class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
							<input type="hidden" name="action" value="newsletter_campaign_kit_subscribe">
							<input type="hidden" name="newsletter_source" value="front_footer">
							<?php wp_nonce_field( 'newsletter_campaign_kit_subscribe', 'newsletter_campaign_kit_nonce' ); ?>
							<label class="sr-only" for="footer-newsletter-email"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></label>
							<input id="footer-newsletter-email" name="newsletter_email" type="email" autocomplete="email" placeholder="vous@exemple.com" class="min-h-12 w-full border border-white/15 bg-white/[0.03] px-4 text-sm text-white outline-none transition focus:border-amber-200" required>
							<button type="submit" class="pv-header-cta min-h-12 justify-center px-6"><?php esc_html_e( 'S’inscrire', 'photovault' ); ?></button>
							<label class="flex items-start gap-3 text-xs leading-5 text-gray-500 sm:col-span-2">
								<input class="mt-1 h-4 w-4 shrink-0 border-gray-700 bg-gray-950 text-amber-200" type="checkbox" name="newsletter_consent" value="1" required>
								<span><?php esc_html_e( 'J’accepte de recevoir les actualités éditoriales et je peux me désinscrire à tout moment.', 'photovault' ); ?></span>
							</label>
						</form>
					<?php else : ?>
						<p class="text-sm text-amber-100"><?php esc_html_e( 'La lettre des archives est momentanément indisponible.', 'photovault' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-8 text-xs text-gray-600 sm:flex-row sm:items-center sm:justify-between">
				<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> PhotoVault. <?php esc_html_e( 'Tous droits réservés.', 'photovault' ); ?></p>
				<p><?php esc_html_e( 'Direction artistique et développement par Enok Junior MIGNANWANDE.', 'photovault' ); ?></p>
			</div>
		</div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
