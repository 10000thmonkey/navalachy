<?php

function nv_dashboard_accomodation_settings ()
{
	ini_set("display_errors", 1);
	$data = json_decode( stripslashes($_POST['data']), true );

	foreach ($data as $key => $value){
		if (is_array($key))
			$data[$key] = json_encode($value);
	}

	$pod = pods("accomodation", 149);
	echo $pod->save($data);
	die();
}
add_action("wp_ajax_nv_dashboard_accomodation_settings", "nv_dashboard_accomodation_settings");
add_action("wp_ajax_nopriv_nv_dashboard_accomodation_settings", "nv_dashboard_accomodation_settings");