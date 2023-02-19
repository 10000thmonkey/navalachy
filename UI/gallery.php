<?php
//provide array of Attachment IDs, component will handle rest

function nv_template_gallery( $VAR ) {

$templ_dir = get_template_directory_uri();
wp_enqueue_script( "lightbox", $templ_dir . "/assets/lightbox/lightbox.min.js", ["jquery"] );
wp_enqueue_style( "lightbox", $templ_dir . "/assets/lightbox/lightbox.min.css");

$VAR["slider"] = isset($VAR["slider"]) ? $VAR["slider"] : false;
$output = "";
ob_start();
?>

<div class="gallery">	
	
	<div class='gallery-<?= ( $VAR["slider"] ? "slider" : "grid" ); ?>'>
	
	<?php

	$args = array( 
	    'post_type' => 'attachment', 
	    'posts_per_page' => -1, 
	    'post_status' => 'any', 
	    'post__in' => $VAR["gallery"],
	    "orderby" => "post__in"
	); 
	$gallery = get_posts($args);

	foreach ($gallery as $image)
	{
		$attr_title = $image->post_title;
		$attr_alt = $attr_title;
		$attr_src = wp_get_attachment_image_src( $image->ID, "medium" );
	    $attr_srcset = wp_get_attachment_image_srcset( $image->ID, 'large' );
	    $attr_sizes_w =
	    	isset( $VAR["sizes_w"] ) ?
	    	$VAR["sizes_w"] :
	    	"(min-width: 1px) 50vw, 50vw";
	    $attr_sizes_h =
	    	isset( $VAR["sizes_w"] ) ?
	    	$VAR["sizes_w"] :
	    	"(min-width: 1px) 50vw, 50vw";
	    echo
	    '<a href="'. esc_attr($attr_src[0]) .'" data-alt="'.esc_attr($attr_alt).'" data-title="'.esc_attr($attr_title).'" title="'.esc_attr($attr_title).'" data-lightbox="gallery-more" class="gallery-image'.( $attr_src[1] > $attr_src[2] ? " w" : " h" ).'">
		    <img src="'.esc_attr( $attr_src[0] ).'"
		        srcset="'.esc_attr( $attr_srcset ).'"
		        sizes="'.esc_attr( $attr_src[1] > $attr_src[2] ? $attr_sizes_w : $attr_sizes_h ).' "
		        alt="'.esc_attr( $attr_alt ).'"
		        loading="lazy"/>
		</a>';
	}
	?>

	</div>
</div>

<?php
return ob_get_clean();
}
?>