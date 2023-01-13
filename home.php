<?php
/* Template Name: NV/Blog/Archive */
get_header();
?>

<main id="primary" class="site-main">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_ID() ),
		"heading" => "Coming soon..."
	) );
	?>

</main><!-- #main -->

<?php
get_footer();
