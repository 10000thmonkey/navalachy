<?php
$NV_MODULES = [
	"Booking/lib"
];

get_header();

$nvbk->sync();
//$insert = $nvbk->insert_booking ( 149, "2023-07-01", "2023-07-05", ["name"=>"voja"], 2 );

//print_r(json_encode($nvbk->get_disabled_days(149)));

$res = $nvbk->get_bookings( 149 );

//print_r($res[count($res) - 1]);
//print_r($res);

foreach ($res as $row) {
	echo unserialize( $row->fields )["summary"] . "<br>";
	echo $row->start_date . " - " .  $row->end_date . "<br>";
	echo $row->status . "<br>";
	echo $row->uid . "<br>";
	echo "<br>.<br>";
}
//$nvbk->sync();
//echo do_shortcode('[wpbs id="1" form_id="1"]');






get_footer();


?>