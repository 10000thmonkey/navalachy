<?php

nv_new_c (
	"blog/c/feed",
	function ( $VAR = [] )
	{
		$VAR = array_merge( [
			"limit" => 10
		], $VAR );

		$args = [
			"post_type" => "blog",
			"limit" => $VAR["limit"]
		];
		$posts = new WP_Query( $args );

		if ( $posts->post_count === 0 )
			return [ "status" => 1, "data" => $posts ];

		$items = [];

		foreach ( $posts->posts as $post )
		{
			$items[] = [
				"title" => $post->post_title,
				"excerpt" => $post->post_content,
				"img" => nv_c( "UI/responsive-image", [ "attachment_id" => get_post_thumbnail_id( $post->ID ) ] )
			];
		}

		return [
			"status" => 0,
			"data" => $posts->posts,
			"items" => $items,
		];
	}
);