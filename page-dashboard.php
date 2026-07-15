<?php
/**
 * Template Name: PhotoVault Dashboard
 *
 * @package PhotoVault
 */

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/login/' ) );
	exit;
}

$current_user = wp_get_current_user();
$user_id      = (int) $current_user->ID;
$is_manager   = function_exists( 'photovault_current_user_can' ) ? photovault_current_user_can( 'photovault_manage_platform' ) : current_user_can( 'manage_options' );
$sections     = array( 'overview', 'favorites', 'downloads', 'access', 'bookings', 'newsletter', 'security' );
if ( $is_manager ) {
	$sections[] = 'analytics';
}
$section = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'overview';
$section = in_array( $section, $sections, true ) ? $section : 'overview';

$favorite_ids = function_exists( 'photovault_get_user_favorite_ids' ) ? photovault_get_user_favorite_ids( $user_id, 100 ) : array();
$downloads    = function_exists( 'photovault_get_user_download_history' ) ? photovault_get_user_download_history( $user_id, 50 ) : array();
$requests     = function_exists( 'photovault_get_user_access_requests' ) ? photovault_get_user_access_requests( $user_id, 50 ) : array();
$grants       = function_exists( 'photovault_get_user_access_grants' ) ? photovault_get_user_access_grants( $user_id, 50 ) : array();
$shootings    = function_exists( 'photovault_get_user_shootings' ) ? photovault_get_user_shootings( $user_id, 50 ) : array();
$subscriber   = function_exists( 'newsletter_campaign_kit_get_subscriber_by_email' ) ? newsletter_campaign_kit_get_subscriber_by_email( $current_user->user_email ) : null;
$email_verified = function_exists( 'identity_security_kit_is_email_verified' ) ? identity_security_kit_is_email_verified( $user_id ) : true;
$phone          = function_exists( 'identity_security_kit_phone_meta_key' ) ? (string) get_user_meta( $user_id, identity_security_kit_phone_meta_key(), true ) : '';
$phone_verified = function_exists( 'identity_security_kit_is_phone_verified' ) ? identity_security_kit_is_phone_verified( $user_id ) : false;
$mfa_methods    = function_exists( 'identity_security_kit_get_user_mfa_methods' ) ? identity_security_kit_get_user_mfa_methods( $user_id ) : array();
$mfa_required   = function_exists( 'identity_security_kit_user_requires_mfa' ) ? identity_security_kit_user_requires_mfa( $current_user ) : false;
$mfa_deadline   = function_exists( 'identity_security_kit_get_mfa_deadline' ) ? identity_security_kit_get_mfa_deadline( $user_id ) : 0;

$section_titles = array(
	'overview'   => __( 'Votre espace PhotoVault', 'photovault' ),
	'favorites'  => __( 'Oeuvres favorites', 'photovault' ),
	'downloads'  => __( 'Historique des telechargements', 'photovault' ),
	'access'     => __( 'Collections et autorisations', 'photovault' ),
	'bookings'   => __( 'Reservations de shootings', 'photovault' ),
	'newsletter' => __( 'Preferences editoriales', 'photovault' ),
	'security'   => __( 'Securite du compte', 'photovault' ),
	'analytics'  => __( 'Analytique de la plateforme', 'photovault' ),
);

get_header();
?>

