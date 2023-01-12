<?php /* Template Name: NV/Accomodation/Archive */
nv_use_modules(["booking"]);

get_header();

$meta_fields = get_post_meta( get_the_ID() );

$apartamentos = get_transient("nvbk_apartamentos");
if(empty($apartamentos)) {
	$query = new WP_Query(array(
		"post_type" => "ubytovani",
	));
	$apartamentos = [];
	while ($query->have_posts()) {
		$query->the_post();
		$cal_id = get_post_meta($query->post->ID, "calendar_id");
		if ($cal_id[0] != "1") $apartamentos[] = (int)$cal_id[0];
	}
	set_transient("nvbk_apartamentos", $apartamentos, DAY_IN_SECONDS );
}

$is_searching = ( !empty($_GET["begin"]) && !empty($_GET["end"]) );
?>
<main id="primary">

	<?php
	echo nv_template_cover_image( array(
		"attachment" => get_post_thumbnail_id( get_the_id() ),
		"heading" => $meta_fields["heading"][0],
		"subheading" => $meta_fields["subheading"][0],
		"content" => nv_template_booking_form(array("iss" => false))
	) );
	?>
	


	<div class="ubytovani-feed contentwrap">
	
	<?php
	$args = []; 
	$emptyQuery = false;


	//search for available apartmentsif checking for a date range availability

	if ( $is_searching )
	{
		$response = $nvbk->get_availability( $_GET['begin'], $_GET['end'], $apartamentos );

		if ( !empty($response["availableApartments"]) )
		{
			$args['meta_query'] = array(
				array (
					'key' => 'calendar_id',
					'value' => $response["availableApartments"]
				)
			);
		} else {
			$emptyQuery = true;
			echo "<pre>".print_r($response)."</pre>";
		}
	} else {

	}


	$arguments = array_merge( array( 'post_type' => 'ubytovani'	), $args);
	$query = new WP_Query( $arguments );
	

	//get array of rates of all apartments, next monday price wont be so high
	$remote_rates_day = date( "Y-m-d", strtotime("next Monday") );
	$remote_rates = $nvbk->get_rates( $remote_rates_day, $remote_rates_day, $apartamentos );	

	// THE LOOP
	if( $query->have_posts() && !$emptyQuery )
	{
		$posts = [];

		while( $query->have_posts() )
		{
			$query->the_post();
			$id = $query->post->ID;
			$posts[$id] = array (
				"feat_image" => get_post_thumbnail_id( $id ),
				"post_link" => get_post_permalink( $id ),
				"meta_fields" => get_post_meta( $id ),
				"title" => get_the_title(),
			);

			$info = [];
			if ( isset($posts[$id]["meta_fields"]["capacity"][0]) )
				$info["group"] = $posts[$id]["meta_fields"]["capacity"][0] . " osob";
			if ( isset($posts[$id]["meta_fields"]["bedroom"][0]) )
				$info["beds"] = $posts[$id]["meta_fields"]["bedroom"][0] . " ložnice";
			if ( isset($posts[$id]["meta_fields"]["wifi"][0]) && $posts[$id]["meta_fields"]["wifi"][0] == 1)
				$info["wifi"] = "Wi-fi";
			if ( isset($posts[$id]["meta_fields"]["pets"][0]) && $posts[$id]["meta_fields"]["pets"][0] == 1 )
				$info["pets"] = "Pejsci vítáni";
			if ( isset($posts[$id]["meta_fields"]["parties"][0]) && $posts[$id]["meta_fields"]["parties"][0] == 1 )
				$info["party"] = "Vhodné pro večírky";
			if ( isset($posts[$id]["meta_fields"]["car"][0]) && $posts[$id]["meta_fields"]["parking"][0] == 1 )
				$info["parking"] = "Parkování na pozemku";
			$posts[$id]["info"] = $info;

		}

		foreach ( $posts as $post )
		{ 

			if ( $is_searching ) {
				$link = $post["post_link"] . "?" . http_build_query( array(
					"begin" => $_GET["begin"],
					"end" => $_GET["end"]
				) );
			} else {
				$link = $post["post_link"];
			}
			?>
			<article class="feed-item">
				<a href="<?= $link;?>" class="image">
					<?= nv_responsive_img( $post["feat_image"] ); ?>
				</a>
				<div class="head">
					<a href="<?= $link;?>"><h2><?= $post["title"]; ?></h2></a>
					<div class="secondary-text"><?= isset($post["meta_fields"]["address"][0]) ? $post["meta_fields"]["address"][0] : ""; ?></div>
				</div>
				<div class="body">
					<?php foreach ($post["info"] as $key => $value) : ?>
						<div class="detail">
							<div class="nvicon nvicon-<?=$key;?>"></div><?=$value;?>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="foot">
					<div class="price">
						<?php
						if ( $post["meta_fields"]["calendar_id"][0] != 1 &&
							 array_key_exists( (int)$post["meta_fields"]["calendar_id"][0], $remote_rates["data"] ) )
							echo "od " . $remote_rates["data"][$post["meta_fields"]["calendar_id"][0]][$remote_rates_day]["price"] . ", Kč / noc";
						?>
					 </div>
					<a class="button" href="<?= $link . "#booking-form";?>">Rezervovat</a>
				</div>
			</article>
		<?php
		}
		wp_reset_postdata();

	} else {
		$emptyQuery = true;
	}
	
	if($emptyQuery) echo 'Pro tento termín není dostupné žádné ubytování.';
	?>
	</div>
</main>
<?php
get_footer();