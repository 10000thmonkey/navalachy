<?php


// FILTROVANI ZAZITKU

add_action('wp_ajax_nv_filter_experiences', 'nv_filter_experiences_function', 1, 1); 
add_action('wp_ajax_nopriv_nv_filter_experiences', 'nv_filter_experiences_function', 1, 1);

function nv_filter_experiences_function( $args )
{
	require_once("feed.php");

	if ( !empty( $_POST['tagfilter'] ) ) {
		if (strpos( $_POST["tagfilter"], ","))
				$tags = explode(",", $_POST["tagfilter"]);
		else
			$tags = array($_POST["tagfilter"]);
	}
	else
		$tags = [];

	if ( !is_array ($args) ) {
		$args = array (
			"tagfilter" => $tags,
			"orderby" => "date",
			"paged" => $_POST['paged'],
		);
	}
	//echo var_dump($_POST['tagfilter']);
	echo json_encode(nv_template_experiences_feed( $args ));

	//echo nv_template_experiences_feed( $args )["args"];
	die();
}

