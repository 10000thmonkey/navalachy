<?php



if ( empty( $_GET["reservation"] ) )
{
	$query = $wpdb->get_results(
		$wpdb->prepare("
		    SELECT *
		    FROM {$nvbk->table_name}
		    WHERE `begin_date` > '%s'
	    ", date("Y-m-d 00:00:00", time() ))
	);

	if ( count($query) === 0 )
	{

	}
	else
	{
		echo "<table>";
		foreach ( $query as $key => $item )
		{
			$from = date( "j. n. Y", strtotime( $item->begin_date ) );
			$to = date( "j. n. Y", strtotime( $item->end_date ) );
			echo <<<HTML
				<tr>
					<td>$from</td>
					<td>$to</td>
					<td>$item->apartment_id</td>
				</tr>
			HTML;
		}
		echo "</table>";
	}

}
else
{
	include "page-listings-single.php";
}