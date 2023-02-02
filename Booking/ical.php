<?php
if ( empty($_GET['id']) ) die();

require( "../../../../wp-load.php" );
require( "./lib-ical-export.php" );

$ical = new ICal();

global $wpdb;

$query = $wpdb->prepare("SELECT * FROM nvbk_booking WHERE `calendar_id` = %d AND `status` = 'CONFIRMED' AND `uid` LIKE '%navalachy%' ", $_GET['id'] );
$result = $wpdb->get_results($query);

foreach ( $result as $row )
{
	$ical->NewEvent();
	$ical->SetTitle("NV Booking");
	$ical->SetDescription( "Navalachy Booking" );
	$ical->AddCustom( "UID", $row->uid );
	$ical->AddCustom( "STATUS", "CONFIRMED" );
	$ical->SetDates( $row->start_date, $row->end_date );
}

$ical->Write();

?>