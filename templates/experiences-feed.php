<?php

function nv_template_experiences_feed ( $VAR = [] )
{
	$html = "";
	
	$wpargs = [
		'post_type' => 'zazitky',
		'post_status' => 'publish'
	];

	//add arguments to query, if they are provided
	if ( !empty($VAR["limit"]) )
		$wpargs["posts_per_page"] = $VAR["limit"];

	if ( !empty($VAR["tagfilter"]) )
		$wpargs['tax_query'][] = array( array(
			'taxonomy' => 'zazitky_tag',
			'field' => 'slug',
			'terms' => $VAR["tagfilter"]
		) );

	if ( !empty($VAR["categoryfilter"]) )
		$wpargs['tax_query'][] = array( array(
			'taxonomy' => 'zazitky_category',
			'field' => 'slug',
			'terms' => $VAR["categoryfilter"]
		) );

	if ( !empty($VAR["orderby"]) )
		$wpargs["orderby"] = $VAR["orderby"];

	if ( !empty($VAR["paged"]) )
		$wpargs["paged"] = $VAR["paged"];


	$query = new WP_Query( $wpargs );
	
	if( $query->have_posts() )
	{
		while( $query->have_posts() )
		{
			$query->the_post();

			$terms = get_the_terms($query->post, "zazitky_tag");
			if ( !empty( $terms ) )
			{
				$terms_icons = '';
				foreach ($terms as $term) {
					$terms_icons .= '<div class="icon nvicon nvicon-'.$term->slug.'"></div>';
				}
			}
			$img = nv_responsive_img( get_post_thumbnail_id($query->post->ID), "(min-width: 1px) 500px, 500px" );
			$link = get_post_permalink( get_the_ID() );

			$html .= <<<HTML

			<article class="feed-item" href="$link">
				<a href="$link" class="image"> $img </a>
				<header class="padding-lg">
					<a href="$link"><h2>{$query->post->post_title}</h2></a>
					<div class="secondary-text">{$query->post->location}</div>
				</header>
				<footer class="padding-lg">
					<div class="iconlist iconlist-hg">$terms_icons</div>
				</footer>
			</article>

			HTML;
		}

		wp_reset_postdata();

		return [
			"page" => $query->max_num_pages,
			"data" => $html
		];

	} else {
		return [
			"page" => 1,
			"data" => "No posts found"
		];
	}
}
