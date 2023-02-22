<?php

nv_new_c (
	"UI/tags",
	function ( $VAR )
	{
		$VAR = array_merge( [
			"include" => "",
			"taxonomy" => "tips_tags"
		] , $VAR);

		if ( empty( $VAR["terms"] ) )
		{
			$nv_tags = get_terms( [
				'hide_empty' => false,
				'taxonomy'   => $VAR["taxonomy"],
				"include"    => $VAR["include"]
			] );
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
					<a href="/tipy/?tags=$s" class="button button-plain button-icon button-icon-right">
						<i class="nvicon nvicon-$s"></i>
						<div>$n</div>
					</a>
				</div>
				HTML;
			}		
		}
		return $c;
	}
);
