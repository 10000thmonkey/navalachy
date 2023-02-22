<?php

$order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
 
 //Get an instance of the WC_Order object
$order = wc_get_order( $order_id );
$order_meta = get_post_meta( $order_id );

$nv_vars = [$order, $order_meta];

get_header();


?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			
		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
