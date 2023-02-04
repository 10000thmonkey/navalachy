<?php
$NV_MODULES = [
	"Booking/lib"
];

get_header();

//$nvbk->sync();
//$insert = $nvbk->insert_booking ( 149, "2023-07-01", "2023-07-05", ["name"=>"voja"], 2 );

//print_r(json_encode($nvbk->get_disabled_days(149)));

$id = empty($_GET["id"]) ? 149 : $_GET['id'];

$res = $nvbk->get_bookings( $id );

//print_r($res[count($res) - 1]);
//print_r($res);
echo "<table border=1>";
foreach ($res as $row) {
    ?>
    	<tr>
	        <td><b><?=$row->start_date?> - <?=$row->end_date?></b></td>
		    <td><?=unserialize( $row->fields )["summary"]?></td>
		    <td><?=unserialize( $row->fields )["description"]?></td>
		    <td><?=$row->order_id;?></td>
		    <td><?=$row->status;?></td>
		    <td><?=$row->uid;?></td>
		</tr>
	<?php
}
echo "</table>";


get_footer();


?>