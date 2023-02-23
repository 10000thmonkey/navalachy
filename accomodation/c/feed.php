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
		$VAR = array_merge( [
			"page" => 1,
			"apartments" => []
		], $VAR );


		include_once(get_template_directory(). "/accomodation/i/lib.php");
		global $nvbk;


		$args = [];
		$emptyQuery = false;
		$html = "";


		if ( !empty( $VAR["limit"] ) )
			$args["posts_per_page"] = $VAR["limit"];

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

		$arguments = array_merge( [
			'post_type' => 'accomodation',
		], $args );
		$query = new WP_Query( $arguments );

		if ( ! $emptyQuery && $query->have_posts() )
		{
			$items = [];
			//get array of rates of all apartments, next monday price wont be so high
			//$remote_rates_day = date( "Y-m-d", strtotime("next Monday") );

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


				if ( !empty( $VAR["hovercards"] ) )
				{
					$html .= <<<HTML

					<a class="hovercard" href="$permalink">
						<!--div class="icon nvicon"></div-->
						$img
						<div class="label">
							<div>{$query->post->post_title}</div>
							<div class="secondary-text">{$meta["address"][0]}</div>
						</div>
					</a>
					HTML;
				}
				else
				{
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
			}
			return [
				"status" => 0,
				"items" => $items,
				"more" => ( (int)$query->max_num_pages > (int)$VAR["page"] ) ? 1 : 0,
			];
		}
		//print_r( $response );

		if ( $emptyQuery ) echo 'Pro tento termín není dostupné žádné ubytování.';
		else echo $html;

	}
);