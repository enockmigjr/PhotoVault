<?php
/**
 * Shared public search form.
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$search_id = wp_unique_id( 'pv-site-search-' );
?>
<form role="search" method="get" class="pv-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Rechercher sur le site', 'photovault' ); ?></label>
	<input id="<?php echo esc_attr( $search_id ); ?>" type="search" class="pv-search-form__input" placeholder="<?php esc_attr_e( 'Rechercher dans les archives…', 'photovault' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="pv-search-form__button" aria-label="<?php esc_attr_e( 'Lancer la recherche', 'photovault' ); ?>" title="<?php esc_attr_e( 'Rechercher', 'photovault' ); ?>"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-5.2-5.2m2.2-5.3a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" /></svg></button>
</form>
