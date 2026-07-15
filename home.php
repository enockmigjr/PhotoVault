<?php
/**
 * Posts index.
 *
 * @package PhotoVault
 */

get_header();
get_template_part( 'templates/post-listing', null, array( 'query' => $GLOBALS['wp_query'] ) );
get_footer();
