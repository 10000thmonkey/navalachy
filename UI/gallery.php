<?php

/* provide array of Attachment IDs, component will handle rest

$VAR [
	int[] $gallery
	str $sizes_w
	str $sizes_h
	bool $slider

]

*/

nv_new_c (
	"UI/gallery",
	function ( $VAR )
	{
		$VAR["slider"] = isset($VAR["slider"]) ? $VAR["slider"] : false;


		$args = array( 
		    'post_type' => 'attachment', 
		    'posts_per_page' => -1, 
		    'post_status' => 'any', 
		    'post__in' => $VAR["gallery"],
		    "orderby" => "post__in"
		); 
		$gallery = get_posts($args);

		$items = [];

	    $sizes_w =
	    	! empty( $VAR["sizes_w"] ) ?
	    	$VAR["sizes_w"] :
	    	"(min-width: 1px) 50vw, 50vw";
	    $sizes_h =
	    	! empty( $VAR["sizes_h"] ) ?
	    	$VAR["sizes_h"] :
	    	"(min-width: 1px) 50vw, 50vw";

		foreach ( $gallery as $item )
		{
			$src = wp_get_attachment_image_src( $item->ID, "full" );
		    $sizes = $src[1] > $src[2] ? $sizes_w : $sizes_h;

			$items[] = [
				"title" => $item->post_title,
				"alt" => $item->post_title,
				"src" => $src[0],
				"srcset" => wp_get_attachment_image_srcset( $item->ID ),
				"sizes" => $sizes,
				"orientation" => $src[1] > $src[2] ? " w" : " h",
			];
		}

		return [ "items" => $items ];
	}
);