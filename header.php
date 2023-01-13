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
global $nv_booking;

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">


	<?php wp_head(); ?>
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
