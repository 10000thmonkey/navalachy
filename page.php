<?php
get_header();
?>

	<main id="primary" class="contentwrap site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header>
					<?php /* the_title( '<h1 class="entry-title">', '</h1>' ); */?>
				</header><!-- .entry-header -->

				<?php //navalachy_post_thumbnail(); ?>

				<div>
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'navalachy' ),
							'after'  => '</div>',
						)
					);
					?>
				</div><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->

		<?php
		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
