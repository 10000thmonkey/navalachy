<?php
/*
$VAR [
	str[] range ( begin, end )
	int[] apartments
	int limit 
	bool hovercards
]
*/
nv_new_c (
	"accomodation/c/feed",
	function ( $VAR )
	{
		include_once(get_template_directory(). "/accomodation/i/lib.php");
		global $nvbk;
	
		$VAR = array_merge( [
			"page" => 1,
			"apartments" => [],
			"limit" => 9,
		], $VAR );
	
		$args = [
			"post_type" => "accomodation",
			"posts_per_page" => $VAR["limit"],
		];
	
		$emptyQuery = false;

		if ( !empty( $VAR["range"] ) )
		{
			$response = $nvbk->get_available_apartments( $VAR['range']['begin'], $VAR['range']['end'], $VAR["apartments"] );

			if ( !empty($response) )
			{
				$args['post__in'] = $response;
			} else {
				$emptyQuery = true;
			}
		}

		$query = new WP_Query( $args );

		if ( ! $emptyQuery && $query->have_posts() )
		{
			$items = [];

			while ( $query->have_posts() )
			{ 
				$query->the_post();

				$id = $query->post->ID;
				$meta = get_post_meta( $id );

				$img = nv_c( "UI/responsive-image", [
					"attachment_id" => get_post_thumbnail_id( $id ),
					"sizes" => "(min-width: 1px) 800px"
				] );

				$permalink = get_permalink( $query->post );

				$link_params = [];
				if ( !empty( $VAR["range"] ) ) 
				{
					$link_params["begin"] = $VAR["range"]["begin"];
					$link_params["end"] = $VAR["range"]["end"];
				}

				$info = [];
				if ( isset($meta["wifi"][0]) && $meta["wifi"][0] == 1)
					$info[] = ["key"=>"wifi", "name"=> "Wi-fi"];
				if ( isset($meta["pets"][0]) && $meta["pets"][0] == 1 )
					$info[] = ["key"=>"pets", "name"=>"Pejsci vítáni"];
				if ( isset($meta["parties"][0]) && $meta["parties"][0] == 1 )
					$info[] = ["key"=>"party", "name" => "Vhodné pro večírky"];
				if ( isset($meta["car"][0]) && $meta["parking"][0] == 1 )
					$info[] = ["key" => "parking", "name" => "Parkování na pozemku"];

				array_push( $items, [
					"title" => $query->post->post_title,
					"img" => $img,
					"link" => "$permalink?" . http_build_query( $link_params ),
					"link_reserv" => "$permalink?" . http_build_query( array_merge( $link_params, ["show" => "reservation"] ) ),
					"capacity" => $meta["capacity"][0],
					"bedroom" => $meta["bedroom"][0],
					"details" => $info,
					"price_final" => $nvbk->get_new_booking_price( $id )["price_final"],
					"address" => $meta["address"][0]
				] );
			}
			return [
				"status" => 0,
				"items" => $items,
				"more" => ( (int)$query->max_num_pages > (int)$VAR["page"] ) ? 1 : 0,
			];
		}
		//print_r( $response );

		if ( $emptyQuery ) return [
			"status" => 1,
			"more" => 0,
			"items" => [],
			"message" => 'Pro tento termín není dostupné žádné ubytování.'
		];
	}
);