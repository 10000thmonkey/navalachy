<?php
get_header();

   $order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
    
    //Get an instance of the WC_Order object
   $order = wc_get_order( $order_id );
   
   echo "<pre>".var_dump($order)."</pre>";
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
