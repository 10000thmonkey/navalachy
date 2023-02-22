<?php


add_action(
	'template_redirect', 
	function ()
	{
	    
	    // do nothing if we are not on the order received page
	    if( ! is_wc_endpoint_url( 'order-received' ) || empty( $_GET[ 'key' ] ) ) {
	        return; 
	    }
	    include_once get_template_directory() . "/accomodation/i/lib.php";
	    include_once get_template_directory() . "/accomodation/email.php";

	    $nvbk = new NVBK();


	    $order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
	    $order = wc_get_order( $order_id );
	    $order_meta = get_post_meta( $order_id );
	   
	    $nvbk_meta = json_decode( $order_meta["nvbk_meta"][0], true );
	    $apartment_meta = get_post_meta( $nvbk_meta["apartmentId"] );
	    $host_meta = get_user_meta( $apartment_meta["host"][0] );


	    if ( ! $nvbk_meta["booking_confirmed"] )
	    {
		    $nvbk->confirm_booking( $nvbk_meta["booking_id"][0], $order_id, $order, $order_meta );

		    $mail = nv_send_mail (array(
				"to" => $order_meta["_billing_email"][0], 
				"subject" => "Rezervace přijata - NaValachy.cz",
				"body" => nvbk_email_order_complete( [ 
					"order" => $order_meta,
					"order_id" => $order_id,
					"nvbk" => $nvbk_meta,
					"apartment" => $apartment_meta,
					"host" => $host_meta
				] ),
				"headers" => array(
					"From: info@navalachy.cz",
					'Content-Type: text/html; charset=UTF-8'
				)
			));

		    $nvbk_meta["booking_confirmed"] = true;
			update_post_meta( $order_id, "nvbk_meta", json_encode( $nvbk_meta ) );
		}

	    //wp_safe_redirect( get_site_url()."/thankyou?mail=".$order_meta["_billing_email"][0]."&key=" . $_GET['key'] );
	}
);




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
