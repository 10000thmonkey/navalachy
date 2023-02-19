<?php

function nvbk_ajax_get_disabled_dates ()
{
	include_once("lib.php");
	header("Content-Type: application/json; charset=UTF-8");

	$apartmentId = $_POST["apartmentId"];

	echo json_encode( $nvbk->get_disabled_dates( (int)$apartmentId ) );
	die();
}

add_action("wp_ajax_nvbk_get_disabled_dates", "nvbk_ajax_get_disabled_dates");
add_action("wp_ajax_nopriv_nvbk_get_disabled_dates", "nvbk_ajax_get_disabled_dates");




function nvbk_ajax_accomodation_contact_form ()
{
	if(WP_DEBUG) @ini_set( 'display_errors', 1 );
	$from = $_POST["name"] ? $_POST['name'] : "";
	$headers = 
	
	$res = nv_send_mail (array(
		"to" => $_POST["host_email"], 
		"subject" => "Nový dotaz z Valach od " . $_POST['name'] . " (" . $_POST['email'] . ")",
		"body" => $_POST['message'],
		"headers" => array(
			"From: Na Valachy kontaktní formulář <info@navalachy.cz>",
			'Content-Type: text/html; charset=UTF-8',
			'Reply-To: '.$_POST["name"].' <'.$_POST['email'].'>',
		)
	));	

	$res = nv_send_mail (array(
		"to" => $_POST["email"], 
		"subject" => "NaValachy.cz: Dotaz jsme majiteli odeslali.",
		"body" => "Váš dotaz na e-mail ".$_POST["host_email"]." byl úspěšně zaslán.",
		"headers" => array(
			"From: NaValachy.cz <info@navalachy.cz>",
			'Content-Type: text/html; charset=UTF-8'
		)
	));
	//echo debug_wpmail($res);
	wp_die();
}
add_action("wp_ajax_nvbk_accomodation_contact_form", "nvbk_ajax_accomodation_contact_form");
add_action("wp_ajax_nopriv_nvbk_accomodation_contact_form", "nvbk_ajax_accomodation_contact_form");





function nvbk_ajax_to_checkout ()
{
	if(WP_DEBUG) @ini_set( 'display_errors', 1 );
	include_once("lib.php");

	$nvbk = new NVBK();
	$cart = WC()->cart;
	$cart_cache = DAY_IN_SECONDS * 2;

	$return = array(
		"success" => false,
		"body" => "",
	);


	//CHECK IF DATE IS AVAILABLE, IF NOT, RETURN ERROR IN MESSAGE BODY
	if ( ! $nvbk->is_available( $_POST['apartmentId'], $_POST['begin'], $_POST['end'] ) )
	{
		$return["body"] = "Termín je obsazený.";
		
		echo json_encode($return);
		wp_die();
	}


	//GET PRICE AND SEND IN MESSAGE BODY
	$price = $nvbk->get_new_booking_price( $_POST['apartmentId'], $_POST['begin'], $_POST['end'] );
	

	//IF OK, SEND PRICE TO MESSAGE BODY
	if ( isset($_POST['preCheckout']) && $_POST['preCheckout'] == "yes" )
	{	
		$return["price"] = $price;
		$return["success"] = true;
	}
	else
	{
		$booking_id = $nvbk->insert_booking ( (int)$_POST['apartmentId'], $_POST["begin"], $_POST["end"] );

		$nv_booking_meta = array(
			"apartmentId" => $_POST["apartmentId"],
			"apartmentName" => $_POST["apartmentName"],
			"begin" => $_POST["begin"],
			"end" => $_POST["end"],
			"price" => $price["price_final"],
			"adults" => $_POST["adults"],
			"kids" => $_POST["kids"],
			"booking_id" => (int)$booking_id,
			"booking_confirmed" => false
		);

		if ( !$cart->is_empty() ) $cart->empty_cart();
	
		$cart->add_to_cart( 1084, 1, NULL, NULL, [ "nvbk_meta" => json_encode( $nv_booking_meta ) ] );
		$cart->calculate_totals();
		WC()->session->set('cart', $cart->cart_content);
		$cart->set_session();
		$cart->maybe_set_cart_cookies();

		$return["success"] = true;
	}
	
	echo json_encode($return);
	wp_die ();
}
add_action("wp_ajax_nvbk_to_checkout", "nvbk_ajax_to_checkout");
add_action("wp_ajax_nopriv_nvbk_to_checkout", "nvbk_ajax_to_checkout");






function nv_order_received_redirect()
{
    
    // do nothing if we are not on the order received page
    if( ! is_wc_endpoint_url( 'order-received' ) || empty( $_GET[ 'key' ] ) ) {
        return; 
    }
    include_once "lib.php";
    include_once "email.php";

    $nvbk = new NVBK();


    $order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
    $order = wc_get_order( $order_id );
    $order_meta = get_post_meta( $order_id );
    $order_meta["_order_id"] = $order_id;
   
    $apartment_meta = get_post_meta( $order_meta["nvbk_booking_apartmentId"][0] );
    $host_meta = get_user_meta( $apartment_meta["host"][0] );

    $nvbk_meta = json_decode( $order_meta["nvbk_meta"][0], true );

    if ( ! $nvbk_meta["booking_confirmed"] )
    {
	    $nvbk->confirm_booking( $order_meta["nvbk_booking_id"][0], $order_id, $order, $order_meta );

	    $mail = nv_send_mail (array(
			"to" => $order_meta["_billing_email"][0], 
			"subject" => "Rezervace přijata - NaValachy.cz",
			"body" => nvbk_email_order_complete( [ 
				"order" => $order_meta,
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
add_action( 'template_redirect', 'nv_order_received_redirect');




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
