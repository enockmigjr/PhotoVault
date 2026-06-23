<?php
/**
 * Template Name: PhotoVault Dashboard
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/login/' ) );
	exit;
}

$current_user = wp_get_current_user();
$stats = photovault_get_photographer_stats( $current_user->ID );

// Récupérer les 4 derniers médias téléchargés par le photographe.
$recent_media_query = new WP_Query( array(
	'post_type'      => 'media_item',
	'post_status'    => array( 'publish', 'private' ),
	'author'         => $current_user->ID,
	'posts_per_page' => 4,
) );

get_header();
?>

<div class="flex min-h-screen bg-[#0b0f19]">
	<!-- Barre latérale -->
	<?php get_template_part( 'templates/dashboard-sidebar' ); ?>

	<!-- Contenu Principal -->
	<main class="flex-1 p-10 overflow-y-auto">
		<div class="max-w-6xl mx-auto">
			<header class="mb-10">
				<h2 class="text-3xl font-extrabold text-white">Ravi de vous revoir, <?php echo esc_html( $current_user->first_name ? $current_user->first_name : $current_user->display_name ); ?> !</h2>
				<p class="text-gray-400 mt-1">Voici l'aperçu de votre activité et de vos statistiques aujourd'hui.</p>
			</header>

			<!-- Grille de statistiques -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
				<!-- Total Médias -->
				<div class="glass-effect p-6 rounded-2xl flex items-center justify-between shadow-lg">
					<div>
						<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Médias</p>
						<h3 class="text-3xl font-extrabold text-white mt-1"><?php echo esc_html( $stats['total'] ); ?></h3>
					</div>
					<div class="p-3 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/20">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
					</div>
				</div>

				<!-- Médias Publics -->
				<div class="glass-effect p-6 rounded-2xl flex items-center justify-between shadow-lg">
					<div>
						<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Médias Publics</p>
						<h3 class="text-3xl font-extrabold text-white mt-1"><?php echo esc_html( $stats['public'] ); ?></h3>
					</div>
					<div class="p-3 rounded-xl bg-emerald-600/20 text-emerald-400 border border-emerald-500/20">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
					</div>
				</div>

				<!-- Médias Protégés -->
				<div class="glass-effect p-6 rounded-2xl flex items-center justify-between shadow-lg">
					<div>
						<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Médias Protégés</p>
						<h3 class="text-3xl font-extrabold text-white mt-1"><?php echo esc_html( $stats['protected'] ); ?></h3>
					</div>
					<div class="p-3 rounded-xl bg-yellow-600/20 text-yellow-400 border border-yellow-500/20">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
					</div>
				</div>

				<!-- Dossiers -->
				<div class="glass-effect p-6 rounded-2xl flex items-center justify-between shadow-lg">
					<div>
						<p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dossiers / Projets</p>
						<h3 class="text-3xl font-extrabold text-white mt-1"><?php echo esc_html( $stats['folders'] ); ?></h3>
					</div>
					<div class="p-3 rounded-xl bg-purple-600/20 text-purple-400 border border-purple-500/20">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
					</div>
				</div>
			</div>

			<!-- Médias Récents -->
			<div class="glass-effect p-8 rounded-2xl shadow-lg">
				<div class="flex justify-between items-center mb-6">
					<h3 class="text-xl font-bold text-white">Médias importés récemment</h3>
					<a href="<?php echo esc_url( home_url( '/my-media/' ) ); ?>" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">Tout afficher &rarr;</a>
				</div>

				<?php if ( $recent_media_query->have_posts() ) : ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
						<?php while ( $recent_media_query->have_posts() ) : $recent_media_query->the_post(); ?>
							<?php get_template_part( 'templates/media-card' ); ?>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				<?php else : ?>
					<div class="text-center py-10">
						<p class="text-gray-500">Aucun média disponible. Téléversez-en un pour commencer.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</main>
</div>

<?php get_footer(); ?>
