<?php
function nv_calculate_img_source ( $id, $orientation )
{
	$source = [];
	$height = 0;
	// Loop through all the available image sizes
	foreach ( get_intermediate_image_sizes() as $size )
	{
	    // Get the image size information
	    $image_src = wp_get_attachment_image_src( $id, $size );
	    if ( empty( $image_src ) ) continue;

	    $url = $image_src[0];
	    $width = absint( $image_src[1] * ( $orientation === "landscape" ? 0.8 : 0.7 ) );
	    $height = $image_src[2];

	    $source[] = "{$url} {$width}w";
	}
	$srcset = implode( ", ", $source );
	return 
	    "<source
	    	srcset='{$srcset}'
	    	media='(orientation: {$orientation})'
	    	sizes='(min-width: 1px) 100vw, 100vw'
	    ></source>";
}


nv_new_c (
	"UI/cover-image",
	function ( $VAR )
	{
		$VAR = array_merge( [
			"heading" => "",
			"subheading" => "",
			"content" => "",
			"attachment_id" => 1,
			"attachment_id_portrait" => 0,
		], $VAR );
		$VAR["heading"] = $VAR["heading"] ? "<h1>{$VAR["heading"]}</h1>" : "";
		$VAR["subheading"] = $VAR["subheading"] ? "<h5>{$VAR["subheading"]}</h5>" : "";


		$sources = nv_calculate_img_source ( $VAR["attachment_id"], "landscape" );
		$sources_portrait = $VAR["attachment_id_portrait"] ? nv_calculate_img_source( $VAR["attachment_id_portrait"], "portrait" ) : "";

		$img = nv_c( "UI/responsive-image", [ "attachment_id" => $VAR["attachment_id"], "sizes" => "(min-width: 1px) 100vh, 100vh", "nonresponsive" => true ] );


		return <<<HTML

		<div class="section-cover-image">
			<div class="cover-image">
				<picture>
					{$sources}
					{$sources_portrait}
					{$img}
				</picture>
			</div>
			<div class="cover-content rows gap-md">
				{$VAR["heading"]}
				{$VAR["subheading"]}
				{$VAR["content"]}
			</div>
		</div>
		
		HTML;
	}
);
?>