<?php


//FASTEN UP WOOO CHECKOUT
add_filter('woocommerce_defer_transactional_emails', '__return_true' );

function is_woocommerce_page ()
{
	return ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() );
}




add_action(
	"template_redirect",
	function ()
	{
		// Skip Woo Pages
		if ( is_woocommerce_page() ) return;

		// Otherwise...
		remove_action('wp_enqueue_scripts', [WC_Frontend_Scripts::class, 'load_scripts']);
		remove_action('wp_print_scripts', [WC_Frontend_Scripts::class, 'localize_printed_scripts'], 5);
		remove_action('wp_print_footer_scripts', [WC_Frontend_Scripts::class, 'localize_printed_scripts'], 5);

		wp_dequeue_script( "woocommerce" );
		wp_dequeue_script( "wc-add-to-cart" );
		wp_dequeue_script( "wc-cart-fragments-js-extra" );

		add_filter( 'woocommerce_enqueue_styles', '__return_false' );
	},
	999
);

add_action(
	"enqueue_block_assets",
	function () {
		wp_dequeue_style( "wc-blocks-style" );
		wp_dequeue_style( "wc-blocks-vendors-style" );
	}, 999
);


/*
adds woocommerce body classes

add_action(
	'body_class',
	function ( $classes ) {

		if ( is_page("my-account") ) {
			$classes[]
		}

		return $classes;
	}
)
*/


/*
disable cart page
*/

add_action(
	'template_redirect',
	function () {
		if (is_page("cart"))
		{
			wp_redirect(home_url());
		}
	}, 1
);



/*
set custom product price in cart item
*/
add_action(
	'woocommerce_before_calculate_totals',
	function ( $cart_object )
	{  
	    //if( !WC()->session->__isset( "reload_checkout" )) {
	        foreach ( $cart_object->get_cart() as $item )
	        {
	            if ( ! empty( $item['nvbk_meta'] ) )
	            {
	            	$meta = json_decode( $item["nvbk_meta"], true );
	                $item['data']->set_price( $meta["price"]);
	            }
	        }  
	    //}
	}, 99 
);



/*
set custom product title in cart item
*/
add_filter(
	'woocommerce_cart_item_name',
	function ( $title, $cart_item, $cart_item_key )
	{
		//@session_start();
		if ( empty( $cart_item["nvbk_meta"] ) ) {
			return $title;
		}
		else {
			$meta = json_decode( $cart_item["nvbk_meta"], true );
			return $meta["apartment_name"];
		}
	}, 99, 3
);


add_filter(
	'woocommerce_order_button_text',
	function( $text ) {
		return "Zaplatit a rezervovat";
	}
);



add_action(
	'woocommerce_thankyou',
	function ( $order_id ) { 
	    if ( ! $order_id ) {
	        return;
	    }

	    $order = wc_get_order( $order_id );
	    $order->update_status( 'completed' );
	}
);

add_filter(
	'woocommerce_default_address_fields', 
	function ( $address_fields ) {
		$address_fields['company']['required'] = false;
		$address_fields['postcode']['required'] = false;
		$address_fields['city']['required'] = false;
		$address_fields['state']['required'] = false;
		$address_fields['country']['required'] = false;
		$address_fields['address_1']['required'] = false;
		return $address_fields;
	 }
);














/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package navalachy
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function navalachy_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 150,
			'single_image_width'    => 300,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'navalachy_woocommerce_setup' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function navalachy_woocommerce_scripts() {
	wp_enqueue_style( 'navalachy-woocommerce-style', get_template_directory_uri() . '/assets/woocommerce.css', array(), _S_VERSION );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'navalachy-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'navalachy_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function navalachy_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'navalachy_woocommerce_active_body_class' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function navalachy_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'navalachy_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'navalachy_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function navalachy_woocommerce_wrapper_before() {
		?>
			<main id="primary" class="site-main">
		<?php
	}
}
add_action( 'woocommerce_before_main_content', 'navalachy_woocommerce_wrapper_before' );

if ( ! function_exists( 'navalachy_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function navalachy_woocommerce_wrapper_after() {
		?>
			</main><!-- #main -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'navalachy_woocommerce_wrapper_after' );
