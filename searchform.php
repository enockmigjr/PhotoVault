<?php
/**
 * Formulaire de recherche personnalisé (searchform.php).
 *
 * @package PhotoVault
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="relative" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" class="w-full pl-10 pr-4 py-3 border border-gray-800 rounded-xl bg-gray-900/50 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Rechercher sur le site..." value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="absolute left-3 top-3.5 text-gray-500 hover:text-white transition-colors cursor-pointer">
		<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
	</button>
</form>
