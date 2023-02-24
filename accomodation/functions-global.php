<?php



// BOOKING CONFIRM

add_action(
	'template_redirect', 
	function ()
	{
	    
	    // do nothing if we are not on the order received page
	    if( ! is_wc_endpoint_url( 'order-received' ) || empty( $_GET[ 'key' ] ) ) {
	        return; 
	    }
	    include_once get_template_directory() . "/accomodation/i/lib.php";

	    $nvbk = new NVBK();


	    $order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
	    $order = wc_get_order( $order_id );
	    $order_meta = get_post_meta( $order_id );
	   
	    $nvbk_meta = json_decode( $order_meta["nvbk_meta"][0], true );
	    $apartment_meta = get_post_meta( $nvbk_meta["apartment_id"] );
	    $host_meta = get_user_meta( $apartment_meta["host"][0] );


	    //if ( ! $nvbk_meta["booking_confirmed"] )
	    //{
		    $nvbk->confirm_booking( $nvbk_meta["booking_id"][0], $order_id, $order, $order_meta );

		    $mail_body = nv_e( "accomodation/e/order-complete", [ 
				"order" => $order_meta,
				"order_id" => $order_id,
				"nvbk" => $nvbk_meta,
				"apartment" => $apartment_meta,
				"host" => $host_meta
			] );

		    $mail = nv_send_mail (array(
				"to" => $order_meta["_billing_email"][0], 
				"subject" => "Rezervace přijata - NaValachy.cz",
				"body" => $mail_body,
				"headers" => array(
					"From: info@navalachy.cz",
					'Content-Type: text/html; charset=UTF-8'
				)
			));

		    $nvbk_meta["booking_confirmed"] = true;
			update_post_meta( $order_id, "nvbk_meta", json_encode( $nvbk_meta ) );

			add_action( "nv_after_header", function() use ($mail_body) { echo $mail_body; } );
		//}

	    //wp_safe_redirect( get_site_url()."/thankyou?mail=".$order_meta["_billing_email"][0]."&key=" . $_GET['key'] );
	}
);



// ADD NVBK CART META TO ORDER META

add_action(
	'woocommerce_checkout_update_order_meta',
	function ( $order_id, $posted_data )
	{
	    $cart = WC()->cart;
		
		foreach ( $cart->get_cart() as $cart_item )
		{
			if ( ! empty( $cart_item["nvbk_meta"] ) )
				update_post_meta( $order_id, "nvbk_meta", $cart_item["nvbk_meta"] );
		} 
	}, 10, 2
);
