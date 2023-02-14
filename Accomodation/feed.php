<?php
/*
$VAR [
	str[] range ( begin, end )
	int[] apartments
	bool hovercards
]
*/
function nv_template_accomodation_feed ( $VAR )
{
	$args = [];
	$emptyQuery = false;
	$html = "";

	global $nvbk;

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

	$arguments = array_merge( array( 'post_type' => 'accomodation' ), $args );
	$query = new WP_Query( $arguments );

	if ( ! $emptyQuery && $query->have_posts() )
	{

		//get array of rates of all apartments, next monday price wont be so high
		//$remote_rates_day = date( "Y-m-d", strtotime("next Monday") );

		while ( $query->have_posts() )
		{ 
			$query->the_post();

			$id = $query->post->ID;
			$meta = get_post_meta( $id );

			$img = nv_responsive_img( get_post_thumbnail_id( $id ) );

			$link = get_permalink( $query->post );


			if ( !empty( $VAR["hovercards"] ) )
			{
				$html .= <<<HTML

				<a class="hovercard" href="$link">
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
				if ( !empty( $VAR["range"] ) ) 
				{
					$link .= "?" . http_build_query( array(
						"begin" => $VAR["begin"],
						"end" => $VAR["end"]
					) );
				}


				$info = [];
				if ( isset($meta["wifi"][0]) && $meta["wifi"][0] == 1)
					$info["wifi"] = "Wi-fi";
				if ( isset($meta["pets"][0]) && $meta["pets"][0] == 1 )
					$info["pets"] = "Pejsci vítáni";
				if ( isset($meta["parties"][0]) && $meta["parties"][0] == 1 )
					$info["party"] = "Vhodné pro večírky";
				if ( isset($meta["car"][0]) && $meta["parking"][0] == 1 )
					$info["parking"] = "Parkování na pozemku";

				$details2 = "";
				foreach ( $info as $key => $value)
				{
					$details2 .= <<<HTML
					<div class="icon"><i class="nvicon nvicon-$key"></i></div>
					HTML;
				}

				$pricing = $nvbk->get_new_booking_price( $id );

				$rate = <<<HTML
				od<span style="font-size: var(--font-hg);color:var(--primary);">&nbsp;{$pricing["price_final"]},-</span>/&nbsp; noc
				HTML;

				$html .= <<<HTML

				<article class="feed-item">
					<a href="$link" class="image">
						$img
						<section class="padding-lg iconlist" style="">
							$details2
						</section>
					</a>
					<header class="padding-lg">
						<a href="$link"><h2 class="nomargin">{$query->post->post_title}</h2></a>
						<div class="secondary-text">{$meta['address'][0]}</div>
					</header>
					<section class="padding-lg iconset iconset-hg cols cols-2">
						<div class="col icon">
							<i class="nvicon nvicon-group"></i>
							<span><b>{$meta["capacity"][0]}</b> lidí</span>
						</div>
						<div class="col icon">
							<i class="nvicon nvicon-beds"></i>
							<span><b>{$meta["bedroom"][0]}</b> ložnice</span>
						</div>
					</section>
					<footer class="padding-lg">
						<div class="price">$rate</div>
						<a class="button nomargin" href="$link?show=reservation">Rezervovat</a>
					</footer>
				</article>

				HTML;
			}
		}
	}
	//print_r( $response );

	if ( $emptyQuery ) echo 'Pro tento termín není dostupné žádné ubytování.';
	else echo $html;

}