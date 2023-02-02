<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package navalachy
 */

$user = wp_get_current_user();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">


	<?php wp_head(); ?>
	<?php if (!WP_DEBUG && !array_key_exists("administrator", $user->roles)): ?>
	<script type='text/javascript'>
		window.smartlook||(function(d) {
		var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
		var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
		c.charset='utf-8';c.src='https://web-sdk.smartlook.com/recorder.js';h.appendChild(c);
		})(document);
		smartlook('init', '794196b5441c964e421dc60be90b282ea9023239', { region: 'eu' });
	</script>
	<?php endif; ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'navalachy' ); ?></a>

	<header id="masthead" class="site-header">
	    <div class="contentwrap">
    		<div class="header-left site-branding">
    			<?php
    			the_custom_logo();
    			?>
    		</div>
    		<nav id="site-navigation" class="main-navigation">
    			<?php
    			wp_nav_menu(
    				array(
    					'theme_location' => 'menu-1',
    					'menu_id'        => 'primary-menu',
    					'menu_class' => 'menu-mobile',
    					'container' => false,

    				)
    			);
    			?>

    		</nav>
    		<div class="header-right">
				<button class="menu-toggle nvicon nvicon-menu" aria-controls="primary-menu-mobile" aria-expanded="false" onclick="document.q('.site-header')[0].toggleClass('toggle')"></button>
    		</div>
    
  			<nav id="site-navigation-mobile" class="main-navigation-mobile">
	  			<?php	
	    			wp_nav_menu(
	    				array(
	    					'theme_location' => 'menu-1',
	    					'menu_id'        => 'primary-menu-mobile',
	    					'container' => false,
	    				)
	    			);
	    		?>

  			</nav>
    	</div>
	</header><!-- #masthead -->
