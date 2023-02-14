<?php

global $user;
global $isAdmin;
$isAdmin = $user ? array_key_exists("administrator", $user->roles) : false;

session_start();
$_SESSION['currency'] = "CZK"; //empty($_SESSION['currency']) ? "CZK" : "EUR";
$currencies = ["EUR" => ["€", 1], "CZK" => ["Kč", floatval(get_option("nvbk_exchange_EUR_CZK"))]];

require_once "inc/functions-nv.php";
require_once "inc/functions-email.php";

require_once "Booking/functions.php";
require_once "Experiences/functions.php";

require_once "dashboard/functions.php";

if ( class_exists( 'WooCommerce' ) ) {
	require_once get_template_directory() . '/inc/functions-woocommerce.php';
}

add_role(
  'accomodation_host',
  'Accomodation Host'
);

$user = (is_user_logged_in()) ? wp_get_current_user() : false;


add_filter( 'login_redirect', 'nv_login_redirect', 10, 3 );

function nv_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( !in_array( 'administrator', $user->roles )) {
             $redirect_to = home_url();
        }
    }
    return $redirect_to;
}







function custom_http_request_timeout( $timeout_value ) {
    return 30;
}
add_filter( 'http_request_timeout', 'custom_http_request_timeout' );



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








//ALLOW SVG UPLOADS

add_filter('upload_mimes', 'cc_mime_types');
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
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

/*
 * Load WooCommerce compatibility file.
 */
