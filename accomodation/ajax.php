<?php

nv_ajax(
	"accomodation/feed-search",
	function ()
	{
		return nv_c( "accomodation/c/feed",[
			"range" => [
				"begin" => $_POST["begin"],
				"end" => $_POST["end"]
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
			"subject" => "Nový dotaz z Valach od " . $_POST['name'] . " (" . $_POST['email'] . ")",
			"body" => $_POST['message'],
			"headers" => array(
				"From: Na Valachy kontaktní formulář <info@navalachy.cz>",
				'Content-Type: text/html; charset=UTF-8',
				'Reply-To: '.$_POST["name"].' <'.$_POST['email'].'>',
			)
		));	

		$res2 = nv_send_mail (array(
			"to" => $_POST["email"], 
			"subject" => "NaValachy.cz: Dotaz jsme majiteli odeslali.",
			"body" => "Váš dotaz na e-mail ".$_POST["host_email"]." byl úspěšně zaslán.",
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
		if(WP_DEBUG) @ini_set( 'display_errors', 1 );
		include_once( get_template_directory() . "/accomodation/i/lib.php" );

		$nvbk = new NVBK();
		$cart = WC()->cart;
		$cart_cache = DAY_IN_SECONDS * 2;

		$return = array(
			"success" => false,
			"body" => "",
		);


		//CHECK IF DATE IS AVAILABLE, IF NOT, RETURN ERROR IN MESSAGE BODY
		if ( ! $nvbk->is_available( $_POST['apartment_id'], $_POST['begin'], $_POST['end'] ) )
		{
			$return["body"] = "Termín je obsazený.";
			return $return;
		}


		//GET PRICE AND SEND IN MESSAGE BODY
		$price = $nvbk->get_new_booking_price( $_POST['apartment_id'], $_POST['begin'], $_POST['end'] );
		

		//IF OK, SEND PRICE TO MESSAGE BODY

		//SENDING PRICE INFO AFTER DATE RANGE SELECTION
		if ( isset($_POST['pre_checkout']) && $_POST['pre_checkout'] == "yes" )
		{	
			$return["price"] = $price;
			$return["success"] = true;
		}
		//CREATING ORDER BEFORE SENDING USER TO CHECKOUT (with JS)
		else
		{ 
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
		
			$cart->add_to_cart( 1084, 1, NULL, NULL, [ "nvbk_meta" => json_encode( $nvbk_meta ) ] );
			$cart->calculate_totals();
			WC()->session->set('cart', $cart->cart_content);
			$cart->set_session();
			$cart->maybe_set_cart_cookies();

			$return["success"] = true;
		}
		
		return $return;
	}
);