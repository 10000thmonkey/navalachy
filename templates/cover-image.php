<?php
//provide array of Attachment IDs, component will handle rest

function nv_template_cover_image( $VAR )
{
	$h = isset($_VAR["heading"]) ? "<h1>".$VAR["heading"]."</h1>" : "";
	$s = isset($_VAR["subheading"]) ? "<h5>".$VAR["subheading"]."</h5>" : "";
	$c = isset($_VAR["content"]) ? $VAR["content"] : "";
	$i = nv_responsive_img( $VAR["attachment"], "(min-width: 1px) 100vw, 100vw");


	return <<<HTML

	<div class="section-cover-image">
		<div class="cover-image">
			$i
		</div>
		<div class="cover-content">
			$h $s $c
		</div>
	</div>
	
	HTML;
}
?>