<?php
/**
 * Default editorial archive.
 *
 * @package PhotoVault
 */

$archive_title = wp_strip_all_tags( get_the_archive_title() );
$archive_copy  = wp_strip_all_tags( get_the_archive_description() );

get_header();
get_template_part(
	'templates/post-listing',
	null,
	array(
		'query'       => $GLOBALS['wp_query'],
		'eyebrow'     => __( 'Journal / Archives', 'photovault' ),
		'title'       => $archive_title,
		'copy'        => $archive_copy ?: __( 'Une sélection chronologique de publications et de notes visuelles.', 'photovault' ),
		'empty_title' => __( 'Aucune publication dans cette archive.', 'photovault' ),
	)
);
get_footer();
