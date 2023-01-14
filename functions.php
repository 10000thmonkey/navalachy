<?php
require_once("inc/lib-nv.php");




/*
INCLUDE JS & CSS LIBRARIES
available modules:
	lightbox - lightbox gallery
	datepicker - for reservation functionality
*/


$NV_MODULES = array();

function nv_use_modules ( $modules ) {
	global $NV_MODULES;
	$NV_MODULES = $modules;
}

function navalachy_modules()
{
	$templ_dir = get_template_directory_uri();
	global $NV_MODULES;

	include "templates/cover-image.php";

	wp_enqueue_style( 'navalachy', $templ_dir."/style.css" );
	wp_enqueue_style( 'navalachy-style', $templ_dir."/inc/style.css" );
	wp_enqueue_style( "navalachy-style-legacy", $templ_dir."/legacy.css" );
	wp_enqueue_style( "navalachy-icons", $templ_dir. "/assets/icons/style.css" );

	wp_enqueue_script( "domster", $templ_dir. "/inc/domster.js" );
	
	if ( in_array("ui/lightbox", $NV_MODULES) ) {
		wp_enqueue_script( "lightbox", $templ_dir . "/inc/lightbox/lightbox.min.js" );
		wp_enqueue_style( "lightbox", $templ_dir . "/inc/lightbox/lightbox.min.css");
		include "templates/gallery.php";
	}
	if ( in_array("ui/slider", $NV_MODULES) ) 
		wp_enqueue_script( "lightbox", $templ_dir . "/assets/slider.js" );

	if ( in_array("booking/lib", $NV_MODULES) ) {
		require_once("inc/lib-booking.php");
		global $nvbk;
		$nvbk = new NV_Booking();
	}
	if ( in_array("booking/form", $NV_MODULES) ) {
		wp_enqueue_script( "booking-datepicker", $templ_dir . "/inc/hello-week.min.js" );
		wp_enqueue_style( "booking-datepicker", $templ_dir . "/inc/hello-week.min.css" );
		include "templates/booking-form.php";
	}

	if ( in_array("accomodation/feed", $NV_MODULES) ) include "templates/accomodation-feed.php";
	if ( in_array("experiences/feed", $NV_MODULES) ) include "templates/experiences-feed.php";

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'navalachy_modules' );

function nv_register_vars ( ) {
	global $nv_vars;
	if ( empty( $nv_vars ) ) return;
	wp_register_script( "nv_vars", "" );
	wp_enqueue_script( "nv_vars" );
	wp_add_inline_script( 'nv_vars', 'var nv_vars = ' . json_encode($nv_vars) , 'before' );
}
add_action( 'wp_enqueue_scripts', 'nv_register_vars' );













function nv_send_mail ($args = []) {
	if (!isset($args) || $args == [] ) return "fuckoff. dej mi kurva aspon jeden argument";
	$body = isset($args->body) ? $args->body : "";

	return wp_mail( $args["to"], $args["subject"], $args["body"], $args["headers"] );

}

function debug_wpmail( $result = false ) {

	if ( $result )
		return;

	global $ts_mail_errors, $phpmailer;

	if ( ! isset($ts_mail_errors) )
		$ts_mail_errors = array();

	if ( isset($phpmailer) )
		$ts_mail_errors[] = $phpmailer->ErrorInfo;

	print_r($ts_mail_errors);
}







add_filter( 'http_request_timeout', 'my_custom_timeout' );
function my_custom_timeout( $timeout_value ) {
    return 30; // Change the timeout value to 60 seconds
}




function nvbk_ajax_ubytovani_contact_form ()
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
add_action("wp_ajax_nvbk_ubytovani_contact_form", "nvbk_ajax_ubytovani_contact_form");
add_action("wp_ajax_nopriv_nvbk_ubytovani_contact_form", "nvbk_ajax_ubytovani_contact_form");







