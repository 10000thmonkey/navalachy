<?php
//if ( empty($_GET['id']) ) die();
$_GET['id'] = 149;
require( __DIR__ . "/../../../../wp-load.php" );
require( __DIR__ . "/../accomodation/i/lib-ical-export.php" );

$ical = new ICal();

global $wpdb;

$query = $wpdb->prepare("SELECT * FROM nvbk_booking WHERE `apartment_id` = %d AND `status` IN ('CONFIRMED', 'CLOSED', 'PENDING', 'RESERVED')", $_GET['id'] );
$result = $wpdb->get_results($query);

$ical->NewEvent();
$ical->SetTitle("initial event");
$ical->SetDescription("for compatibility issues");
$ical->SetDates( "2000-01-01", "2000-01-02" );

foreach ( $result as $row )
{
	$ical->NewEvent();
	$ical->SetTitle("NV Booking");
	$ical->SetDescription( "Navalachy Booking" );
	$ical->AddCustom( "UID", $row->uid );
	$ical->AddCustom( "STATUS", "CONFIRMED" );
	$ical->SetDates( $row->begin_date, $row->end_date );
}

$ical->Write();

?>