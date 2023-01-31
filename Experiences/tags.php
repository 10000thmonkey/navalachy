<?php
function nv_template_experiences_tags ( $VAR )
{
	if ( empty( $VAR["terms"] ) )
	{
		$nv_tags = get_terms(
			array(
				'taxonomy'   => 'experiences_tags',
				'hide_empty' => false,
				"include" => !empty( $VAR["include"] ) ? $VAR["include"] : ""
			)
		);
	}
	else
	{
		$nv_tags = $VAR["terms"];
	}
	
	$c = "";

	if ( !empty( $VAR["filter"] ) && $VAR["filter"] )
	{
		foreach ( $nv_tags as $nv_tag )
		{
			$s = $nv_tag->slug;
			$n = $nv_tag->name;
			$c .= <<<HTML
			<input type="checkbox" name="tagfilter[]" id="nv_experiences_tag_toggle-$s" value="$s">
			<label class="button button-plain button-icon button-icon-right" for="nv_experiences_tag_toggle-$s">
				<i class="nvicon nvicon-$s"></i>
				<div>$n</div>
			</label>
			HTML;
		}
	}
	else
	{
		foreach ( $nv_tags as $nv_tag )
		{
			$s = $nv_tag->slug;
			$n = $nv_tag->name;
			$c .= <<<HTML
			
			<div class="tag">
				<button class="button button-plain button-icon button-icon-right">
					<i class="nvicon nvicon-$s"></i>
					<div>$n</div>
				</button>
			</div>
			HTML;
		}		
	}
	return $c;
}
?>