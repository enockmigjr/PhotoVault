<?php
/**
 * Media detail template.
 *
 * @package PhotoVault
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$media_id             = get_the_ID();
		$author_id            = (int) get_the_author_meta( 'ID' );
		$current_user_id      = get_current_user_id();
		$is_private           = 'private' === get_post_status( $media_id );
		$is_admin             = current_user_can( 'manage_options' );
		$is_owner             = $current_user_id && $current_user_id === $author_id;
		$is_protected         = '1' === get_post_meta( $media_id, 'is_protected', true );
		$has_verified_identity = function_exists( 'photovault_user_has_verified_identity' ) ? photovault_user_has_verified_identity( $current_user_id ) : true;
		$can_access_media     = function_exists( 'photovault_user_can_access_media' ) ? photovault_user_can_access_media( $media_id, $current_user_id ) : ( ! $is_private || $is_admin || $is_owner );

		if ( $is_private && ! $can_access_media ) {
			if ( function_exists( 'photovault_log_media_event' ) ) {
				photovault_log_media_event( 'access_denied', 'warning', $media_id, array( 'reason' => $has_verified_identity ? 'private_detail_view' : 'email_unverified_detail_view' ) );
			}
			?>
			<main class="min-h-[78vh] bg-[#0d0c0b] text-gray-100">
				<section class="mx-auto flex min-h-[78vh] max-w-4xl flex-col justify-center px-5 py-20 sm:px-8" aria-labelledby="media-access-title">
					<p class="text-xs font-extrabold uppercase text-amber-200"><?php echo esc_html( $has_verified_identity ? __( 'Archive confidentielle', 'photovault' ) : __( 'Identité à confirmer', 'photovault' ) ); ?></p>
					<h1 id="media-access-title" class="mt-5 font-serif text-4xl leading-tight text-white sm:text-5xl"><?php echo esc_html( $has_verified_identity ? __( 'Cette œuvre reste hors du regard public.', 'photovault' ) : __( 'Confirmez votre e-mail avant d’ouvrir cette archive.', 'photovault' ) ); ?></h1>
					<p class="mt-6 max-w-2xl text-base leading-8 text-gray-400"><?php echo esc_html( $has_verified_identity ? __( 'L’accès à ce média est limité à son propriétaire, aux personnes autorisées et aux administrateurs PhotoVault.', 'photovault' ) : __( 'La confirmation protège les collections privées et relie chaque consultation à une identité vérifiée.', 'photovault' ) ); ?></p>
					<div class="mt-9 flex flex-wrap gap-3"><a class="pv-header-cta min-h-12 px-6" href="<?php echo esc_url( $has_verified_identity ? home_url( '/contact/' ) : home_url( '/profile/' ) ); ?>"><?php echo esc_html( $has_verified_identity ? __( 'Demander un accès', 'photovault' ) : __( 'Ouvrir mon profil', 'photovault' ) ); ?></a><a class="inline-flex min-h-12 items-center border border-white/15 px-6 text-sm font-bold hover:border-amber-200/60" href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>"><?php esc_html_e( 'Retour à la galerie', 'photovault' ); ?></a></div>
				</section>
			</main>
			<?php
			get_footer();
			return;
		}

		$folders    = get_the_terms( $media_id, 'media_folder' );
		$categories = get_the_terms( $media_id, 'media_category' );
		$image_url  = photovault_get_secure_image_url( $media_id, 'preview' );
		$can_download = is_user_logged_in() && $has_verified_identity && ( ! $is_protected || $is_admin || $is_owner );
		$download_url = $can_download ? photovault_get_secure_image_url( $media_id, 'full', true ) : '';

		$previous_media = get_previous_post();
		$next_media     = get_next_post();
		if ( $previous_media && function_exists( 'photovault_user_can_access_media' ) && ! photovault_user_can_access_media( $previous_media->ID, $current_user_id ) ) {
			$previous_media = null;
		}
		if ( $next_media && function_exists( 'photovault_user_can_access_media' ) && ! photovault_user_can_access_media( $next_media->ID, $current_user_id ) ) {
			$next_media = null;
		}

		if ( function_exists( 'photovault_log_media_event' ) ) {
			photovault_log_media_event( 'media_view', 'info', $media_id, array( 'private' => $is_private, 'protected' => $is_protected, 'owner' => $is_owner, 'admin' => $is_admin ) );
		}
		if ( ! $is_admin ) {
			update_post_meta( $media_id, 'photovault_views_count', (int) get_post_meta( $media_id, 'photovault_views_count', true ) + 1 );
		}
		?>
		<main class="min-h-screen bg-[#0d0c0b] text-gray-100">
			<nav class="mx-auto flex max-w-[90rem] flex-wrap items-center justify-between gap-5 border-b border-white/10 px-5 py-5 text-xs font-bold text-gray-400 sm:px-8 lg:px-12" aria-label="<?php esc_attr_e( 'Navigation entre les œuvres', 'photovault' ); ?>">
				<a class="inline-flex min-h-11 items-center hover:text-white" href="<?php echo esc_url( get_post_type_archive_link( 'media_item' ) ); ?>"><span class="mr-2" aria-hidden="true">&larr;</span><?php esc_html_e( 'Toutes les œuvres', 'photovault' ); ?></a>
				<div class="flex items-center gap-5"><?php if ( $previous_media ) : ?><a class="inline-flex min-h-11 items-center hover:text-amber-200" href="<?php echo esc_url( get_permalink( $previous_media ) ); ?>"><?php esc_html_e( 'Précédente', 'photovault' ); ?></a><?php endif; ?><?php if ( $next_media ) : ?><a class="inline-flex min-h-11 items-center hover:text-amber-200" href="<?php echo esc_url( get_permalink( $next_media ) ); ?>"><?php esc_html_e( 'Suivante', 'photovault' ); ?></a><?php endif; ?></div>
			</nav>

			<article class="mx-auto grid max-w-[90rem] gap-12 px-5 py-10 sm:px-8 lg:grid-cols-12 lg:px-12 lg:py-16">
				<div id="media-grid" class="lg:col-span-8" data-pv-lightbox-scope>
					<figure class="group relative flex min-h-[55vh] items-center justify-center overflow-hidden rounded-md border border-white/10 bg-[#080807]" data-pv-lightbox-item data-title="<?php echo esc_attr( get_the_title() ); ?>" data-meta="<?php echo esc_attr( get_the_date( 'Y' ) . ' / ' . get_the_author() ); ?>" data-preview-url="<?php echo esc_url( $image_url ); ?>" data-detail-url="<?php echo esc_url( get_permalink() ); ?>">
						<?php if ( $image_url ) : ?><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="block max-h-[78vh] max-w-full select-none object-contain" draggable="false"><?php else : ?><div class="py-28 text-sm text-gray-600"><?php esc_html_e( 'Aucun aperçu disponible.', 'photovault' ); ?></div><?php endif; ?>
						<?php if ( $image_url ) : ?><button type="button" class="absolute inset-0 cursor-zoom-in" data-pv-lightbox-open <?php echo $is_protected && ! $is_admin ? 'data-pv-protection-guard' : ''; ?> data-pv-message="<?php esc_attr_e( 'Cet aperçu est protégé. Le fichier original reste hors du navigateur.', 'photovault' ); ?>" aria-label="<?php esc_attr_e( 'Afficher l’œuvre dans la visionneuse PhotoVault', 'photovault' ); ?>"></button><?php endif; ?>
						<?php if ( $is_protected && ! $is_admin ) : ?><div class="watermark font-extrabold" aria-hidden="true"><?php for ( $watermark_index = 0; $watermark_index < 30; $watermark_index++ ) : ?><span><?php echo esc_html( get_option( 'photovault_watermark_text', 'PHOTOVAULT' ) ); ?></span><?php endfor; ?></div><?php endif; ?>
					</figure>
					<p class="mt-4 text-xs leading-6 text-gray-500"><?php esc_html_e( 'Cliquez sur l’image pour l’ouvrir dans la visionneuse. L’aperçu reste distinct du fichier haute définition.', 'photovault' ); ?></p>
				</div>

				<aside class="pv-media-detail min-w-0 lg:col-span-4">
					<div class="flex flex-wrap gap-2 text-[0.65rem] font-extrabold uppercase"><span class="border border-white/15 px-2.5 py-1 text-gray-300"><?php echo esc_html( $is_private ? __( 'Accès privé', 'photovault' ) : __( 'Galerie publique', 'photovault' ) ); ?></span><?php if ( $is_protected ) : ?><span class="border border-amber-200/30 px-2.5 py-1 text-amber-200"><?php esc_html_e( 'Aperçu protégé', 'photovault' ); ?></span><?php endif; ?></div>
					<h1 class="mt-6 font-serif text-4xl leading-tight text-white sm:text-5xl"><?php the_title(); ?></h1>
					<p class="mt-4 text-xs font-semibold uppercase text-gray-500"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time> / <?php the_author(); ?></p>

					<?php if ( get_the_content() ) : ?><div class="pv-editorial-content mt-8 border-t border-white/10 pt-7 text-sm leading-7 text-gray-300"><?php the_content(); ?></div><?php endif; ?>

					<?php if ( ( $folders && ! is_wp_error( $folders ) ) || ( $categories && ! is_wp_error( $categories ) ) ) : ?><div class="mt-8 border-t border-white/10 pt-7"><p class="text-xs font-extrabold uppercase text-gray-500"><?php esc_html_e( 'Classification', 'photovault' ); ?></p><div class="mt-4 flex flex-wrap gap-2"><?php foreach ( array_merge( is_array( $folders ) ? $folders : array(), is_array( $categories ) ? $categories : array() ) as $term ) : ?><a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="border border-white/15 px-3 py-2 text-xs font-bold text-gray-300 hover:border-amber-200/50 hover:text-amber-100"><?php echo esc_html( $term->name ); ?></a><?php endforeach; ?></div></div><?php endif; ?>

					<div class="mt-8 border-t border-white/10 pt-7">
						<?php if ( $can_download ) : ?><a class="pv-header-cta min-h-12 w-full justify-center" href="<?php echo esc_url( $download_url ); ?>"><?php esc_html_e( 'Télécharger la haute définition', 'photovault' ); ?></a><p class="mt-3 text-xs leading-5 text-gray-500"><?php esc_html_e( 'Le fichier original sera demandé au serveur uniquement après cette action.', 'photovault' ); ?></p>
						<?php elseif ( ! is_user_logged_in() ) : ?><a class="pv-header-cta min-h-12 w-full justify-center" href="<?php echo esc_url( add_query_arg( 'redirect_to', get_permalink(), home_url( '/login/' ) ) ); ?>"><?php esc_html_e( 'Se connecter pour télécharger', 'photovault' ); ?></a>
						<?php elseif ( ! $has_verified_identity ) : ?><a class="pv-header-cta min-h-12 w-full justify-center" href="<?php echo esc_url( home_url( '/profile/' ) ); ?>"><?php esc_html_e( 'Vérifier mon identité', 'photovault' ); ?></a>
						<?php else : ?><div class="border-l-2 border-amber-200/50 pl-4"><p class="text-sm font-bold text-white"><?php esc_html_e( 'Original non téléchargeable', 'photovault' ); ?></p><p class="mt-2 text-xs leading-6 text-gray-400"><?php esc_html_e( 'Cette œuvre peut être consultée, mais son fichier haute définition reste réservé au propriétaire et aux administrateurs.', 'photovault' ); ?></p></div><?php endif; ?>
					</div>
				</aside>
			</article>

			<?php
			$related_query = new WP_Query(
				array(
					'post_type'      => 'media_item',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'post__not_in'   => array( $media_id ),
					'tax_query'      => $categories && ! is_wp_error( $categories ) ? array( array( 'taxonomy' => 'media_category', 'field' => 'term_id', 'terms' => wp_list_pluck( $categories, 'term_id' ) ) ) : array(),
				)
			);
			if ( $related_query->have_posts() ) :
				?>
				<section class="mx-auto max-w-[90rem] border-t border-white/10 px-5 py-16 sm:px-8 lg:px-12" aria-labelledby="related-media-title"><h2 id="related-media-title" class="font-serif text-3xl text-white"><?php esc_html_e( 'Œuvres similaires', 'photovault' ); ?></h2><div class="mt-9 grid gap-8 sm:grid-cols-2 lg:grid-cols-3" data-pv-lightbox-scope><?php while ( $related_query->have_posts() ) : $related_query->the_post(); get_template_part( 'templates/media-card' ); endwhile; wp_reset_postdata(); ?></div></section>
			<?php endif; ?>
		</main>
		<?php get_template_part( 'templates/gallery-lightbox' ); ?>
		<?php
	endwhile;
endif;

get_footer();
