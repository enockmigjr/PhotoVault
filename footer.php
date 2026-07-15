<?php
/**
 * Pied de page global du thème PhotoVault.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$is_dashboard_template = function_exists( 'photovault_is_dashboard_surface' ) ? photovault_is_dashboard_surface() : is_page_template( 'page-dashboard.php' );
?>

<?php if ( ! $is_dashboard_template ) : ?>
	<footer class="bg-[#090808] text-gray-300 pt-16 pb-8 border-t border-gray-900 mt-auto font-sans">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
				
				<div class="space-y-4">
					<div class="text-white text-xl font-black tracking-tight">
						Photo<span class="text-indigo-500">Vault</span>
					</div>
					<p class="text-xs leading-relaxed text-gray-300 font-medium">
						Galerie photo privee pour presenter des apercus rapides, proteger les images sensibles et livrer les fichiers HD aux clients autorises.
					</p>
				</div>

				<div>
					<h4 class="text-white font-bold mb-4 tracking-widest uppercase text-xs">Navigation</h4>
					<ul class="space-y-2 text-xs font-semibold">
						<li>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Accueil
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( home_url( '/fonctionnalites/' ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Fonctionnalités
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								À propos
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Contact
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Galerie
							</a>
						</li>
					</ul>
				</div>

				<div>
					<h4 class="text-white font-bold mb-4 tracking-widest uppercase text-xs">Légal</h4>
					<ul class="space-y-2 text-xs font-semibold">
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'mentions-legales' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Mentions Légales
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'politique-de-confidentialite' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Politique de Confidentialité
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'conditions-generales' ) ) ); ?>" class="hover:text-indigo-400 transition-colors">
								Conditions Générales
							</a>
						</li>
					</ul>
				</div>

				<div class="space-y-4">
					<h4 class="text-white font-bold mb-2 tracking-widest uppercase text-xs">Restez connecte</h4>
					<p class="text-xs text-gray-300 font-medium">Recevez les carnets visuels, les nouvelles collections et les invitations privees.</p>
					<?php $newsletter_status = isset( $_GET['newsletter'] ) ? sanitize_key( wp_unslash( $_GET['newsletter'] ) ) : ''; ?>
					<?php if ( 'subscribed' === $newsletter_status ) : ?>
						<p class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-100">Inscription enregistree.</p>
					<?php elseif ( 'confirmation_required' === $newsletter_status ) : ?>
						<p class="rounded-lg border border-amber-400/30 bg-amber-400/10 px-3 py-2 text-xs font-semibold text-amber-100">Consultez votre e-mail pour confirmer l'inscription. La reponse reste identique si l'adresse est deja connue.</p>
					<?php elseif ( 'confirmed' === $newsletter_status ) : ?>
						<p class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-100">Adresse confirmee. Votre abonnement est maintenant actif.</p>
					<?php elseif ( 'unsubscribed' === $newsletter_status ) : ?>
						<p class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-100">Desinscription enregistree.</p>
					<?php elseif ( in_array( $newsletter_status, array( 'invalid_email', 'consent_required', 'security_failed', 'db_error', 'confirmation_email_failed', 'confirmation_invalid', 'unsubscribe_invalid', 'unsubscribe_failed' ), true ) ) : ?>
						<p class="rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-100">Operation newsletter impossible. Verifiez votre e-mail, votre consentement ou le lien utilise.</p>
					<?php endif; ?>
					<?php if ( function_exists( 'newsletter_campaign_kit_handle_subscribe' ) ) : ?>
						<form class="space-y-3" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
							<input type="hidden" name="action" value="newsletter_campaign_kit_subscribe">
							<input type="hidden" name="newsletter_source" value="front_footer">
							<?php wp_nonce_field( 'newsletter_campaign_kit_subscribe', 'newsletter_campaign_kit_nonce' ); ?>
							<div class="flex flex-col gap-2 sm:flex-row">
								<input name="newsletter_email" type="email" placeholder="Votre email..." class="bg-gray-950/40 text-white placeholder-gray-600 px-4 py-2.5 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-gray-800 w-full" required>
								<button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-md cursor-pointer">
									OK
								</button>
							</div>
							<label class="flex items-start gap-2 text-[10px] leading-4 text-gray-400">
								<input class="mt-0.5 h-3.5 w-3.5 rounded border-gray-700 bg-gray-950 text-indigo-500" type="checkbox" name="newsletter_consent" value="1" required>
								<span>J'accepte de recevoir les actualites editoriales de PhotoVault et je peux me desinscrire a tout moment.</span>
							</label>
						</form>
					<?php else : ?>
						<p class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-100">Newsletter en cours d'activation.</p>
					<?php endif; ?>
				</div>
			</div>

			<div class="border-t border-gray-900 pt-8 mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] text-gray-600">
				<div>
					&copy; <?php echo date('Y'); ?> <span class="text-gray-300 font-bold">EXO PAUL</span>. Tous droits réservés.
				</div>
				<div>
					Développé par <a href="https://github.com/enockmigjr" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-indigo-400 font-bold transition-colors">Enok Junior MIGNANWANDE</a>
				</div>
			</div>

		</div>
	</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
