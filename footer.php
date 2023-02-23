<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package navalachy
 */

?>

	<footer id="colophon" class="site-footer padding-hg">

		<div class="contentwrap gap-md footer-top cols cols-md-4 cols-sm-2">
			
			<div class="col padding-lg footer-logo">
				<img src="/wp-content/uploads/navalachy-logo-bila.svg">
			</div>
			<div class="col padding-lg">
				<div class="iconlist iconlist-lg" style="color: white">
					<a href="https://www.instagram.com/mountainlodgeazzy/" class="icon nvicon nvicon-instagram"></a>
					<a href="https://www.facebook.com/azzyubytovani/" class="icon nvicon nvicon-facebook"></a>
				</div>
			</div>
			<ul class="col padding-lg">
				<li>Návrat ovečky do Bílých Karpat z.s.</li>
				<li><a href="tel:+420776293114">+420 776 293 114</a></li>
				<li><a href="mailto:info@navalachy.cz">info@navalachy.cz</a></li>
			</ul>
			<div class="col padding-lg">
				<?php
					wp_nav_menu(
	    				array(
	    					'menu_id'        => 51,
	    					'container' => false,
	    				)
	    			);
	    		?>
			</div>
		</div>

		<div class="contentwrap footer-bottom cols-flex space-between padding-lg">
			<div>Webdesign: 10000 Monkeys © 2022</div>
			<div>
				<a href="/ochrana-osobnich-udaju">Ochrana osobních údajů</a>
				<a href="/vseobecne-obchodni-podminky">Všeobecné obchodní podmínky</a>
			</div>
		</div>

		
	</footer><!-- #colophon -->
</div><!-- #page -->

<script type="text/javascript">
var modal;
var ggg;

function isMobile () {
	return window.screen.width <= 800;
}

q(function()
{

	if ( 'undefined' !== typeof lightbox ) {
		lightbox.option({
			'resizeDuration': 200,
			'fadeDuration': 200,
			'imageFadeDuration' : 200,
			'wrapAround': true,
			'disableScrolling' : true
		});
	}
});


</script>

<?php
wp_footer();
global $isAdmin;
if (!$isAdmin):
?>
<STYLE>
	#wpadminbar {display: none;}
html {margin-top: 0 !important;}
</STYLE>
<?php endif; ?>
</body>
</html>
