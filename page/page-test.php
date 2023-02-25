<?php
include_once( get_template_directory(). "/accomodation/i/lib.php" );

global $nvbk;


get_header();


//echo var_dump( file_exists(get_template_directory()."/accomodation/i/lib.php"));



if ($_GET["test"] == 0)
{
	echo $nvbk->create_table();
	$wpdb->print_error();
	echo $nvbk->sync();
}

if ($_GET['test'] == 1) {
	$id = empty($_GET["id"]) ? 149 : $_GET['id'];

	$res = $nvbk->get_bookings( $id );

	//print_r($res[count($res) - 1]);
	//print_r($res);
	echo "<table border=1>";
	foreach ($res as $row) {
	    ?>
	    	<tr>
		        <td><b><?=$row->begin_date?> - <?=$row->end_date?></b></td>
			    <td><?=unserialize( $row->fields )["summary"]?></td>
			    <td><?=unserialize( $row->fields )["description"]?></td>
			    <td><?=$row->order_id;?></td>
			    <td><?=$row->status;?></td>
			    <td><?=$row->uid;?></td>
			</tr>
		<?php
	}
	echo "</table>";
}
if ($_GET["test"] == 2)
{
	global $nvbk;
	//echo "prdel";
	print_r( $nvbk->get_available_apartments( "2023-05-13", "2023-06-15" ) );
}

if ($_GET['test'] == 3)
{
	$booking = $nvbk->get_new_booking_price( 149 );

	print_r($booking);
}
if ($_GET["test"] == 4)
{
	print_r(wp_get_current_user());
	//print_r(get_user_meta( wp_get_current_user() ));
}
if ($_GET["test"] == 5)
{
	$pod = pods("accomodation", 149);

	echo $pod->form();
}
if ($_GET["test"] == 6)
{
	echo var_dump( $nvbk->get_disabled_dates(149) );
}

if ($_GET["test"] == 7)
{
    echo var_dump( $nvbk->insert_booking(149, "2023-05-13", "2023-06-15") );
}

if ($_GET["test"] == 8)
{
	?>

	<nv-logged-in>
		<h1>přihlasen</h1>
	</nv-logged-in>
	<nv-logged-out>
		<h1>nepřihlasen</h1>
	</nv-logged-out>

	<?php
}



get_footer();


?>