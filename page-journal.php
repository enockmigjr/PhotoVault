<?php
/**
 * Template Name: Carnets visuels
 *
 * @package PhotoVault
 */

get_header();

$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 9,
		'paged'               => $paged,
		'ignore_sticky_posts' => false,
	)
);

get_template_part( 'templates/post-listing', null, array( 'query' => $posts ) );
wp_reset_postdata();
get_footer();
