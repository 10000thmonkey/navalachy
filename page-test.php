<?php
$NV_MODULES = [
	"Booking/lib"
];

get_header();

$nvbk->sync();
//$insert = $nvbk->insert_booking ( 149, "2023-07-01", "2023-07-05", ["name"=>"voja"], 2 );

//print_r(json_encode($nvbk->get_disabled_days(149)));

$id = empty($_GET["id"]) ? 149 : $_GET['id'];

$res = $nvbk->get_bookings( $id );

//print_r($res[count($res) - 1]);
//print_r($res);

foreach ($res as $row) {
	echo "summary:".unserialize( $row->fields )["summary"] . "<br>";
	echo "description:".unserialize( $row->fields )["description"] . "<br>";
	echo "date:".$row->start_date . " - " .  $row->end_date . "<br>";
	echo "order:".$row->order_id . "<br>";
	echo "status:".$row->status . "<br>";
	echo "uid:".$row->uid . "<br>";
	echo "<br>.<br>";
}
//$nvbk->sync();
//echo do_shortcode('[wpbs id="1" form_id="1"]');






get_footer();


?>