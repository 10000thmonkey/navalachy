<?php
//remove_action('template_redirect', 'redirect_canonical');


// function custom_router() {

// 	//add_rewrite_rule('([a-zA-Z0-9,_]+)$', 'index.php?route1=$matches[1]', 'top');
// 	//add_rewrite_rule('^blog/?$', 'index.php?route1=blog', 'top' );
//     //add_rewrite_rule('^blog/([^/]*)/?', 'index.php?custom_page=$matches[1]', 'top');
// }
// add_action('init', 'custom_router');






$NV_DEV = ! empty( $_GET['NV_DEV'] ) ? true : NV_DEV;
//echo var_dump($NV_DEV);



if ( ! $NV_DEV )
{
	add_action(
		"send_headers",
		function() {
			header( "Strict-Transport-Security: max-age=604800; includeSubDomains" );
		}
	);
}






//load modules, global functions 

$templ_dir = get_template_directory();

require_once "$templ_dir/inc/functions-nv.php";
require_once "$templ_dir/inc/functions-email.php";

if ( class_exists( 'WooCommerce' ) ) {
	require_once "$templ_dir/inc/functions-woocommerce.php";
}

$global_functions = glob( get_template_directory() . "/*/functions-global.php");
foreach ( $global_functions as $gf )
	require_once "$gf";













add_role( 'accomodation_host', 'Accomodation Host' );

global $user;
global $isAdmin;
$user = (is_user_logged_in()) ? wp_get_current_user() : false;
$isAdmin = $user ? in_array("administrator", $user->roles) : false;

add_filter(
	'login_redirect', 
    function ( $redirect_to, $request, $user ) {
	    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
	        if ( !in_array( 'administrator', $user->roles )) {
	             $redirect_to = home_url();
	        }
	    }
	    return $redirect_to;
	}, 10, 3
);

if ( isset( $user ) && is_array( $user->roles ) )
{
    if ( ! $NV_DEV || ! $isAdmin )
    {
    	add_filter( "show_admin_bar", "__return_false" );
    }
}




add_filter(
    'page_template',
    function ($template) {
        global $post;

        if ($post->post_parent) {

            // get top level parent page
            $parent = get_post(
               reset( array_reverse( get_post_ancestors( $post->ID ) ) )
            );

            // or ...
            // when you need closest parent post instead
            // $parent = get_post($post->post_parent);

            $child_template = locate_template(
                [
                    $parent->post_name . '/' . $post->post_name . '/page.php'
                ]
            );

            if ($child_template) return $child_template;
        }
        return $template;
    }
);


add_filter(
	"http_request_timeout" ,
	function ( $timeout_value ) {
	    return 30;
	}
);













// DOING AJAX.
// Tries to load ajax.php from module directory.

if ( defined( "DOING_AJAX" ) && DOING_AJAX )
{
	$action = $_REQUEST['action'];
	if ( strpos( $action, "/") )
	{	
		//$mod = explode( "/", $action )[0];
		$mod = substr( $action, 0, strpos( $action, "/" ) );

		//override $action used by wordpress, "booking/confirm_booking" becomes "nv_confirm_booking"
		$_REQUEST['action'] = $_GET['action'] = $_POST['action'] = $action = "nv_" . str_replace( "/", "_", $action );

		if ( file_exists( "$templ_dir/$mod/ajax.php" ) )
			include_once "$templ_dir/$mod/ajax.php";
	}
}






add_action(
	"init",
	function()
	{
	    remove_image_size( 'woocommerce_thumbnail' );
	    remove_image_size( 'woocommerce_single' );
	    remove_image_size( 'woocommerce_gallery_thumbnail' );
	    remove_image_size( 'shop_catalog' );
	    remove_image_size( 'shop_single' );
	    remove_image_size( 'shop_thumbnail' );
	    //remove_image_size( 'medium_large' );
	    remove_image_size( '1536x1536' );

	    update_option('medium_large_size_w', '800');
	    update_option('medium_large_size_h', '0');
	}
);
add_filter(
    'intermediate_image_sizes', 
    function ( $default_image_sizes )
    {
        unset( $default_image_sizes["medium"] );
	    unset( $default_image_sizes['medium_large'] );
	    return $default_image_sizes;
    }
);





add_action(
	'wppusher_theme_was_updated',
	function () {

		ob_start();

	    w3tc_flush_all();

	    error_log( "Flushed cache! " . ob_get_clean() );
	}
);



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

add_filter(
	'upload_mimes',
	function ($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
);




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
