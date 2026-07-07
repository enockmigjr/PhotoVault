<?php
/**
 * Presentation-only helpers for the PhotoVault theme.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'photovault_render_post_visual' ) ) {
	function photovault_render_post_visual( $size = 'medium_large', $image_class = 'w-full h-full object-cover' ) {
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $size, array( 'class' => $image_class ) );
			return;
		}
		?>
		<div class="pv-post-placeholder w-full h-full flex items-center justify-center">
			<div class="pv-post-placeholder__mark" aria-hidden="true">
				<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4 7h3l2-2h6l2 2h3v12H4V7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 13a3 3 0 106 0 3 3 0 00-6 0z"></path></svg>
			</div>
			<div class="pv-post-placeholder__lines" aria-hidden="true"><span></span><span></span><span></span></div>
		</div>
		<?php
	}
}