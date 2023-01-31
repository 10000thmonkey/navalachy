<?php
$NV_MODULES = [
	"UI/gallery",
	"Experiences/tags",
	"Accomodation/feed",
	"Booking/lib"
];

$ID = get_the_id();
$meta_fields = get_post_meta( $ID );

get_header();
?>
<main id="primary" class="site-main" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_id() ),
		"heading" => get_the_title(),
		"subheading" => $meta_fields["location"][0]
	) );
	?>

	<article class="contentwrap cols cols-md-2 gap-xl padding-xl">

		<div class="col entry-content">

			<p class="description"><?php echo get_the_content(); ?></p>
			<div class="gallery space-around-lg">
				<?php
				$gallery = $meta_fields["gallery"];
				echo nv_template_gallery(array("gallery" => $meta_fields["gallery"]));
				?>
			</div>
		</div>

		<div class="col sidebar rows gap-hg">	

			<div class="tags">
				<?php
				echo nv_template_experiences_tags( [ "terms" => get_the_terms($post, "experiences_tags") ] );
				?>
			</div>
			
			<div class="iconset">
				<?php
				$details = array("location", "ticket", "time", "link");

				foreach ( $details as $detail )
				{
					if ( isset($meta_fields[$detail][0]) && $meta_fields[$detail][0] != "")
					{
						if ($detail == "location") {
							$c = "<div>" . $meta_fields[$detail][0] . '<br><a href="geo:'.$meta_fields["gps"][0].'">'.$meta_fields["gps"][0].'</a></div>';
						} elseif ($detail == "link") {
							$c = '<a target="_blank" href="'.$meta_fields["link"][0].'">'.$meta_fields["link"][0].'</a>';
						} else {
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
				<?php echo '<iframe width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q='.urlencode($meta_fields["gps"][0]).'+(Azzy)&amp;t=p&amp;z=8&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>';?>
			</div>

		</div>

	</article>

	<footer class="related-accomodation" style="background:var(--secondary-light);">
		<div class="contentwrap padding-xl">
			<h2 class="space-around-hg">Populární ubytování v okolí</h2>

			<div class="cols cols-sm-2 cols-md-3 gap-hg">
				<?php
				echo nv_template_accomodation_feed( array(
					"hovercards" => true
				) );
				?>
			</div>
		</div>
	</footer>
</main>
<?php
get_footer();
