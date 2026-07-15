<?php
/**
 * Default content index.
 *
 * @package PhotoVault
 */

get_header();
get_template_part( 'templates/post-listing', null, array( 'query' => $GLOBALS['wp_query'], 'eyebrow' => __( 'PhotoVault / Publications', 'photovault' ), 'title' => __( 'Histoires, notes et nouvelles des archives.', 'photovault' ) ) );
get_footer();
