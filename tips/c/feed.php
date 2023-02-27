<?php


/*

$VAR [
	int $limit
	str $tags - "tag1, tag2"
	str $orderby
	int $paged
	int[] $post__in
]

*/

nv_new_c (
	"tips/c/feed",
	function ( $VAR )
	{
		$VAR = array_merge( [
			"limit" => 9,
			"orderby" => "date",
			"paged" => 1
		], $VAR );

		$items = [];
		$wpargs = [
			'post_type' => 'tips',
			'post_status' => 'publish',
			'posts_per_page' => $VAR['limit'],
			'orderby' => $VAR['orderby'],
			'paged' => $VAR['paged']
		];

		//add arguments to query, if they are provided

		if ( !empty($VAR["tags"]) )
			$wpargs['tax_query'] = array( array(
				'taxonomy' => 'tips_tags',
				'field' => 'slug',
				'terms' => explode( ",", $VAR["tags"] )
			) );

	
		if ( !empty($VAR["post__in"]) )
			$wpargs["post__in"] = $VAR["post__in"];



		$query = new WP_Query( $wpargs );
		
		if( $query->have_posts() )
		{
			while( $query->have_posts() )
			{
				$query->the_post();

				$item = [
					"tags" => get_the_terms( $query->post, "tips_tags" ),
					"img" => nv_c( "UI/responsive-image", [
						"attachment_id" => get_post_thumbnail_id( $query->post->ID ),
						"sizes" => "(min-width: 1px) 500px, 500px"
					] ),
					"link" => get_post_permalink( $query->post->ID ),
					"title" => $query->post->post_title,
					"location" => $query->post->location,
				];
				array_push( $items, $item );

				//print_r($item);
			}

			wp_reset_postdata();

			return [
				"status" => 0,
				"query" => $wpargs,
				"more" => ( (int)$query->max_num_pages > (int)$VAR["paged"] ) ? 1 : 0,
				"items" => $items
			];
		}
		else
		{
			return [
				"status" => 1,
				"query" => $wpargs,
				"more" => 0,
				"items" => []
			];
		}
	}
);