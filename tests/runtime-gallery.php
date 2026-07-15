<?php
/**
 * Runtime verification for optimized gallery markup.
 *
 * Run with: wp eval-file wp-content/themes/PhotoVault/tests/runtime-gallery.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function photovault_gallery_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$attachment_id = 0;
$media_id      = 0;
$path          = '';

try {
	$upload = wp_upload_bits(
		'pv-gallery-runtime-' . wp_generate_password( 8, false, false ) . '.png',
		null,
		base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=' )
	);
	photovault_gallery_assert( empty( $upload['error'] ), 'Gallery fixture upload failed.' );
	$path          = $upload['file'];
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'Runtime gallery image',
			'post_status'    => 'inherit',
		),
		$path
	);
	$media_id = wp_insert_post(
		array(
			'post_type'   => 'media_item',
			'post_status' => 'publish',
			'post_title'  => 'Runtime gallery work',
		),
		true
	);
	photovault_gallery_assert( ! is_wp_error( $media_id ), 'Gallery media creation failed.' );
	set_post_thumbnail( $media_id, $attachment_id );
	update_post_meta( $media_id, 'is_protected', '1' );

	$GLOBALS['post'] = get_post( $media_id );
	setup_postdata( $GLOBALS['post'] );
	ob_start();
	get_template_part( 'templates/media-card' );
	$html = ob_get_clean();
	wp_reset_postdata();

	photovault_gallery_assert( false !== strpos( $html, 'display=card' ), 'Gallery card does not request the card variant.' );
	photovault_gallery_assert( false !== strpos( $html, 'data-preview-url=' ) && false !== strpos( $html, 'display=preview' ), 'Gallery viewer preview is not deferred in a data attribute.' );
	photovault_gallery_assert( false === strpos( $html, 'download=1' ), 'Gallery markup exposes an original download URL.' );
	photovault_gallery_assert( false === strpos( $html, 'srcset=' ), 'Gallery markup may let the browser select an original-sized source.' );

	echo wp_json_encode(
		array(
			'grid_variant'       => 'card',
			'lightbox_variant'   => 'preview_on_demand',
			'original_in_markup' => false,
			'protected_guard'    => true,
		)
	);
} finally {
	if ( $media_id && ! is_wp_error( $media_id ) ) {
		wp_delete_post( $media_id, true );
	}
	if ( $attachment_id ) {
		wp_delete_attachment( $attachment_id, true );
	} elseif ( $path && file_exists( $path ) ) {
		wp_delete_file( $path );
	}
}