<div class="min-h-screen bg-[#0d0c0b] text-white lg:flex">
	<?php get_template_part( 'templates/dashboard-sidebar', null, array( 'section' => $section ) ); ?>

	<main class="min-w-0 flex-1 px-4 py-8 sm:px-8 lg:px-10 lg:py-10">
		<div class="mx-auto max-w-7xl">
			<header class="mb-10 flex flex-col gap-5 border-b border-white/10 pb-8 md:flex-row md:items-end md:justify-between">
				<div>
					<p class="text-xs font-bold uppercase tracking-[0.24em] text-amber-300"><?php esc_html_e( 'Espace personnel', 'photovault' ); ?></p>
					<h1 class="mt-3 text-3xl font-black sm:text-4xl"><?php echo esc_html( $section_titles[ $section ] ); ?></h1>
					<p class="mt-3 max-w-2xl text-sm leading-6 text-gray-400"><?php echo esc_html( sprintf( __( 'Bonjour %s. Retrouvez ici vos oeuvres, vos acces et les reglages de votre compte.', 'photovault' ), $current_user->display_name ?: $current_user->user_login ) ); ?></p>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="inline-flex items-center justify-center rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black transition hover:bg-amber-200"><?php esc_html_e( 'Explorer la galerie', 'photovault' ); ?></a>
			</header>

			<?php if ( ! $email_verified ) : ?>
				<section class="mb-8 flex flex-col gap-4 border-l-4 border-amber-300 bg-amber-300/10 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
					<div><h2 class="font-bold text-amber-100"><?php esc_html_e( 'Adresse e-mail a verifier', 'photovault' ); ?></h2><p class="mt-1 text-sm text-amber-100/75"><?php esc_html_e( 'La confirmation est requise avant le telechargement des originaux et certains acces.', 'photovault' ); ?></p></div>
					<a href="<?php echo esc_url( home_url( '/profile/' ) ); ?>" class="text-sm font-bold text-amber-200 underline underline-offset-4"><?php esc_html_e( 'Verifier maintenant', 'photovault' ); ?></a>
				</section>
			<?php endif; ?>

			<?php if ( 'overview' === $section ) : ?>
				<section aria-labelledby="dashboard-summary-title">
					<h2 id="dashboard-summary-title" class="sr-only"><?php esc_html_e( 'Resume du compte', 'photovault' ); ?></h2>
					<div class="grid gap-px overflow-hidden rounded-md border border-white/10 bg-white/10 sm:grid-cols-2 xl:grid-cols-5">
						<?php
						$summary = array(
							array( __( 'Favoris', 'photovault' ), count( $favorite_ids ), add_query_arg( 'section', 'favorites', home_url( '/dashboard/' ) ) ),
							array( __( 'Telechargements', 'photovault' ), count( $downloads ), add_query_arg( 'section', 'downloads', home_url( '/dashboard/' ) ) ),
							array( __( 'Demandes d acces', 'photovault' ), count( $requests ), add_query_arg( 'section', 'access', home_url( '/dashboard/' ) ) ),
							array( __( 'Collections actives', 'photovault' ), count( array_filter( $grants, static function ( $grant ) { return 'active' === $grant['status']; } ) ), add_query_arg( 'section', 'access', home_url( '/dashboard/' ) ) ),
							array( __( 'Reservations', 'photovault' ), count( $shootings ), add_query_arg( 'section', 'bookings', home_url( '/dashboard/' ) ) ),
						);
						foreach ( $summary as $item ) :
							?>
							<a href="<?php echo esc_url( $item[2] ); ?>" class="bg-[#151412] px-6 py-6 transition hover:bg-[#1b1916]"><span class="text-xs font-bold uppercase tracking-[0.16em] text-gray-500"><?php echo esc_html( $item[0] ); ?></span><strong class="mt-3 block text-3xl font-black text-white"><?php echo esc_html( number_format_i18n( $item[1] ) ); ?></strong></a>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="mt-12 grid gap-10 xl:grid-cols-[1.6fr,1fr]">
					<div>
						<div class="mb-5 flex items-end justify-between"><div><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Selection personnelle', 'photovault' ); ?></p><h2 class="mt-2 text-2xl font-bold"><?php esc_html_e( 'Favoris recents', 'photovault' ); ?></h2></div><a class="text-sm font-semibold text-amber-200" href="<?php echo esc_url( add_query_arg( 'section', 'favorites', home_url( '/dashboard/' ) ) ); ?>"><?php esc_html_e( 'Tout voir', 'photovault' ); ?></a></div>
						<?php if ( $favorite_ids ) : ?>
							<?php $favorite_query = new WP_Query( array( 'post_type' => 'media_item', 'post_status' => array( 'publish', 'private' ), 'post__in' => array_slice( $favorite_ids, 0, 4 ), 'orderby' => 'post__in', 'posts_per_page' => 4 ) ); ?>
							<div class="grid gap-5 sm:grid-cols-2"><?php while ( $favorite_query->have_posts() ) : $favorite_query->the_post(); get_template_part( 'templates/media-card' ); endwhile; wp_reset_postdata(); ?></div>
						<?php else : ?>
							<div class="border-y border-white/10 py-10"><p class="text-gray-400"><?php esc_html_e( 'Ajoutez des oeuvres depuis la galerie pour construire votre selection.', 'photovault' ); ?></p></div>
						<?php endif; ?>
					</div>

					<div>
						<p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Etat du compte', 'photovault' ); ?></p>
						<h2 class="mt-2 text-2xl font-bold"><?php esc_html_e( 'Acces et securite', 'photovault' ); ?></h2>
						<dl class="mt-5 divide-y divide-white/10 border-y border-white/10">
							<div class="flex items-center justify-between py-4"><dt class="text-sm text-gray-400"><?php esc_html_e( 'E-mail', 'photovault' ); ?></dt><dd class="text-sm font-bold <?php echo $email_verified ? 'text-emerald-300' : 'text-amber-200'; ?>"><?php echo esc_html( $email_verified ? __( 'Verifie', 'photovault' ) : __( 'En attente', 'photovault' ) ); ?></dd></div>
							<div class="flex items-center justify-between py-4"><dt class="text-sm text-gray-400"><?php esc_html_e( 'Telephone', 'photovault' ); ?></dt><dd class="text-sm font-bold <?php echo $phone_verified ? 'text-emerald-300' : 'text-gray-300'; ?>"><?php echo esc_html( $phone_verified ? __( 'Verifie', 'photovault' ) : ( $phone ? __( 'Non verifie', 'photovault' ) : __( 'Non renseigne', 'photovault' ) ) ); ?></dd></div>
							<div class="flex items-center justify-between py-4"><dt class="text-sm text-gray-400"><?php esc_html_e( 'Double authentification', 'photovault' ); ?></dt><dd class="text-sm font-bold <?php echo $mfa_methods ? 'text-emerald-300' : 'text-gray-300'; ?>"><?php echo esc_html( $mfa_methods ? implode( ', ', array_map( 'strtoupper', $mfa_methods ) ) : __( 'Inactive', 'photovault' ) ); ?></dd></div>
							<div class="flex items-center justify-between py-4"><dt class="text-sm text-gray-400"><?php esc_html_e( 'Newsletter', 'photovault' ); ?></dt><dd class="text-sm font-bold text-gray-300"><?php echo esc_html( $subscriber ? ucfirst( $subscriber['status'] ) : __( 'Non inscrit', 'photovault' ) ); ?></dd></div>
						</dl>
						<a href="<?php echo esc_url( add_query_arg( 'section', 'security', home_url( '/dashboard/' ) ) ); ?>" class="mt-5 inline-flex text-sm font-bold text-amber-200"><?php esc_html_e( 'Examiner la securite', 'photovault' ); ?> &rarr;</a>
					</div>
				</section>
			<?php elseif ( 'favorites' === $section ) : ?>
				<?php if ( $favorite_ids ) : ?>
					<?php $favorite_query = new WP_Query( array( 'post_type' => 'media_item', 'post_status' => array( 'publish', 'private' ), 'post__in' => $favorite_ids, 'orderby' => 'post__in', 'posts_per_page' => 100 ) ); ?>
					<div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4" data-pv-favorites-grid><?php while ( $favorite_query->have_posts() ) : $favorite_query->the_post(); ?><div data-pv-favorite-card="<?php echo esc_attr( get_the_ID() ); ?>"><?php get_template_part( 'templates/media-card' ); ?></div><?php endwhile; wp_reset_postdata(); ?></div>
				<?php else : ?>
					<section class="border-y border-white/10 py-16 text-center"><h2 class="text-2xl font-bold"><?php esc_html_e( 'Votre selection est encore vide', 'photovault' ); ?></h2><p class="mx-auto mt-3 max-w-xl text-gray-400"><?php esc_html_e( 'Utilisez le coeur present sur chaque oeuvre pour la retrouver ici.', 'photovault' ); ?></p><a href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>" class="mt-6 inline-flex rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black"><?php esc_html_e( 'Parcourir les oeuvres', 'photovault' ); ?></a></section>
				<?php endif; ?>
			<?php elseif ( 'downloads' === $section ) : ?>
				<section class="overflow-hidden border-y border-white/10">
					<?php if ( ! $downloads ) : ?><p class="py-12 text-center text-gray-400"><?php esc_html_e( 'Aucun original telecharge pour le moment.', 'photovault' ); ?></p><?php endif; ?>
					<?php foreach ( $downloads as $download ) : ?>
						<?php $can_download = function_exists( 'photovault_user_can_access_media' ) && photovault_user_can_access_media( $download['media_id'], $user_id ) && $email_verified; ?>
						<article class="flex flex-col gap-4 border-b border-white/10 py-5 last:border-0 sm:flex-row sm:items-center">
							<img class="h-20 w-24 rounded object-cover" src="<?php echo esc_url( photovault_get_secure_image_url( $download['media_id'], 'card' ) ); ?>" alt="" loading="lazy" width="96" height="80">
							<div class="min-w-0 flex-1"><h2 class="truncate font-bold"><?php echo esc_html( $download['post_title'] ); ?></h2><p class="mt-1 text-sm text-gray-500"><?php echo esc_html( get_date_from_gmt( $download['created_at'], get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></p></div>
							<?php if ( $can_download ) : ?><a class="text-sm font-bold text-amber-200" href="<?php echo esc_url( photovault_get_secure_image_url( $download['media_id'], '', true ) ); ?>"><?php esc_html_e( 'Telecharger a nouveau', 'photovault' ); ?></a><?php else : ?><span class="text-xs font-semibold text-gray-600"><?php esc_html_e( 'Acces indisponible', 'photovault' ); ?></span><?php endif; ?>
						</article>
					<?php endforeach; ?>
				</section>
			<?php elseif ( 'access' === $section ) : ?>
				<section class="grid gap-10 xl:grid-cols-2">
					<div><div class="mb-5 flex items-end justify-between"><h2 class="text-2xl font-bold"><?php esc_html_e( 'Demandes', 'photovault' ); ?></h2><a class="text-sm font-bold text-amber-200" href="<?php echo esc_url( add_query_arg( 'type', 'access', home_url( '/contact/' ) ) ); ?>"><?php esc_html_e( 'Nouvelle demande', 'photovault' ); ?></a></div><div class="divide-y divide-white/10 border-y border-white/10"><?php if ( ! $requests ) : ?><p class="py-10 text-gray-400"><?php esc_html_e( 'Aucune demande enregistree.', 'photovault' ); ?></p><?php endif; ?><?php foreach ( $requests as $request ) : ?><article class="py-5"><div class="flex items-start justify-between gap-4"><div><h3 class="font-bold"><?php echo esc_html( $request['collection'] ?: $request['subject'] ); ?></h3><p class="mt-1 text-sm text-gray-500"><?php echo esc_html( get_date_from_gmt( $request['created_at'], get_option( 'date_format' ) ) ); ?></p></div><span class="rounded-full border border-white/15 px-3 py-1 text-xs font-bold uppercase text-gray-300"><?php echo esc_html( $request['status'] ); ?></span></div></article><?php endforeach; ?></div></div>
					<div><h2 class="mb-5 text-2xl font-bold"><?php esc_html_e( 'Autorisations', 'photovault' ); ?></h2><div class="divide-y divide-white/10 border-y border-white/10"><?php if ( ! $grants ) : ?><p class="py-10 text-gray-400"><?php esc_html_e( 'Aucune collection privee autorisee.', 'photovault' ); ?></p><?php endif; ?><?php foreach ( $grants as $grant ) : ?><?php $folder = get_term( $grant['folder_id'], 'media_folder' ); ?><article class="flex items-center justify-between gap-4 py-5"><div><h3 class="font-bold"><?php echo esc_html( $folder && ! is_wp_error( $folder ) ? $folder->name : __( 'Collection', 'photovault' ) ); ?></h3><p class="mt-1 text-sm text-gray-500"><?php echo esc_html( get_date_from_gmt( $grant['updated_at'], get_option( 'date_format' ) ) ); ?></p></div><span class="text-sm font-bold <?php echo 'active' === $grant['status'] ? 'text-emerald-300' : 'text-gray-500'; ?>"><?php echo esc_html( ucfirst( $grant['status'] ) ); ?></span></article><?php endforeach; ?></div></div>
				</section>
			<?php elseif ( 'bookings' === $section ) : ?>
				<?php $shooting_types = function_exists( 'photovault_get_shooting_types' ) ? photovault_get_shooting_types() : array(); $shooting_statuses = function_exists( 'photovault_get_shooting_statuses' ) ? photovault_get_shooting_statuses() : array(); ?>
				<section>
					<div class="mb-7 flex flex-col gap-4 border-b border-white/10 pb-6 sm:flex-row sm:items-end sm:justify-between"><div><h2 class="text-2xl font-bold"><?php esc_html_e( 'Vos projets photographiques', 'photovault' ); ?></h2><p class="mt-2 max-w-2xl text-sm leading-6 text-gray-400"><?php esc_html_e( 'Suivez la confirmation de chaque demande et annulez une seance encore active si votre projet change.', 'photovault' ); ?></p></div><a class="inline-flex justify-center rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black" href="<?php echo esc_url( home_url( '/booking/' ) ); ?>"><?php esc_html_e( 'Nouvelle reservation', 'photovault' ); ?></a></div>
					<?php if ( isset( $_GET['booking'] ) && 'success' === sanitize_key( wp_unslash( $_GET['booking'] ) ) ) : ?><div class="mb-6 border-l-4 border-emerald-400 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100"><?php esc_html_e( 'Votre demande a ete transmise. Un e-mail de confirmation vient de vous etre envoye.', 'photovault' ); ?></div><?php endif; ?>
					<?php if ( isset( $_GET['shooting'] ) ) : $shooting_notice = sanitize_key( wp_unslash( $_GET['shooting'] ) ); ?><div class="mb-6 border-l-4 <?php echo 'updated' === $shooting_notice ? 'border-emerald-400 bg-emerald-400/10 text-emerald-100' : 'border-red-400 bg-red-400/10 text-red-100'; ?> px-5 py-4 text-sm" role="status"><?php echo esc_html( 'updated' === $shooting_notice ? __( 'La reservation a ete annulee et le studio a ete informe.', 'photovault' ) : __( 'La reservation n a pas pu etre modifiee.', 'photovault' ) ); ?></div><?php endif; ?>
					<div class="divide-y divide-white/10 border-y border-white/10">
						<?php if ( ! $shootings ) : ?><div class="py-14 text-center"><h3 class="text-xl font-bold"><?php esc_html_e( 'Aucune reservation pour le moment', 'photovault' ); ?></h3><p class="mt-3 text-gray-400"><?php esc_html_e( 'Portrait, famille, evenement ou projet artistique: decrivez votre intention pour commencer.', 'photovault' ); ?></p></div><?php endif; ?>
						<?php foreach ( $shootings as $shooting ) : ?><article class="grid gap-5 py-6 lg:grid-cols-[1.2fr,1fr,auto] lg:items-center"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-amber-300"><?php echo esc_html( $shooting_types[ $shooting['type'] ] ?? $shooting['type'] ); ?></p><h3 class="mt-2 text-lg font-bold"><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $shooting['desired_date'] ) ) ); ?></h3><p class="mt-1 text-sm text-gray-400"><?php echo esc_html( $shooting['location'] ); ?></p></div><div><span class="inline-flex border border-white/15 px-3 py-1 text-xs font-bold uppercase text-gray-200"><?php echo esc_html( $shooting_statuses[ $shooting['status'] ] ?? $shooting['status'] ); ?></span><p class="mt-3 line-clamp-2 text-sm leading-6 text-gray-500"><?php echo esc_html( $shooting['message'] ); ?></p></div><div><?php if ( in_array( $shooting['status'], array( 'pending', 'confirmed' ), true ) ) : ?><form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="photovault_shooting_transition"><input type="hidden" name="shooting_id" value="<?php echo esc_attr( $shooting['id'] ); ?>"><input type="hidden" name="shooting_status" value="cancelled"><?php wp_nonce_field( 'photovault_shooting_transition_' . $shooting['id'], 'photovault_shooting_nonce' ); ?><button class="text-sm font-bold text-red-300 underline decoration-red-300/40 underline-offset-4" type="submit"><?php esc_html_e( 'Annuler', 'photovault' ); ?></button></form><?php endif; ?></div></article><?php endforeach; ?>
					</div>
				</section>
			<?php elseif ( 'newsletter' === $section ) : ?>
				<section class="grid gap-10 lg:grid-cols-[1fr,1.2fr]">
					<div><p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500"><?php esc_html_e( 'Abonnement', 'photovault' ); ?></p><h2 class="mt-2 text-2xl font-bold"><?php echo esc_html( $subscriber && 'subscribed' === $subscriber['status'] ? __( 'Vous recevez les carnets PhotoVault', 'photovault' ) : ( $subscriber && 'pending' === $subscriber['status'] ? __( 'Confirmez votre adresse e-mail', 'photovault' ) : __( 'Restez au courant des nouvelles archives', 'photovault' ) ) ); ?></h2><p class="mt-4 text-sm leading-7 text-gray-400"><?php esc_html_e( 'Les preferences thematiques permettent de recevoir uniquement les lettres correspondant aux sujets choisis.', 'photovault' ); ?></p></div>
					<div class="border-y border-white/10 py-6">
						<?php if ( $subscriber && 'subscribed' === $subscriber['status'] ) : ?>
							<dl class="space-y-4"><div class="flex justify-between gap-4"><dt class="text-gray-500"><?php esc_html_e( 'Adresse', 'photovault' ); ?></dt><dd class="font-semibold"><?php echo esc_html( $current_user->user_email ); ?></dd></div><div class="flex justify-between gap-4"><dt class="text-gray-500"><?php esc_html_e( 'Statut', 'photovault' ); ?></dt><dd class="font-bold text-emerald-300"><?php esc_html_e( 'Abonne', 'photovault' ); ?></dd></div></dl>
							<?php if ( function_exists( 'newsletter_campaign_kit_get_preferences_url' ) ) : ?><a class="mt-6 inline-flex rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black" href="<?php echo esc_url( newsletter_campaign_kit_get_preferences_url( $subscriber['unsubscribe_token'] ) ); ?>"><?php esc_html_e( 'Gerer mes thematiques', 'photovault' ); ?></a><?php endif; ?>
						<?php elseif ( function_exists( 'newsletter_campaign_kit_handle_subscribe' ) ) : ?>
							<?php if ( $subscriber && 'pending' === $subscriber['status'] ) : ?><p class="mb-5 border-l-4 border-amber-300 bg-amber-300/10 px-4 py-3 text-sm text-amber-100"><?php esc_html_e( 'Un lien de confirmation a ete envoye. Le formulaire ci-dessous permet de le renvoyer apres le delai de securite.', 'photovault' ); ?></p><?php endif; ?>
							<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="space-y-5"><input type="hidden" name="action" value="newsletter_campaign_kit_subscribe"><input type="hidden" name="newsletter_source" value="dashboard"><?php wp_nonce_field( 'newsletter_campaign_kit_subscribe', 'newsletter_campaign_kit_nonce' ); ?><label class="block text-sm font-semibold" for="dashboard-newsletter-email"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></label><input id="dashboard-newsletter-email" class="w-full rounded-md border border-white/15 bg-black/30 px-4 py-3 text-gray-300" name="newsletter_email" type="email" readonly value="<?php echo esc_attr( $current_user->user_email ); ?>"><label class="flex items-start gap-3 text-sm text-gray-400"><input class="mt-1" type="checkbox" name="newsletter_consent" value="1" required><span><?php esc_html_e( 'J accepte de recevoir les lettres editoriales et je pourrai me desinscrire a tout moment.', 'photovault' ); ?></span></label><button class="rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black" type="submit"><?php echo esc_html( $subscriber && 'pending' === $subscriber['status'] ? __( 'Renvoyer la confirmation', 'photovault' ) : __( 'S inscrire', 'photovault' ) ); ?></button></form>
						<?php else : ?><p class="text-gray-400"><?php esc_html_e( 'Le service newsletter est actuellement indisponible.', 'photovault' ); ?></p><?php endif; ?>
					</div>
				</section>
			<?php elseif ( 'security' === $section ) : ?>
				<section class="grid gap-8 lg:grid-cols-3">
					<div class="border-t border-white/10 pt-5"><p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500"><?php esc_html_e( 'Adresse e-mail', 'photovault' ); ?></p><h2 class="mt-3 text-xl font-bold"><?php echo esc_html( $email_verified ? __( 'Identite verifiee', 'photovault' ) : __( 'Verification requise', 'photovault' ) ); ?></h2><p class="mt-3 break-all text-sm text-gray-400"><?php echo esc_html( $current_user->user_email ); ?></p></div>
					<div class="border-t border-white/10 pt-5"><p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500"><?php esc_html_e( 'Telephone', 'photovault' ); ?></p><h2 class="mt-3 text-xl font-bold"><?php echo esc_html( $phone_verified ? __( 'Numero verifie', 'photovault' ) : __( 'A verifier', 'photovault' ) ); ?></h2><p class="mt-3 text-sm text-gray-400"><?php echo esc_html( $phone ?: __( 'Aucun numero international', 'photovault' ) ); ?></p></div>
					<div class="border-t border-white/10 pt-5"><p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-500"><?php esc_html_e( 'Double authentification', 'photovault' ); ?></p><h2 class="mt-3 text-xl font-bold"><?php echo esc_html( $mfa_methods ? implode( ', ', array_map( 'strtoupper', $mfa_methods ) ) : __( 'Aucun facteur actif', 'photovault' ) ); ?></h2><?php if ( $mfa_required && ! $mfa_methods && $mfa_deadline ) : ?><p class="mt-3 text-sm text-amber-200"><?php echo esc_html( sprintf( __( 'Activation obligatoire avant le %s.', 'photovault' ), wp_date( get_option( 'date_format' ), $mfa_deadline ) ) ); ?></p><?php else : ?><p class="mt-3 text-sm text-gray-400"><?php esc_html_e( 'E-mail, SMS, application TOTP et codes de secours sont pris en charge.', 'photovault' ); ?></p><?php endif; ?></div>
				</section>
				<div class="mt-10 border-y border-white/10 py-6"><h2 class="text-2xl font-bold"><?php esc_html_e( 'Gerer vos facteurs et vos informations', 'photovault' ); ?></h2><p class="mt-3 max-w-2xl text-sm leading-7 text-gray-400"><?php esc_html_e( 'Le profil centralise le changement d e-mail, le telephone international, le mot de passe et le cycle de vie MFA re-authentifie.', 'photovault' ); ?></p><a class="mt-5 inline-flex rounded-md bg-amber-300 px-5 py-3 text-sm font-black text-black" href="<?php echo esc_url( home_url( '/profile/' ) ); ?>"><?php esc_html_e( 'Ouvrir les reglages de securite', 'photovault' ); ?></a></div>
			<?php elseif ( 'analytics' === $section && $is_manager ) : ?>
				<?php $stats = photovault_get_photographer_stats( 0 ); ?>
				<section class="grid gap-px overflow-hidden rounded-md border border-white/10 bg-white/10 sm:grid-cols-2 xl:grid-cols-4">
					<?php foreach ( array( 'total' => __( 'Medias', 'photovault' ), 'public' => __( 'Publics', 'photovault' ), 'private' => __( 'Prives', 'photovault' ), 'protected' => __( 'Proteges', 'photovault' ), 'folders' => __( 'Collections', 'photovault' ), 'categories' => __( 'Categories', 'photovault' ), 'views' => __( 'Vues', 'photovault' ), 'downloads' => __( 'Telechargements', 'photovault' ) ) as $key => $label ) : ?><div class="bg-[#151412] px-6 py-6"><span class="text-xs font-bold uppercase tracking-[0.16em] text-gray-500"><?php echo esc_html( $label ); ?></span><strong class="mt-3 block text-3xl font-black"><?php echo esc_html( number_format_i18n( $stats[ $key ] ) ); ?></strong></div><?php endforeach; ?>
				</section>
				<section class="mt-12"><div class="mb-5 flex items-end justify-between"><h2 class="text-2xl font-bold"><?php esc_html_e( 'Derniers medias importes', 'photovault' ); ?></h2><a class="text-sm font-bold text-amber-200" href="<?php echo esc_url( admin_url( 'edit.php?post_type=media_item' ) ); ?>"><?php esc_html_e( 'Gerer dans WordPress', 'photovault' ); ?></a></div><?php $recent_media_query = new WP_Query( array( 'post_type' => 'media_item', 'post_status' => array( 'publish', 'private' ), 'posts_per_page' => 4 ) ); ?><div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4"><?php while ( $recent_media_query->have_posts() ) : $recent_media_query->the_post(); get_template_part( 'templates/media-card' ); endwhile; wp_reset_postdata(); ?></div></section>
			<?php endif; ?>
		</div>
	</main>
</div>

<script>
document.addEventListener('photovault:favorite-changed', function(event) {
	if (!event.detail || event.detail.favorite) return;
	const card = document.querySelector('[data-pv-favorite-card="' + event.detail.mediaId + '"]');
	if (card) card.remove();
});
</script>

<?php get_footer(); ?>
