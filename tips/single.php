<?php
$ID = get_the_id();
$meta_fields = get_post_meta( $ID );

wp_enqueue_script("nv-slider", "/wp-content/themes/navalachy/assets/slider.js" );
wp_enqueue_script( "lightbox", "/wp-content/themes/navalachy/assets/lightbox/lightbox.min.js", ["jquery"] );
wp_enqueue_style( "lightbox", "/wp-content/themes/navalachy/assets/lightbox/lightbox.min.css");

get_header();
?>
<main id="primary" class="site-main" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	echo nv_c( "UI/cover-image", array(
		"attachment_id" => get_post_thumbnail_id( get_the_id() ),
		"heading" => get_the_title(),
		"subheading" => $meta_fields["location"][0]
	) );
	?>

	<article class="contentwrap cols cols-md-2 gap-xl padding-xl">

		<div class="col entry-content">

			<p class="description"><?= get_the_content(); ?></p>
			<div class="gallery space-around-lg">
				<?= nv_c( "UI/gallery", ["gallery" => $meta_fields["gallery"] ] );	?>
			</div>
		</div>

		<div class="col sidebar rows gap-hg">	

			<div class="tags">
				<?= nv_c( "UI/tags", [ "terms" => get_the_terms($post, "tips_tags") ] ); ?>
			</div>
			
			<div class="iconset">
				<?php
				$details = array("location", "ticket", "time", "link");

				foreach ( $details as $detail )
				{
					if ( isset($meta_fields[$detail][0]) && $meta_fields[$detail][0] )
					{
						switch ( $detail ) {
							case "location":
								$c = "<div>" . $meta_fields[$detail][0] . '<br><a href="geo:'.$meta_fields["gps"][0].'">'.$meta_fields["gps"][0].'</a></div>';
								break;
							case "link":
								$c = '<a target="_blank" href="'.$meta_fields["link"][0].'">'.$meta_fields["link"][0].'</a>';
								break;
							default:
								$c = $meta_fields[$detail][0];
						}

						echo <<<HTML
						<div class="icon">
							<i class="nvicon nvicon-$detail"></i>
							$c
						</div>
						HTML;
					}
				}
				?>
			</div>

			<div class="map">
				<?= '<iframe width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q='.urlencode($meta_fields["gps"][0]).'+(Azzy)&amp;t=p&amp;z=8&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>';?>
			</div>

		</div>

	</article>

	<footer class="related-accomodation" style="background:var(--secondary-light);">
		<div class="contentwrap padding-xl">
			<h2 class="space-around-hg">Ubytování v okolí</h2>

			<nv-repeat nv-inner-class="cols cols-sm-2 cols-md-3 gap-hg" nv-items="<?= esc_attr( json_encode( nv_c ( "accomodation/c/feed", [ "limit" => 3 ] )["items"] ) ); ?>">
				<?= nv_t( "accomodation/t/hovercard" ); ?>
			</nv-repeat>
		</div>
	</footer>
</main>
<?php
get_footer();
