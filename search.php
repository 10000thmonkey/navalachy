<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package navalachy
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'navalachy' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h1>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

						<?php if ( 'post' === get_post_type() ) : ?>
						<div class="entry-meta">
							<?php
							navalachy_posted_on();
							navalachy_posted_by();
							?>
						</div><!-- .entry-meta -->
						<?php endif; ?>
					</header><!-- .entry-header -->

					<?php navalachy_post_thumbnail(); ?>

					<div class="entry-summary">
						<?php the_excerpt(); ?>
					</div><!-- .entry-summary -->

					<footer class="entry-footer">
						<?php navalachy_entry_footer(); ?>
					</footer><!-- .entry-footer -->
				</article><!-- #post-<?php the_ID(); ?> -->

			<?php

			endwhile;

			the_posts_navigation();

		else :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'navalachy' ); ?></p>
			
			<?php

			get_search_form();

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
