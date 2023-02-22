<?php

// FILTERING FUNCTION

nv_ajax(
	"tips/filter",
	function ( )
	{
		return nv_c( "tips/c/feed", [
			"tags" => empty( $_POST['tags'] ) ? [] : $_POST['tags'],
			"orderby" => "date",
			"paged" => $_POST['paged'],
		] );
	}
);