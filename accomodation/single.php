<?php
//global $nvbk;
global $_VAR;
global $nv_vars;

$ID = get_the_id();
$meta_fields = get_post_meta( $ID );
$title = get_the_title();

$host = get_user_meta( $meta_fields["host"][0] );

$amenities_query = pods("amenities")->find(array("limit" => 100));
$amenities_array = array();
while( $amenities_query->fetch() ) {
	$amenities_array[(int)$amenities_query->display("id")] = $amenities_query->display("name");
}

wp_enqueue_script( "lightbox", "/wp-content/themes/navalachy/assets/lightbox/lightbox.min.js", ["jquery"] );
wp_enqueue_style( "lightbox", "/wp-content/themes/navalachy/assets/lightbox/lightbox.min.css");
wp_enqueue_script("nv-booking", "/wp-content/themes/navalachy/accomodation/a/booking.js");
wp_enqueue_script("nv-slider", "/wp-content/themes/navalachy/assets/slider.js" );

wp_enqueue_script( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.js" );
wp_enqueue_style( "nv-datepicker", "/wp-content/themes/navalachy/accomodation/a/hello-week.min.css" );

get_header();
?>
<main id="primary">

	<div class="section-gallery">

		<?php
		$gallery = nv_c( "UI/gallery", [
			"gallery" => $meta_fields["gallery"],
			"sizes_w" => "(min-width: 800px) 700px, 100vw",
			"sizes_h" => "(min-width: 800px) 350px, 50vw",
			"slider" => true
		] );
		?>
		<nv-gallery-slider nv-items="<?= nv_c_attr( $gallery["items"] );?>">
			<?= nv_t( "UI/t/gallery-item" );?>
		</nv-gallery-slider>

		<a class="gallery-showmore button button-plain" nv-modal-open="accomodation-modal-detail">Více<i class="nvicon nvicon-grid"></i></a>
	</div>

	<div class="main columns cols-flex cols-md contentwrap gap-lg">

		<div class="sections-left col padding-hg">

			<div class="box padding-lg gap-lg rows">

				<div>
					<h1><?= get_the_title();?></h1>
					<a class="secondary-text" style="text-decoration: none;" href="#map">
						<?=$meta_fields["address"][0];?>
					</a>	
				</div>

				<p><?= $meta_fields["desc_short"][0]; ?></p>

				<div class="iconset iconset-hg cols cols-sm-2">
					
					<?php
					$basic_information = array();
					if ( isset($meta_fields["capacity"][0]) )
						$basic_information["group"] = $meta_fields["capacity"][0] . " osob";
					if ( isset($meta_fields["bedroom"][0]) )
						$basic_information["beds"] = $meta_fields["bedroom"][0] . " ložnice";
					if ( isset($meta_fields["wifi"][0]) && $meta_fields["wifi"][0] == 1)
						$basic_information["wifi"] = "Wi-fi";
					if ( isset($meta_fields["pets"][0]) && $meta_fields["pets"][0] == 1 )
						$basic_information["pets"] = "Pejsci vítáni";
					if ( isset($meta_fields["parties"][0]) && $meta_fields["parties"][0] == 1 )
						$basic_information["party"] = "Vhodné pro večírky";
					if ( isset($meta_fields["parking"][0]) && $meta_fields["parking"][0] == 1 )
						$basic_information["parking"] = "Parkování na pozemku";
				
					foreach ($basic_information as $key => $value) :
					?>
						<div class="icon">
							<i class="nvicon nvicon-<?php echo $key;?>"></i>
							<?php echo $value;?>
						</div>
					<?php 
					endforeach;
					?>
				</div>
				<div class="cols-flex" style="justify-content: right;">
					<a class="button button-icon button-plain" nv-modal-open="accomodation-modal-detail">Detail ubytování <i class="nvicon nvicon-arrow-right"></i></a>
				</div>
			</div>

		</div><!--.section-left-->



		<aside class="col reservation reallyaside">


			<div class="mobile-sliding-footer">
				
				<div class="content-on-shown space-between">
					<a class="btn btn-close" onclick="this.closestParent('aside').removeClass('slided').addClass('reallyaside');body.css('overflow','auto')"></a>
				</div>

				<div class="content-on-hidden rows center">
					<a class="button center" style="max-width:300px;width: 100%;" onclick="this.closestParent('aside').addClass('slided');body.css('overflow','hidden')">Rezervovat</a>
				</div>

			</div>

			<?= nv_c( "accomodation/c/form", [
				"iss" => true,
				"apartment_id" => (int)$ID,
				"apartment_name" => $title,
				"capacity" => (int)$meta_fields["capacity"][0],
				"ical" => $meta_fields["ical_url"][0]
			] ); ?>
 
		</aside>



	</div>
	<div class="main contentwrap">
		<div class="reviews padding-xl">

			<h3>Recenze</h3>

			<nv-modal aria-hidden="true" id="accomodation-modal-reviews">
				<div>
					<header class="only-close-button">
						<a class="btn-close" nv-modal-close aria-hidden="true"></a>
					</header>
					<article class="padding-lg">
					<?php

					$pod = pods("reviews");
					$query = new WP_Query( array(
						"post_type" => "reviews",
						"post__in" => $meta_fields["reviews"],
						"orderby" => "post__in"
					));

					if ($query->have_posts())
					{
						$counter = 0;
						$first3 = "";

						while ($query->have_posts())
						{
							$query->the_post();
							$pod->fetch($query->post->ID);
							$meta = get_post_meta(get_the_id());

							$t = get_the_title();
							$s = $pod->display("source");
							$c = $pod->display("text");

							$avatarId = get_post_thumbnail_id();
							if (empty($avatarId)) $avatarId = 1364;
							$avatarImg = nv_c( "UI/responsive-image", [
								"attachment_id"=> $avatarId,
								"sizes" => "(min-width:1px) 64px, 64px"
							] );
							
							//if ($meta["ubytko"][0] != $ID) continue;

							$pod->fetch(get_the_id());

							$r = <<<HTML
							<div class="review">
								<div class="review-head">
									<div class="review-avatar">$avatarImg</div>
									<div>
										<h3 class="review-name">$t</h3>
										<div class="review-source">$s</div>
									</div>
								</div>
								<div class="review-body">
									<div class="review-text">$c</div>
								</div>
							</div>
							HTML;

							echo $r;

							if ($counter < 3) $first3 .= $r;
							$counter++;
						}
						wp_reset_postdata();
					}
					?>

					</article>
				</div>
			</nv-modal>
			
			<div class="reviews-container">
				<?=$first3;?>
			</div>
			<div class="cols-flex" style="justify-content: center;"><a class="button button-plain" nv-modal-open="accomodation-modal-reviews">Všechny recenze (<?=$counter;?>)</a></div>
		</div>



		<div class="padding-hg">
			<div class="box info cols cols-md-3 cols-sm-2 padding-lg gap-xl">

				<div class="col">
					<div class="cols-flex cols-xs gap-hg">
						<div class="avatar avatar-big">
							<?= nv_c ("UI/responsive-image", [ "attachment_id" => $host["profile_picture"][0], "sizes" => "(min-width: 1px) 64px, 64px" ]); ?>
						</div>

						<div class="col rows gap-md">
							<h3><?= $host["first_name"][0] . " " . $host["last_name"][0]; ?></h3>
							<div class="iconset">
								<div class="icon">
									<i class="nvicon nvicon-phone"></i>
									<span class="nodisplay"></span>
									<a class="link" onclick="this.parentElement.q('span').removeClass('nodisplay').html('<?= $host["billing_phone"][0];?>'); this.remove();">Zobrazit</a>
								</div>
								<div class="icon">
									<i class="nvicon nvicon-email"></i>
									<a href="mailto:<?= $host["billing_email"][0];?>"><?= $host["billing_email"][0];?></a>
								</div>
							</div>
							<a class="button button-icon button-plain button-sm" nv-modal-open="accomodation-modal-contact" style="width:fit-content;">
								Rychlý dotaz <nv-icon class="nvicon-write"></nv-icon>
							</a>
						</div>
					</div>
				</div>

				<div class="col rows gap-md">
					<h3>Důležité informace</h3>
					<div class="iconset">
						<?php
						$additional_information = array(
							"self_checkin" => "Self check-in",
							"checkin" => "Check-in",
							"checkout" => "Check-out",
						);
						foreach($additional_information as $key => $value) {
							if ( !empty( $meta_fields[$key][0] ) && $meta_fields[$key][0] ) {
								echo "<div class='icon'>";
								switch ( $key ) {
									case "checkin" :
										echo "<i class='nvicon nvicon-in'></i>$value: ".$meta_fields[$key][0];
										break;
									case "checkout" :
										echo "<i class='nvicon nvicon-out'></i>$value: ".$meta_fields[$key][0];
										break;
									case "self_checkin" :
										echo "<i class='nvicon nvicon-locker'></i>$value";
										break;
								}
								echo "</div>";
							}
						}
						?>
					</div>
				</div>

				<div class="col rows gap-md">
					<h3>Storno podmínky</h3>
					<div class="iconset">
						<div class="icon">14 dní předem: 100% ceny zpět</div>
						<div class="icon">7 dní předem: 50% ceny zpět</div>
					</div>
				</div>
			</div>
		</div>


		<div class="padding-xl rows gap-md" id="map">
		
			<div class="space-around-hg">
				<h3>Adresa</h3>
				<span><?=$meta_fields["address"][0];?></span>
			</div>

			<iframe loading="lazy" width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?=urlencode($meta_fields["gps"][0]);?>+(Azzy)&amp;t=p&amp;z=8&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>

		</div>
	</div>


	<footer class="related-experiences" style="background:var(--secondary-light);">
		<div class="contentwrap padding-xl">
			<div class="cols-flex cols-md gap-md space-between space-around-hg">
				<h2 class="">Tipy na výlet v okolí</h2>
				<div class="tags inverted">
					<?= nv_c( "UI/tags", ["include" => [52, 29, 28, 46] ]);?>
				</div>
			</div>

			<?php
			$feed = nv_c( "tips/c/feed", [
				"limit" => 3,
				"post__in" => $meta_fields["related_tips"]
			] );
			?>
			<nv-repeat nv-inner-class="cols cols-sm-2 cols-md-3 gap-hg" nv-items="<?=esc_attr( json_encode( $feed["items"] ) );?>">
				<?= nv_t ( "tips/t/feed-item" ); ?>
			</nv-repeat>

		</div>
	</footer>











	<nv-modal class="fullwidth" aria-hidden="true" id="accomodation-modal-detail">
		<div>
			<header class="only-close-button">
				<a class="btn-close" nv-modal-close aria-hidden="true"></a>
			</header>
			<article>

				<header class="section space-around-xl cols cols-md-2 gap-lg padding-xl">
					<div class="col">
						<h1 class="space-around-hg"><?=$title?></h1>
						<p style="font-size: calc(var(--font-md) + 2px); color:var(--primary);"><?=$meta_fields["desc_long"][0];?></p>
					</div>
					<div class="col imgstretch"><?= nv_c ("UI/responsive-image", ["attachment_id" => $meta_fields["photo"][0] ] ) ?></div>
				</header>

				<?php
				$query = new WP_Query( array(
					"post_type" => "rooms",
					"orderby" => "post__in",
					"post__in" => $meta_fields["rooms"]
				));
				if ($query->have_posts()) :

					while ($query->have_posts()) :
						$query->the_post();
						$meta = get_post_meta($query->post->ID);
						?>
						<div class="section space-around-hg">
							<div class="details cols cols-sm-2 space-around-md gap-lg padding-xl">
								<div class="col">
									<h3><?=get_the_title();?></h3>
									<p><?=get_the_content();?></p>
								</div>
								<?php
								if (!empty ($meta["amenities"])):
									?>
									<div class="amenities col gap-hg space-around-md">
										<?php
										foreach ($meta["amenities"] as $amenity):
										?>
											<div class="amenity font-sm text-center">
												<i class="nvicon nvicon-amenity-<?=$amenity;?>"></i>
												<?= $amenities_array[$amenity];?>
											</div>
										<?php
										endforeach;
										?>
									</div>
								<?php
								endif;
								?>	
							</div>
							<div class="col">
								<?php
								if ( !empty( $meta["foto"] ) ):
									$gallery = nv_c( "UI/gallery", [
										"gallery" => $meta["foto"],
										"sizes_w" => "(min-width: 800px) 700px, 100vw",
										"sizes_h" => "(min-width: 800px) 350px, 50vw",
										"slider" => true
									] );
									?>
									<nv-gallery-slider nv-items="<?= nv_c_attr( $gallery["items"] );?>">
										<?= nv_t( "UI/t/gallery-item" );?>
									</nv-gallery-slider>
								<?php endif; ?>
								
							</div>
						</div>

					<?php
					endwhile;
					wp_reset_postdata();
				endif;
				?>
				
			</article>
		</div>
	</div>

	<nv-modal aria-hidden="true" id="accomodation-modal-contact">
		<div>
			<header>
				<h2>Kontaktovat hostitele</h2>
				<a class="btn-close" nv-modal-close aria-hidden="true"></a>
			</header>
			<article class="padding-hg">
				<form id="accomodation-contact-form" style="display: flex; flex-direction: column; gap: 15px">
					<label style="">
						<div>Jméno:</div>
						<input required class="input" name="name">
					</label>
					<label>
						<div>Email:</div>
						<input required class="input" name="email" type="email">
					</label>
					<label>
						<div>Dotaz:</div>
						<textarea required rows="5" class="input" name="message"></textarea>
					</label>
					<input type="hidden" name="host_name" value="<?php $host["first_name"][0]." ".$host["last_name"][0];?>">
					<input type="hidden" name="host_email" value="<?php $host["billing_email"][0]?>">
					
					<div class="messages nodisplay"></div>
					
					<a onclick="nvbk_accomodation_contact_form()" class="button" style="align-self:end">Odeslat</a>
				</form>
			</article>
		</div>
	</nv-modal>

</main>

<script>
	function nvbk_accomodation_contact_form ()
	{
		let messages = nv.modal["accomodation-modal-contact"].q(".messages")[0];
		let form = q("#accomodation-contact-form");
		
		jax.post( "/wp-admin/admin-ajax.php",
			{
				"action" : "accomodation/form-contact",
				"name" : form.q("input[name=name]")[0].value,
				"email" : form.q("input[name=email]")[0].value,
				"message" : form.q("textarea[name=message]")[0].value,
				"host_name" : form.q("input[name=host_email]")[0].value,
				"host_email" : form.q("input[name=host_email]")[0].value
			},
			(e) => {
				messages.display().messagebox("Zpráva úspěšně odeslána", "success", "success");
			},
			(e) => {
				messages.display().messagebox("Vyskytla se chyba", "error", "error");
				console.log(e);
			}
		);
	}
</script>
<?php
get_footer();