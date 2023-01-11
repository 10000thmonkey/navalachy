<?php

//function IcalUpdate () {

	require_once("./lib-ical.php");

	// create new ical object
	$ical = new iCal();

	$voucherss = json_decode(file_get_contents("ical.json"));

	foreach ($voucherss as $key => $value) {

		$ical->NewEvent();
		$ical->SetTitle( $value->name );
		$ical->SetDescription("z");
		$ical->SetDates( date( "Y-m-d H:M", (int) $value->from), date( "Y-m-d H:M", (int) $value->to ) );

	}	

	$ical->Write();

//echo date( "Y-m-d H:M", time() ); //1673866800);
?>