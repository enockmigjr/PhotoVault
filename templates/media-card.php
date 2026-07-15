<?php
/**
 * Editorial media preview used by archives and related-work sections.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$media_id       = get_the_ID();
$is_protected   = '1' === get_post_meta( $media_id, 'is_protected', true );
$is_private     = 'private' === get_post_status( $media_id );
$is_admin       = current_user_can( 'manage_options' );
$author_name    = get_the_author();
$card_url       = photovault_get_secure_image_url( $media_id, 'card' );
$preview_url    = photovault_get_secure_image_url( $media_id, 'preview' );
$is_favorite    = is_user_logged_in() && function_exists( 'photovault_is_media_favorite' ) ? photovault_is_media_favorite( $media_id ) : false;
$attachment_id  = get_post_thumbnail_id( $media_id );
$metadata       = $attachment_id ? wp_get_attachment_metadata( $attachment_id ) : array();
$image_width    = ! empty( $metadata['width'] ) ? absint( $metadata['width'] ) : 4;
$image_height   = ! empty( $metadata['height'] ) ? absint( $metadata['height'] ) : 3;
$image_ratio    = $image_height > 0 ? min( 2.2, max( 0.55, $image_width / $image_height ) ) : 1.3333;
$terms          = get_the_terms( $media_id, 'media_category' );
$category_label = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'Photographie', 'photovault' );
?>

<article class="pv-media-card group mb-8 break-inside-avoid" data-pv-lightbox-item data-media-id="<?php echo esc_attr( $media_id ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>" data-author="<?php echo esc_attr( $author_name ); ?>" data-meta="<?php echo esc_attr( $category_label . ' / ' . get_the_date( 'Y' ) ); ?>" data-preview-url="<?php echo esc_url( $preview_url ); ?>" data-detail-url="<?php echo esc_url( get_permalink() ); ?>" data-protected="<?php echo $is_protected ? '1' : '0'; ?>">
	<div class="relative overflow-hidden bg-[#171614]" style="aspect-ratio:<?php echo esc_attr( number_format( $image_ratio, 4, '.', '' ) ); ?>">
		<?php if ( $card_url ) : ?>
			<img class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.025]" src="<?php echo esc_url( $card_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" width="400" height="<?php echo esc_attr( max( 1, (int) round( 400 / $image_ratio ) ) ); ?>" draggable="false">
		<?php else : ?>
			<div class="flex h-full w-full items-center justify-center border border-white/10 text-gray-600"><svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="1.5" d="M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
		<?php endif; ?>

		<button class="absolute inset-0 z-10 cursor-zoom-in" type="button" data-pv-lightbox-open <?php echo ! $is_admin ? 'data-pv-protection-guard' : ''; ?> data-pv-message="<?php esc_attr_e( 'Cette miniature ouvre la visionneuse PhotoVault. Le fichier original reste protege.', 'photovault' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Voir %s en grand', 'photovault' ), get_the_title() ) ); ?>"></button>

		<div class="pointer-events-none absolute inset-x-0 bottom-0 z-20 flex items-end justify-between gap-4 bg-gradient-to-t from-black/75 to-transparent px-4 pb-4 pt-12 opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100">
			<span class="text-xs font-bold uppercase text-white"><?php esc_html_e( 'Voir l oeuvre', 'photovault' ); ?></span>
			<?php if ( $is_protected || $is_private ) : ?><span class="border border-white/30 bg-black/45 px-2 py-1 text-[10px] font-bold uppercase text-white"><?php echo esc_html( $is_private ? __( 'Privee', 'photovault' ) : __( 'Protegee', 'photovault' ) ); ?></span><?php endif; ?>
		</div>

		<?php if ( is_user_logged_in() && function_exists( 'photovault_is_media_favorite' ) ) : ?>
			<button class="pv-favorite-button absolute left-3 top-3 z-30 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-black/65 text-white transition hover:border-amber-200 hover:text-amber-200" type="button" data-pv-favorite data-media-id="<?php echo esc_attr( $media_id ); ?>" aria-pressed="<?php echo $is_favorite ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $is_favorite ? __( 'Retirer des favoris', 'photovault' ) : __( 'Ajouter aux favoris', 'photovault' ) ); ?>" title="<?php echo esc_attr( $is_favorite ? __( 'Retirer des favoris', 'photovault' ) : __( 'Ajouter aux favoris', 'photovault' ) ); ?>"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="<?php echo $is_favorite ? 'currentColor' : 'none'; ?>" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 4.6a5.5 5.5 0 00-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 00-7.8 7.8l8.9 8.8 8.8-8.8a5.5 5.5 0 000-7.8z"/></svg></button>
		<?php endif; ?>
	</div>
	<div class="flex items-start justify-between gap-5 pt-3">
		<div class="min-w-0"><h2 class="truncate text-base font-bold text-white"><?php the_title(); ?></h2><p class="mt-1 text-xs text-gray-500"><?php echo esc_html( $category_label ); ?> / <?php echo esc_html( get_the_date( 'Y' ) ); ?></p></div>
		<a class="relative z-30 shrink-0 text-xs font-bold text-amber-200 hover:text-white" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Details', 'photovault' ); ?></a>
	</div>
</article>
