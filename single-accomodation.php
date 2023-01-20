<?php
$NV_MODULES = [
	"UI/gallery",

	"Booking/lib",
	"Booking/form",

	"Accomodation/feed",
 
	"Experiences/feed",
	"Experiences/tags"
];
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

$nv_vars = array(
	'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	'host_email' => $host["billing_email"][0],
	'apartmentId' => $ID,
	'apartmentName' => $title,
	'apartmentCapacity' => $meta_fields["capacity"][0],
	'meta' => $meta_fields
);

get_header();
?>
<main id="primary">

	<div class="section-gallery">

		<?php
		echo nv_template_gallery( array(
			"gallery" => $meta_fields["gallery"],
			"sizes_w" => "(min-width: 800px) 700px, 100vw",
			"sizes_h" => "(min-width: 800px) 350px, 50vw",
			"slider" => true
		));
		?>

		<a href="#" class="gallery-showmore openmodale button button-plain" data-modale="accomodation-detail">Více<i class="nvicon nvicon-grid"></i></a>
	</div>

	<div class="main columns cols-flex cols-md gap-lg contentwrap">

		<div class="sections-left col">

			<div class="box padding-xl gap-lg rows">

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
					<a class="button button-icon button-plain openmodale" data-modale="accomodation-detail">Detail ubytování <i class="nvicon nvicon-arrow-right"></i></a>
				</div>
			</div>

		</div><!--.section-left-->



		<aside class="col reservation">
			<div class="mobile-sliding-footer">
				
				<div class="content-on-shown space-between">
					<a class="btn-close" onclick="this.closestParent('aside').removeClass('slided')"></a>
				</div>

				<div class="content-on-hidden">
					<a class="button" onclick="this.closestParent('aside').addClass('slided')">rezervovat</a>
				</div>

			</div>

			<?= nv_template_booking_form(array(
				"iss" => true,
				"apartmentId" => (int)$meta_fields["calendar_id"][0],
				"apartmentName" => $title,
				"capacity" => (int)$meta_fields["capacity"][0]
			)); ?>
		</aside>



	</div>
	<div class="main contentwrap">
		<div class="reviews padding-xl">

			<h3>Recenze</h3>

			<div class="modale" aria-hidden="true" data-modale="accomodation-reviews">
				<div class="modal-dialog">
					<div class="modal-header only-close-button">
						<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
					</div>
					<div class="modal-body padding-lg">
					<?php

					$pod = pods("reviews");
					$query = new WP_Query( array(
						"post_type" => "reviews",
						"post__in" => $meta_fields["reviews"],
						"orderby" => "post__in"
					));

					if ($query->have_posts()) {

						$counter = 0;
						$first3 = "";

						while ($query->have_posts()) {

							$query->the_post();
							$meta = get_post_meta(get_the_id());

							$t = get_the_title();
							$s = $pod->display("source");
							$c = $pod->display("text");

							$avatarId = get_post_thumbnail_id();
							if (empty($avatarId)) $avatarId = 1364;
							$avatarImg = nv_responsive_img( $avatarId, "(min-width:1px) 64px, 64px");
							
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

					</div>
				</div>
			</div>
			
			<div class="reviews-container">
				<?=$first3;?>
			</div>
			<div class="cols-flex" style="justify-content: center;"><a class="button button-plain openmodale" data-modale="accomodation-reviews">Všechny recenze (<?=$counter;?>)</a></div>
		</div>

		<div class="box info cols cols-md-3 cols-sm-2 padding-xl gap-lg">

			<div class="col">
				<!--h3>Hostitel</h3-->
				<div class="cols-flex gap-hg">
					<div class="avatar avatar-big">
						<?= nv_responsive_img( $host["profile_picture"][0], "(min-width: 1px) 64px, 64px" ); ?>
					</div>

					<div class="col rows gap-md">
						<h3><?= $host["first_name"][0] . " " . $host["last_name"][0]; ?></h3>
						<div class="iconset">
							<div class="icon">
								<i class="nvicon nvicon-phone"></i>
								<span class="nodisplay"></span>
								<a class="link" onclick="this.parentElement.q('span').removeClass('nodisplay').content('<?= $host["billing_phone"][0];?>'); this.remove();">Zobrazit</a>
							</div>
							<div class="icon">
								<i class="nvicon nvicon-email"></i>
								<a href="mailto:<?= $host["billing_email"][0];?>"><?= $host["billing_email"][0];?></a>
							</div>
						</div>
						<a class="button button-icon button-plain button-sm openmodale" data-modale="accomodation-contact" style="width:fit-content;">
							Rychlý dotaz <i class="nvicon nvicon-write"></i>
						</a>
					</div>

				</div>

				
			</div>

			<div class="col">
				<h3>Důležité informace</h3>
				<div class="iconset">
					<?php
					$additional_information = array(
						"pets" => "Vhodné pro pejsky",
						"self_checkin" => "Self Check-in",
						"checkin" => "Check-in",
						"checkout" => "Check-out",
						"kids" => "Vhodné pro děti",
						"parking" => "Parkování na pozemku",
						"parties" => "Vhodné pro oslavy a večírky"
					);
					foreach($additional_information as $key => $value) {
						if ( isset($meta_fields[$key][0]) && $meta_fields[$key][0] ) {
							echo "<div class='icon'>";
							switch ( $key ) {
								case "kids" :
									echo "<i class='nvicon nvicon-pets'></i>Pejsci vítáni";
									break;
								case "kids" :
									echo "<i class='nvicon nvicon-parking'></i>Pozemek s parkováním";
									break;
								case "kids" :
									echo "<i class='nvicon nvicon-kids'></i>Vhodné pro děti";
									break;
								case "checkin" :
									echo "<i class='nvicon nvicon-in'></i>Checkin: ".$meta_fields[$key][0];
									break;
								case "checkout" :
									echo "<i class='nvicon nvicon-out'></i>Checkout: ".$meta_fields[$key][0];
									break;
								case "self_checkin" :
									echo "<i class='nvicon nvicon-locker'></i>Self check-in";
									break;
								case "other" :
									echo $value;
									break;
							}
							echo "</div>";
						}
					}
					?>
				</div>
			</div>

			<div class="col">
				<h3>Storno podmínky</h3>
				<ul>
					<li>14 dní předem: 100% ceny zpět</li>
					<li>7 dní předem: 50% ceny zpět</li>
				</ul>
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
			<div class="cols-flex space-between space-around-hg">
				<h2 class="">Populární v okolí</h2>
				<div class="tags">
					<?= nv_template_experiences_tags(["include" => [52, 29, 28, 46]]);?>
				</div>
			</div>

			<div class="cols cols-sm-2 cols-md-3 gap-hg">
				<?php
				echo nv_template_experiences_feed( [
					"limit" => 3,
					"orderby" => "rand",
				] )["data"];
				?>
			</div>

		</div>
	</footer>







	<div class="modale fullwidth" aria-hidden="true" data-modale="accomodation-detail">
		<div class="modal-dialog">
			<nav class="modal-header only-close-button">
				<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
			</nav>
			<article class="modal-body">

				<header class="section space-around-xl cols cols-md-2 gap-lg padding-xl">
					<div class="col">
						<h1 class="space-around-hg"><?=$title?></h1>
						<p style="font-size: calc(var(--font-md) + 2px); color:var(--primary);"><?=$meta_fields["desc_long"][0];?></p>
					</div>
					<div class="col imgstretch"><?= nv_responsive_img( $meta_fields["photo"][0] ) ?></div>
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
								if (!empty ($meta["foto"])) {
									echo nv_template_gallery( array(
										"gallery" => $meta["foto"],
										"sizes_w" => "(min-width: 800px) 700px, 100vw",
										"sizes_h" => "(min-width: 800px) 350px, 50vw",
										"slider" => true
									));
								}
								?>
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

	<div class="modale" aria-hidden="true" data-modale="accomodation-contact">
		<div class="modal-dialog">
			<div class="modal-header">
				<h2>Kontaktovat hostitele</h2>
				<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
			</div>
			<div class="modal-body padding-lg">
				<div class="messagebox hidden"></div>
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
					<a onclick="nvbk_accomodation_contact_form()" class="button" style="align-self:end">Odeslat</a>
				</form>
			</div>
		</div>
	</div>

<!-- 	<div class="modale" aria-hidden="true" data-modale="accomodation-terms">
		<div class="modal-dialog">
			<div class="modal-header">
				<h2>Podmínky rezervace</h2>
				<a href="#" class="btn-close closemodale" aria-hidden="true">&times;</a>
			</div>
			<div class="modal-body">
				<p>
					Storno atd.
				</p>
			</div>
		</div>
	</div> -->

</main>

<script>
	function nvbk_accomodation_contact_form () {
		let messagebox = q(".modale[data-modale=accomodation-contact] .messagebox")[0];

		var form = q("form#accomodation-contact-form");
		jax.post( "/wp-admin/admin-ajax.php", {
			"action" : "nvbk_accomodation_contact_form",
			"name" : form.q("input[name=name]")[0].value,
			"email" : form.q("input[name=email]")[0].value,
			"message" : form.q("textarea[name=message]")[0].value,
			"host_email" : nv_vars.host_email
		},
		(e) => {
			messagebox.show().content("Zpráva úspěšně odeslána");
		}
		);
	}
</script>
<?php
get_footer();