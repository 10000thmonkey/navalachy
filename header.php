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
global $NV_DEV;
global $user;
global $isAdmin;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preload" as="image" href="/wp-content/uploads/logo-navalachy.svg">
	<link rel="profile" href="https://gmpg.org/xfn/11">


	<?php wp_head(); ?>
	<?php if ( ! $NV_DEV && ! $isAdmin ): ?>
	<script type='text/javascript'>
		window.smartlook||(function(d) {
		var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
		var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
		c.charset='utf-8';c.src='https://web-sdk.smartlook.com/recorder.js';h.appendChild(c);
		})(document);
		smartlook('init', '794196b5441c964e421dc60be90b282ea9023239', { region: 'eu' });
	</script>
	<!-- Google tag (gtag.js) -->
	
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-9LDG9K764S"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-9LDG9K764S');
	</script>

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
    		<div class="header-right nodisplay">

    			<?php if ( $NV_DEV ): ?>

	    			<nv-logged-in>
						<nav role="navigation" class="dropdown-nav">
							<ul>
							    <li>

									<nv-dropdown nv-dropdown="" class="dropdown padding-md hiding hidden ">
										<nv-dropdown-open>
									    	<a class="button button-plain" onclick="this.parentElement.q('.dropdown')[0].toggleHide();">
									    		<?=$user->data->display_name;?>
												<div class="avatar avatar-small">
													<?= nv_c( "UI/responsive-image", [ "attachment_id" => 1404, "sizes" => "(min-width: 1px) 32px, 32px" ] ); ?>
												</div>
									    	</a>	
										</nv-dropdown-open>
										<nv-dropdown-items>
											<li><a href="/my-account">Můj účet</a></li>
											<?php if( in_array( "accomodation_host", $user->roles ) || in_array( "administrator", $user->roles ) ): ?>
												<li><a href="/dashboard/accomodation">Ubytování</a></li>
											<?php endif; ?>

											<li><a href="/my-account/customer-logout">Odhlásit</a></li>
										</nv-dropdown-items>	
									</nv-dropdown>
							    </li>
						 	</ul>
						</nav>
					</nv-logged-in>

					<nv-logged-out>
	    				<a class="button button-secondary-transparent" href="/my-account/">Přihlásit</a>
	    			</nv-logged-out>

    			<?php endif; ?>

				<button class="menu-toggle nvicon nvicon-menu" aria-controls="primary-menu-mobile" aria-expanded="false" onclick="document.q('.site-header')[0].toggleClass('toggle')"></button>
    		</div>
    
  			<nav id="site-navigation-mobile" class="rows main-navigation-mobile">
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
	<?php do_action("nv_after_header"); ?>
