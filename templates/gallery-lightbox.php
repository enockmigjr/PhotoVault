<?php
/**
 * Shared fullscreen gallery viewer.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<dialog id="pv-gallery-lightbox" class="pv-gallery-lightbox" aria-labelledby="pv-lightbox-title">
	<div class="pv-gallery-lightbox__stage">
		<div class="pv-gallery-lightbox__topbar">
			<span data-pv-lightbox-count aria-live="polite"></span>
			<div class="flex items-center gap-2">
				<button type="button" data-pv-lightbox-fullscreen title="<?php esc_attr_e( 'Plein écran', 'photovault' ); ?>" aria-label="<?php esc_attr_e( 'Afficher en plein écran', 'photovault' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M8 3H3v5M16 3h5v5M8 21H3v-5M16 21h5v-5" /></svg></button>
				<button type="button" data-pv-lightbox-close title="<?php esc_attr_e( 'Fermer', 'photovault' ); ?>" aria-label="<?php esc_attr_e( 'Fermer la visionneuse', 'photovault' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m5 5 14 14M19 5 5 19" /></svg></button>
			</div>
		</div>
		<button class="pv-gallery-lightbox__nav pv-gallery-lightbox__nav--prev" type="button" data-pv-lightbox-prev aria-label="<?php esc_attr_e( 'Œuvre précédente', 'photovault' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m15 18-6-6 6-6" /></svg></button>
		<figure class="pv-gallery-lightbox__figure">
			<div class="pv-gallery-lightbox__image-wrap" data-pv-protection-guard data-pv-message="<?php esc_attr_e( 'Cet aperçu est protégé. Utilisez les options prévues sur la fiche de l’œuvre.', 'photovault' ); ?>"><img data-pv-lightbox-image alt="" draggable="false"></div>
			<figcaption><div><h2 id="pv-lightbox-title" data-pv-lightbox-title></h2><p data-pv-lightbox-meta></p></div><a data-pv-lightbox-detail href="#"><?php esc_html_e( 'Voir la fiche', 'photovault' ); ?></a></figcaption>
		</figure>
		<button class="pv-gallery-lightbox__nav pv-gallery-lightbox__nav--next" type="button" data-pv-lightbox-next aria-label="<?php esc_attr_e( 'Œuvre suivante', 'photovault' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" /></svg></button>
	</div>
</dialog>
