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
				<div class="iconlist iconlist-hg" style="color: white">
					<a class="icon nvicon nvicon-instagram"></a>
					<a class="icon nvicon nvicon-facebook"></a>
					<a class="icon nvicon nvicon-youtube"></a>
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
var isMobile = false;
  
var ggg;


q(function(){
	q('.openmodale').on("click", function (e) {
	    let modale = e.currentTarget.attr("data-modale");
	    q('.modale[data-modale="' + modale + '"]').addClass('opened');
	    document.body.css("overflow", "hidden");
	});
	q('.closemodale').on("click", function (e) {
		e.preventDefault();
		q('.modale').removeClass('opened');
	    document.body.css("overflow", "unset");
	});
	q('.modale').on("click", function (e) {
		//e.preventDefault();
		if ( e.path.indexOf( this.q(".modal-dialog")[0] ) == -1 )
		{
			q('.modale').removeClass('opened');
		    document.body.css("overflow", "unset");
		}
	});

	if (window.screen.width <= 800) isMobile = true;
	window.addEventListener("resize", () => { if (window.screen.width <= 800) isMobile = true; else isMobile = false; });




	q(".gallery-slider").each( function (e)
	{
		ggg = this;
		const gallery = this;
		const items = this.q(".gallery-image");

		let prev = createNode("a")
		.addClass("slider-prev").addClass("nvicon").addClass("nvicon-arrow-left")
		.on( "click", () =>
		{
			for ( let item of items )
			{
				if ( gallery.scrollLeft > item.offsetLeft ) 
				{
					gallery.scrollTo( {
						left: item.offsetLeft,
						top: 0,
						behavior: "smooth"
					} );
				}
			}
		} );

		let next = createNode("a")
		.addClass("slider-next").addClass("nvicon").addClass("nvicon-arrow-right")
		.on( "click", () =>
		{
			for ( let item of items )
			{
				if ( gallery.scrollLeft < item.offsetLeft ) 
				{
					gallery.scrollTo( {
						left: item.offsetLeft,
						top: 0,
						behavior: "smooth"
					} );
					break;
				}
			}
		} );

		gallery.parentElement.append(prev);
		gallery.parentElement.append(next);
	});



	lightbox.option({
		'resizeDuration': 200,
		'fadeDuration': 200,
		'imageFadeDuration' : 200,
		'wrapAround': true,
		'disableScrolling' : true
	})
});


</script>

<?php wp_footer(); ?>

</body>
</html>
