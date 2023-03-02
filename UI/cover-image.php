<?php
function nv_calculate_img_source ( $id, $orientation )
{
	$source = [];
	// Loop through all the available image sizes
	foreach ( get_intermediate_image_sizes() as $size )
	{
	    // Get the image size information
	    $image_src = wp_get_attachment_image_src( $VAR["attachment_id"], $size );
	    $url = $image_src[0];
	    $width = $image_src[1];
	    $height = $image_src[2];

	    $source[] = "<source srcset='{$url}' media='(max-height: {$height}) and (orientation: {$orientation})'></source>";
	}
	return implode( "\n", $source );
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


		$sources = nv_calculate_img_source ( $attachment_id );
		$sources_portait = $VAR["attachment_id_portrait"] ? nv_calculate_img_source( $VAR["attachment_id_portrait"] ) : "";

		$img = nv_c( "UI/responsive-image", [ "attachment_id" => $VAR["attachment_id"], "sizes" => "(min-width: 1px) 100vh, 100vh" ] );


		return <<<HTML

		<div class="section-cover-image">
			<div class="cover-image">
				<picture>
					{$sources}
					{$sources_portrait}
					{$img}
				</picture>
				$i
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