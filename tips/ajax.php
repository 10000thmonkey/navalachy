<?php

// FILTERING FUNCTION

nv_ajax(
	"tips/feed",
	function ( )
	{
		return nv_c( "tips/c/feed", [
			"tags" => empty( $_POST['tags'] ) ? [] : $_POST['tags'],
			"orderby" => "date",
			"paged" => empty( $_POST['paged'] ) ? 1 : $_POST['paged'],
		] );
	}
);