function nvbk_ajax_show_rates ()
{
	global $nvbk;
	if(WP_DEBUG) @ini_set("display_errors", 1);
	
	header("Content-Type: application/json; charset=UTF-8");

	$from = $_POST["from"];
	$to = $_POST["to"];
	$apartmentId = $_POST["apartmentId"];

	$response = $nvbk->get_rates($from, $to, [(int)$apartmentId]);

	echo json_encode( $response );
	die();
}
add_action("wp_ajax_nvbk_show_rates", "nvbk_ajax_show_rates");
add_action("wp_ajax_nopriv_nvbk_show_rates", "nvbk_ajax_show_rates");








function nvbk_ajax_to_checkout()
{
	if(WP_DEBUG) @ini_set( 'display_errors', 1 );
	require_once("inc/lib-booking.php");
	$nvbk = new NV_Booking();
	$cart = WC()->cart;
	$cart_cache = DAY_IN_SECONDS * 2;

	$return = array(
		"success" => false,
		"body" => "",
	);


	//CHECK IF DATE IS AVAILABLE, IF NOT, RETURN ERROR IN MESSAGE BODY
	$unavailable_days = $nvbk->get_disabled_days($_POST['apartmentId']);
	$stay_array = $nvbk->get_date_range_array($_POST['begin'], $_POST['end']);
	if ( !empty( array_intersect( $unavailable_days, $stay_array ) ) ) {
		$return["body"] = "Termín je obsazený.";
		echo json_encode($return);
		wp_die();
	}


	//GET PRICE AND SEND IN MESSAGE BODY
	$response = $nvbk->get_new_booking_price( $_POST['begin'], $_POST['end'], $_POST['apartmentId'],1);//$_POST['people'] );

	//ANY ERROR
	if ( empty( $response["prices"] ) || ! empty( $response["errorMessages"] ) || is_wp_error($response) ) {

		$return["body"] = "Rezervaci se nepodařilo vytvořit. Prosíme, kontaktujte nás.";
		$return["response"] = $response;

		echo json_encode($return);
		wp_die();
	}


	//echo print_r($response);
	$price = (int)reset($response["prices"])["price"];

	//IF OK, SEND PRICE TO MESSAGE BODY
	if ( isset($_POST['preCheckout']) && $_POST['preCheckout'] == "yes" )
	{	
		$return["price"] = $price;
		$return["success"] = true;
	}
	else
	{	
		$args = array(
			"nvbk_booking_apartmentId" => $_POST["apartmentId"],
			"nvbk_booking_apartmentName" => $_POST["apartmentName"],
			"nvbk_booking_begin" => $_POST["begin"],
			"nvbk_booking_end" => $_POST["end"],
			"nvbk_booking_price" => (int)$price,
			"nvbk_booking_people" => $_POST["people"],
		);

		if ( !$cart->is_empty() ) $cart->empty_cart();
	
		$cart->add_to_cart( 1084, 1, NULL, NULL, $args );
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

function nv_woo_custom_price_to_cart_item( $cart_object ) {  
    //if( !WC()->session->__isset( "reload_checkout" )) {
        foreach ( $cart_object->get_cart() as $item ) {
            if( array_key_exists( 'nvbk_booking_price', $item  ) ) {
                $item['data']->set_price( $item["nvbk_booking_price"]);
            }
        }  
    //}
}
add_action( 'woocommerce_before_calculate_totals', 'nv_woo_custom_price_to_cart_item', 99 );


function nvbk_cart_product_title( $title, $cart_item, $cart_item_key ) {
	@session_start();
	$name = $cart_item["nvbk_booking_apartmentName"];
	if (!empty($name))
		return $name;
	else
		return $title;
}
add_filter( "woocommerce_cart_item_name", "nvbk_cart_product_title", 99, 3);






///rezervace zaplacena


function nv_order_received_redirect(){
    
    // do nothing if we are not on the order received page
    if( ! is_wc_endpoint_url( 'order-received' ) || empty( $_GET[ 'key' ] ) ) {
        return; 
    }

    include_once("inc/lib-booking.php");
    $nvbk = new NV_Booking();


    $order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
    $order = wc_get_order( $order_id );
    $order_meta = get_post_meta( $order_id );
   
    // if( 'navalachy_qrpayment' === $order->get_payment_method() ) {
    //     if cash of delivery, redirecto to a custom thank you page
    //    wp_safe_redirect( site_url( 'zblaaabluu' ) );
    //    exit; // always exit
    // }
    //echo json_encode($order_meta);
	$response = $nvbk->confirm_reservation( array( 
	 	  "arrivalDate" => $order_meta["nvbk_booking_begin"][0],
		"departureDate" => $order_meta["nvbk_booking_end"][0],
		  "apartmentId" => $order_meta["nvbk_booking_apartmentId"][0],
		    "firstName" => $order->get_billing_first_name(),
		     "lastName" => $order->get_billing_last_name(),
		        "email" => $order->get_billing_email(),
		        "phone" => $order->get_billing_phone(),
		       "notice" => $order->get_customer_note(),
		       "adults" => $order_meta["nvbk_booking_people"][0],
		        "price" => $order_meta["nvbk_booking_price"][0],
		     "language" => "cs"
	 ) );
	//file_put_contents("log.log", var_dump($response));
    //wp_safe_redirect( get_site_url()."/thankyou?key=" . $_GET['key'] );
}
add_action( 'template_redirect', 'nv_order_received_redirect');



function nvbk_cartmeta_to_ordermeta( $order_id, $posted_data )
{
    $cart = WC()->cart;
	
	foreach ( $cart->get_cart() as $cart_item )
	{
		$values = ["nvbk_booking_apartmentId", "nvbk_booking_apartmentName", "nvbk_booking_begin", "nvbk_booking_end", "nvbk_booking_price", "nvbk_booking_people" ];
		foreach ( $values as $value ) {
			update_post_meta( $order_id, $value, $cart_item[$value] );
		}
	} 
}
add_action( 'woocommerce_checkout_update_order_meta', "nvbk_cartmeta_to_ordermeta", 10, 2);
//add_action('woocommerce_add_order_item_meta','nvbk_add_values_to_order_item_meta',1,2);








// function nv_gen_voucher(){

// 	if (isset($_POST["imgURI"])) {

// 		$vouchers = json_decode(file_get_contents(get_template_directory()."/vouchers.json"));

// 		//is there the requested voucher in the db? if so, 
// 		foreach($vouchers as $key => $voucher) {
		
// 			if ($voucher->order == $_POST["order_id"]) {

// 				//define image path
// 				$filename = "voucher-".$_POST['order_id']."_v".$_POST['type'].".png";
// 				$upload_dir = get_template_directory(). "/../../uploads/vouchers/". $filename;

// 				//upload image
// 				$img_stream = str_replace (" ", "+", str_replace("data:image/octet-stream;base64," , "", $_POST["imgURI"]) );
// 				file_put_contents($upload_dir, base64_decode($img_stream));
				
// 				//replace with database query
// 				//update voucher image location in db
// 				$voucher_image_path = get_site_url() . "/wp-content/uploads/vouchers/" . $filename;

// 				//update db, replace with db query
// 				$vouchers[$key]->image = $voucher_image_path;
// 				file_put_contents(get_template_directory()."/vouchers.json", json_encode($vouchers));


// 				//send email
// 				if ($voucher->given_email_send) { //send email to the person voucher was given to

// 					if ( $voucher->anonymous ) {

// 						$subject = "Dárkový voucher pro " . $voucher->given_name . "!";
// 					} else {

// 						$subject = "X vám právě daroval voucher na pobyt v Y!";
// 					}

// 					nv_send_mail( array(
// 						"to" => $voucher->given_email,
// 						"subject" => $subject,
// 						"body" => "this is the email body"
// 					) );
// 				}

// 				$subject = "Váš voucher NaValachy na pobyt na X";
// 				nv_send_mail( array(
// 					"to" => $voucher->given_email,
// 					"subject" => $subject,
// 					"body" => "this is the email body for you who purchased"
// 				) );

// 			}
// 		}
// 	} else {
// 		echo "nastal nejaky problem, ktery je potreba nejak vyresit";
// 	}

// 	die();
// }
// add_action("wp_ajax_nv_gen_voucher", "nv_gen_voucher");
// add_action("wp_ajax_nopriv_nv_gen_voucher", "nv_gen_voucher");


//FASTEN UP WOOO CHECKOUT
add_filter('woocommerce_defer_transactional_emails', '__return_true' );






// add_action('wp_ajax_nv_voucher_precheckout', 'nv_voucher_precheckout'); // wp_ajax_{ACTION HERE} 
// add_action('wp_ajax_nopriv_nv_voucher_precheckout', 'nv_voucher_precheckout');
// function nv_voucher_precheckout () {

// 	session_start();

// 	$voucher_code = (string)substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
// 	$_SESSION['voucher_code'] = $voucher_code;
// 	echo $voucher_code;
// 	$voucher = array(

// 		"code" => $voucher_code,
// 		"order" => 0,
// 		"value" => isset($_POST['voucher_value']) ? $_POST['voucher_value'] : 1,
// 		"type" => isset($_POST['voucher_type']) ? $_POST['voucher_type'] : 1,
// 		"given_email" => ( isset($_POST['voucher_given_email']) && $_POST['voucher_given_email'] ) ?
// 			$_POST['voucher_given_email'] : "unset",
// 		"given_email_send" =>
// 			isset($_POST['voucher_given_email_send']) == "on" ? true : false,
// 		"given_name" => ( isset($_POST['voucher_given_name']) && $_POST['voucher_given_name'] ) ?
// 			$_POST['voucher_given_name'] : "unset",
// 		"given_name_send" => 
// 			isset($_POST['voucher_given_name_send']) == "on" ? true : false,
// 		"anonymous" => false
// 	);

// 	$filename = get_template_directory() . "/vouchers.json";
// 	$vouchers = json_decode( file_get_contents( $filename ) );
// 	array_push( $vouchers , $voucher);
// 	file_put_contents( $filename, json_encode($vouchers) );



// 	//$_SESSION['voucher'] = json_encode($voucher);	

// 	//referemce: https://rudrastyh.com/woocommerce/create-orders-programmatically.html

// 	WC()->cart->add_to_cart( 919 );

// /*	$order = wc_create_order();
// 	$order->add_product( wc_get_product(919) );
// 	$order->calculate_totals();
// 	$order->set_customer_ip_address($_SERVER['REMOTE_ADDR']);
// 	$order->save();
// 	echo 1;*/
// 	//wp_safe_redirect( wc_get_checkout_url() );
	
// 	//header("Location: /checkout/");

// 	die();

// }






//RESPONSIVE IMG FUNCTION

function nv_responsive_img ( $attachment_id, $sizes = "(max-width: 600px) 100vw, 25vw", $alt = "") {
	$src = wp_get_attachment_image_url( $attachment_id, "medium");
	$srcfull = wp_get_attachment_image_url( $attachment_id, "full");
	$srcset = wp_get_attachment_image_srcset( $attachment_id, "large" );
	$attalt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true);
	if ($attalt != "") $alt = $attalt;

	return '<img src="'.esc_attr( $src ).'"
			srcset="'.esc_attr( $srcset ).'"
			sizes="'.esc_attr( $sizes ).'"
			alt="'.esc_attr( $alt ).'"
			loading="lazy"/>';
}





//ALLOW SVG UPLOADS

add_filter('upload_mimes', 'cc_mime_types');
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}









// FILTROVANI ZAZITKU

add_action('wp_ajax_nv_filter_experiences', 'nv_filter_experiences_function', 1, 1); 
add_action('wp_ajax_nopriv_nv_filter_experiences', 'nv_filter_experiences_function', 1, 1);

function nv_filter_experiences_function( $args )
{
	if ( !is_array ($args) ) {
		$args = array (
			"tagfilter" => $_POST['tagfilter'],
			"categoryfilter" => $_POST['categoryfilter'],
			"orderby" => $_POST['orderby'],
			"paged" => $_POST['paged'],
		);
	}

	echo nv_experiences_fetch( $args );
	die();
}





























if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function navalachy_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on navalachy, use a find and replace
		* to change 'navalachy' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'navalachy', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-header' => esc_html__( 'Header', 'navalachy' ),
			'menu-footer' => esc_html__( 'Footer', 'navalachy' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

}
add_action( 'after_setup_theme', 'navalachy_setup' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';
/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';
/**
 * Customizer additions.
 */
//require get_template_directory() . '/inc/customizer.php';
/*
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}