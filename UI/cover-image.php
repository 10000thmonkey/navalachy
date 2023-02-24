<?php
nv_new_c (
	"UI/cover-image",
	function ( $VAR )
	{
		$VAR = array_merge( [
			"heading" => "",
			"subheading" => "",
			"content" => "",
			"attachment_id" => 1
		], $VAR );
		$VAR["heading"] = $VAR["heading"] ? "<h1>{$VAR["heading"]}</h1>" : "";
		$VAR["subheading"] = $VAR["subheading"] ? "<h5>{$VAR["subheading"]}</h5>" : "";


		$i = nv_c( "UI/responsive-image", [ "attachment_id" => $VAR["attachment_id"], "sizes" => "(min-width: 1px) 100vw, 100vw" ] );


		return <<<HTML

		<div class="section-cover-image">
			<div class="cover-image">
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