<?php
nv_use_modules(["booking", "lightbox"]);
global $_VAR;

$ID = get_the_id();
$meta_fields = get_post_meta( $ID );
$title = get_the_title();

$host = get_user_meta( $meta_fields["host"][0] );

$amenities_query = pods("amenities")->find(array("limit" => 100));
$amenities_array = array();
while( $amenities_query->fetch() ) {
	$amenities_array[(int)$amenities_query->display("id")] = $amenities_query->display("name");
}

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

		<a href="#" class="gallery-showmore openmodale button button-primary" data-modale="ubytovani-detaily"><i class="nvicon nvicon-grid"></i>Více</a>
	</div>

	<div class="main columns cols-flex cols-md gap-lg contentwrap">

		<div class="sections-left col">

			<div class="header box padding-xl">

				<h1>
					<?= get_the_title();?>
				</h1>
				<a class="secondary-text" style="text-decoration: none;" href="#map">
					<?=$meta_fields["address"][0];?>
				</a>	

				<div class="excerpt">
					<div><?= $meta_fields["desc_short"][0]; ?></div>

					<?php if(isset($meta_fields["desc_long"])): ?>
						<div class="description-hidden"><?= $meta_fields["desc_long"][0]; ?></div>
						<a class="button-showmore"
							onclick="q('.description-hidden').css('display', 'block'); this.hide()">
							Celý popis<i class="nvicon nvicon-arrow-right"></i>
						</a>
					<?php endif; ?>
				</div>

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
				<a class="button-showmore openmodale" data-modale="ubytovani-detaily">Detail ubytování<i class="nvicon nvicon-arrow-right"></i></a>
			</div>

		</div><!--.section-left-->



		<aside class="col reservation hidden">
			<div class="mobile-sliding-footer">
				
				<div class="content-on-shown space-between">
					<a class="btn-close" onclick="this.closestParent('aside').hide()"></a>
				</div>

				<div class="content-on-hidden">
					<a class="button" onclick="this.closestParent('aside').show()">rezervovat</a>
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

			<div class="modale" aria-hidden="true" data-modale="ubytovani-reviews">
				<div class="modal-dialog">
					<div class="modal-header">
						<h3>Recenze</h3>
						<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
					</div>
					<div class="modal-body padding-lg">
					<?php

					$pod = pods("ubytovani-reviews");
					$query = new WP_Query( array(
						"post_type" => "ubytovani-reviews",
						"order" => "DESC",
						"orderby" => "menu_order date",
						"meta_query" => array(
							"key" => "ubytko",
							"value" => $ID
						)
					));

					if ($query->have_posts()) {

						$counter = 0;
						$first3 = "";

						while ($query->have_posts()) {

							$query->the_post();
							$meta = get_post_meta(get_the_id());
							$avatarImg = get_post_thumbnail_id();
							if (empty($avatarImg)) $avatarImg = 1364;

							if ($meta["ubytko"][0] != $ID) continue;

							$pod->fetch(get_the_id());

							$r = '
							<div class="review">
								<div class="review-head">
									<div class="review-avatar">'.nv_responsive_img($avatarImg, "(min-width:1px) 64px, 64px").'</div>
									<div>
										<h3 class="review-name">'.get_the_title().'</h3>
										<div class="review-source">'.$pod->display("zdroj").'</div>
									</div>
								</div>
								<div class="review-body">
									<div class="review-text">'.$pod->display("recenze").'</div>
								</div>
							</div>';
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
			<a class="button-showmore openmodale" data-modale="ubytovani-reviews">Všechny recenze (<?=$counter;?>)<i class="nvicon nvicon-arrow-right"></i></a>
		</div>

		<div class="box info cols cols-md-3 cols-sm-2 padding-xl gap-lg">

			<div class="col">
				<!--h3>Hostitel</h3-->
				<div class="cols-fixed-flex">
					<div class="col-fixed avatar avatar-big">
						<?= nv_responsive_img( $host["profile_picture"][0], "(min-width: 1px) 64px, 64px" ); ?>
					</div>

					<div class="col-flex">
						<h3><?= $host["first_name"][0] . " " . $host["last_name"][0]; ?></h3>
						<div class="iconset">
							<div class="icon">
								<i class="nvicon nvicon-phone"></i>
								<span class="hidden"></span>
								<a class="link" onclick="this.parentElement.q('span').show().content('<?= $host["billing_phone"][0];?>'); this.remove();">Zobrazit</a>
							</div>
							<div class="icon">
								<i class="nvicon nvicon-email"></i>
								<?= $host["billing_email"][0];?>
							</div>
						</div>
						<a class="button button-icon button-plain button-sm openmodale" data-modale="ubytovani-contact" style="width:fit-content;">
							<i class="nvicon nvicon-write"></i>Rychlý dotaz
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


		<div class="padding-xl" id="map">
		
			<h3>Adresa</h3>
		
			<div style="margin-bottom:1em"><?=$meta_fields["address"][0];?></div>
			<iframe loading="lazy" width="100%" height="600" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=<?=urlencode($meta_fields["gps"][0]);?>+(Azzy)&amp;t=p&amp;z=8&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>

		</div>
	</div>






	<div class="modale fullwidth" aria-hidden="true" data-modale="ubytovani-detaily">
		<div class="modal-dialog">
			<div class="modal-header">
				<h2>Detail ubytování</h2>
				<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
			</div>
			<div class="modal-body">
				<?php
				$query = new WP_Query( array(
					"post_type" => "ubytovani-rooms",
					//"orderby" => "menu_order",
					"meta_query" => array(
						array(
							"key" => "ubytovani",
							"value" => $ID
						)
					)
				));
				if ($query->have_posts()) :
					while ($query->have_posts()) :
						$query->the_post();
						$meta = get_post_meta($query->post->ID);
						?>
						<div class="section space-around-hg">
							<div class="details cols cols-sm-2 space-around-md gap-lg padding-lg">
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
				
			</div>
		</div>
	</div>

	<div class="modale" aria-hidden="true" data-modale="ubytovani-contact">
		<div class="modal-dialog">
			<div class="modal-header">
				<h2>Kontaktovat hostitele</h2>
				<a href="#" class="btn-close closemodale" aria-hidden="true"></a>
			</div>
			<div class="modal-body padding-lg">
				<form id="ubytovani-contact-form" action="/wp-admin/admin-ajax.php" method="POST" style="display: flex; flex-direction: column; gap: 15px">
					<label style="">
						<div>Jméno:</div>
						<input class="input" name="name">
					</label>
					<label>
						<div>Email:</div>
						<input class="input" name="email" type="email">
					</label>
					<label>
						<div>Dotaz:</div>
						<textarea rows="5" class="input" name="message"></textarea>
					</label>
					<input type="hidden" name="action" value="nvbk_ubytovani_contact_form">
					<a onclick="nvbk_ubytovani_contact_form()" class="button" style="align-self:end">Odeslat</a>
				</form>
			</div>
		</div>
	</div>

<!-- 	<div class="modale" aria-hidden="true" data-modale="ubytovani-terms">
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
	function nvbk_ubytovani_contact_form () {
		var form = jQuery("form#ubytovani-contact-form");
		jQuery.ajax({
		    url: form.attr("action"),
		    type: form.attr("method"),
		    data: form.serialize(),
		    success: (e) => console.log(e)
		});
	}
</script>
<?php
get_footer();