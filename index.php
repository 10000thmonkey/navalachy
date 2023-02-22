<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package navalachy
 */

get_header();


$post_type = get_post_type();

if ( file_exists( __DIR__."/$post_type/functions.php" ) )
	include_once __DIR__."/$post_type/functions.php";




echo $post_type;

if ( is_archive() && file_exists( __DIR__."/$post_type/archive.php" ) )
{
	include_once __DIR__."/$post_type/archive.php";
}
else if ( is_single() && file_exists( __DIR__."/$post_type/single.php" ) )
{
	include_once __DIR__."/$post_type/single.php";
}
else if (
    is_page()
    && file_exists( __DIR__."/page/page-{$post->post_name}" )
    && ! is_woocommerce_page()
)
{
	include_once __DIR__."/page/page-{$post->post_name}";
}
else {
	add_action("qm/debug", $post_type);
    //add_action("qm/debug", $post->post_name );

    the_content();
}



get_footer();