<?php

nv_ajax(
	"accomodation/feed",
	function ()
	{
		return nv_c( "accomodation/c/feed",[
			"range" => [
				"begin" => ! empty( $_POST["begin"] ) ? $_POST["begin"] : null,
				"end" =>   ! empty( $_POST["end"] )   ? $_POST["end"] : null,
			]
		] );
	}
);




nv_ajax (
	"accomodation/init-form",
	function()
	{
		include_once( get_template_directory() . "/accomodation/i/lib.php");
		//header("Content-Type: application/json; charset=UTF-8");

		$id = $_POST["apartment_id"];
		$post = get_post( $id );
		$meta = get_post_meta( $id );

		return [
			"apartment_id" => $id,
			"apartment_name" => $post->post_title,
			"capacity" => $meta["capacity"][0],
			"disabled_dates" => $nvbk->get_disabled_dates( $id )
		];
	}
);


nv_ajax (
	"accomodation/form-contact",
	function ()
	{
		if(WP_DEBUG) @ini_set( 'display_errors', 1 );
		$from = $_POST["name"] ? $_POST['name'] : "";
		$headers = 
		
		$res = nv_send_mail (array(
			"to" => $_POST["host_email"], 
			"subject" => "Dotaz na ".$_POST["name"]." od " . $_POST['name'] . " (" . $_POST['email'] . ")",
			"body" => $_POST['message'],
			"headers" => array(
				"From: NaValachy ubytování - dotaz <info@navalachy.cz>",
				'Content-Type: text/html; charset=UTF-8',
				'Reply-To: '.$_POST["name"].' <'.$_POST['email'].'>',
			)
		));	

		$res2 = nv_send_mail (array(
			"to" => $_POST["email"], 
			"subject" => "NaValachy: Dotaz na ".$_POST["name"]." jsme odeslali majiteli",
			"body" => "Váš dotaz na ".$_POST["name"]." byl úspěšně zaslán majiteli ".$_POST["host_name"]." na e-mail ".$_POST["host_email"].". Co nevidět se vám ozve.",
			"headers" => array(
				"From: NaValachy.cz <info@navalachy.cz>",
				'Content-Type: text/html; charset=UTF-8'
			)
		));

		return [
			"debug" => [
				"res1" => $res1,
				"res2" => $res2
			]
		];
	}
);




nv_ajax(
	"accomodation/to-checkout",
	function ()
	{
		include_once( get_template_directory() . "/accomodation/i/lib.php" );

		$nvbk = new NVBK();
		$cart = WC()->cart;
		$cart_cache = DAY_IN_SECONDS * 2;


		//CHECK IF DATE IS AVAILABLE, IF NOT, RETURN ERROR IN MESSAGE BODY
		if ( ! $nvbk->is_available( $_POST['apartment_id'], $_POST['begin'], $_POST['end'] ) )
			return ["status" => 1];




		//GET PRICE AND SEND IN MESSAGE BODY
		$price = $nvbk->get_new_booking_price( $_POST['apartment_id'], $_POST['begin'], $_POST['end'] );


		//SENDING PRICE INFO AFTER DATE RANGE SELECTION
		if ( isset($_POST['pre_checkout']) && $_POST['pre_checkout'] == "yes" )
			return ["price" => $price, "status" => 0];


		//CREATING ORDER BEFORE SENDING USER TO CHECKOUT (with JS)
		$booking_id = $nvbk->insert_booking ( (int)$_POST['apartment_id'], $_POST["begin"], $_POST["end"] );

		$nvbk_meta = array(
			"apartment_id" => $_POST["apartment_id"],
			"apartment_name" => $_POST["apartment_name"],
			"begin" => $_POST["begin"],
			"end" => $_POST["end"],
			"price" => $price["price_final"],
			"adults" => $_POST["adults"],
			"kids" => $_POST["kids"],
			"booking_id" => (int)$booking_id,
			"booking_confirmed" => false
		);

		if ( !$cart->is_empty() ) $cart->empty_cart();
	
		$cart->add_to_cart( 1084, 1, NULL, NULL, [ "nvbk_meta" => json_encode( $nvbk_meta, JSON_UNESCAPED_UNICODE ) ] );
		$cart->calculate_totals();
		WC()->session->set('cart', $cart->cart_content);
		$cart->set_session();
		$cart->maybe_set_cart_cookies();

		return ["status" => 0];
	}
);