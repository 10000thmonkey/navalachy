<?php 

class NVBK
{
	protected $columns = array(
        'calendar_id' => '%d',
        'uid' => '%s',
        'order_id' => '%d',
        'start_date' => '%s',
        'end_date' => '%s',
        'fields' => '%s',
        'status' => '%s',
        'is_read' => '%d',
        'date_created' => '%s',
        'date_modified' => '%s'
    );
	public $table_name = "nvbk_booking";



	public function __construct()
	{
		//check if table is created
		$nvbk_table_exists = get_option("nvbk_table_exists");

		if ( $nvbk_table_exists === false )
		{
			$this->create_table();
			$this->sync();

			update_option("nvbk_table_exists", true);
		}

	}


    public function create_table()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $wpdb->query("DROP TABLE {$this->table_name}");

        $query = "CREATE TABLE {$this->table_name} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			calendar_id bigint(10) NOT NULL,
			uid tinytext NOT NULL,
			order_id int NOT NULL,
            start_date datetime NOT NULL,
			end_date datetime NOT NULL,
			fields text NOT NULL,
			status text NOT NULL,
            is_read tinyint(1) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($query);

        $wpdb->query("CREATE UNIQUE INDEX uid_idx ON {$this->table_name} (uid(50))");
    }


    public function confirm_booking ( $booking_id, $wc_order_id, $wc_order, $wc_order_meta )
    {
    	global $wpdb;

    	$query = $wpdb->prepare( "UPDATE {$this->table_name}
    							SET `order_id` = %d, `status` = 'CONFIRMED'
    	                        WHERE `id` = %d"
    	                        , (int)$wc_order_id, (int)$booking_id );

    	$results = $wpdb->query( $query );





    	$data = json_encode([
    		"apartment_id" => $wc_order_meta["nvbk_booking_apartmentId"],
			"apartment_name" => $wc_order_meta["nvbk_booking_apartmentName"],
			"begin" => $wc_order_meta["nvbk_booking_begin"],
			"end" => $wc_order_meta["nvbk_booking_end"],
			"price" => $wc_order_meta["nvbk_booking_price"],
			"guests" => $wc_order_meta["nvbk_booking_people"],
			"booking_id" => $wc_order_meta["nvbk_booking_id"]
    	]);
    	do_action("qm/debug", wp_remote_post( "http://142.93.157.222:5678/webhook-test/69ae1463-85a5-4497-8694-aed4fb067fa6", array(
		    'body'    => $data,
		    'headers' => array(
		    	'Content-Type' => 'application/json',
		        //'Authorization' => 'Basic ' . base64_encode( "spiderweb" . ':' . "hovnokleslo" ),
		    ),
		) ) );

    	return $results;
    }

    public function get_bookings ( $apartment_id, $begin = NULL, $end = NULL )
    {
    	global $wpdb;

    	$range = ( $begin == NULL || $end == NULL ) ? "" : "AND `end_date` >= %s AND `start_date` <= %s";

    	$query = $wpdb->prepare( "SELECT * FROM {$this->table_name}
    	                        WHERE `calendar_id` = %s
    	                        AND `status` != 'PENDING'
    	                        " . $range, $apartment_id );

    	$results = $wpdb->get_results( $query );

    	return $results;
    }


    public function get_confirmed_bookings ( $apartment_id, $begin = NULL, $end = NULL )
    {
    	global $wpdb;

    	$range = ( $begin == NULL || $end == NULL ) ? "" : "AND `end_date` >= %s AND `start_date` <= %s";

    	$query = $wpdb->prepare( "SELECT * FROM {$this->table_name}
    	                        WHERE `calendar_id` = %s
    	                        AND `uid` LIKE '%navalachy.cz'
    	                        AND `status` = 'CONFIRMED'
    	                        " . $range, $apartment_id );

    	$results = $wpdb->get_results( $query );

    	return $results;
    }

    public function is_available ( $apartment_id, $begin = NULL, $end = NULL )
	{
		global $wpdb;

		$query = $wpdb->prepare( "SELECT * FROM {$this->table_name}
		                        WHERE `calendar_id` = %d
		                        AND `end_date` >= %s
		                        AND `start_date` <= %s
		                        AND `status` NOT LIKE 'PENDING'",
		                        (int)$apartment_id, $begin." 00:00:00", $end." 00:00:00" );

		$res = $wpdb->get_results($query);

		return empty( $res ) ? true : false;
	}

	
	public function get_new_booking_price ( $apartment_id, $begin = NULL, $end = NULL )
	{
		global $wpdb;

		return 100;
	}



	public function sync ( )
	{
		global $wpdb;
		require_once("lib-ical.php");

		$wpdb->query("DELETE FROM {$this->table_name} WHERE `uid` NOT LIKE '%navalachy%'");

		$apartments = $this->get_apartments_ids();

		$keys = implode(", ", array_keys( $this->columns ) );
		$format = "(" . implode(", ", array_values( $this->columns ) ) . ")";

		foreach ( $apartments as $apartment )
		{
			$icals_meta = get_post_meta( $apartment, "ical_url");
			
			if ( count( $icals_meta ) > 0 )
			{
				$values = [];
				$formats = [];

				foreach ( $icals_meta as $ical_url )
				{
					$ical = new ICal( $ical_url );
					$events = $ical->events();

					//echo var_dump($events[0]);

					foreach ( $events as $event )
					{
						$status = 
							empty( $event["STATUS"] ) ?
								"unknown" :
								strtolower($event["STATUS"]);
						$fields = ["summary" => $event["SUMMARY"], "description" => $event["DESCRIPTION"]];

				    	array_push($values, ...array_values( [
				            'calendar_id' => absint($apartment),
				            'uid' => $event["UID"],
				            'order_id' => "1",
				            'start_date' => date('Y-m-d 00:00:00', strtotime($event["DTSTART"])),
				            'end_date' => date('Y-m-d 00:00:00', strtotime($event["DTEND"])),
				            'fields' => serialize($fields),
				            'status' => $status,
				            'is_read' => '0',
				            'date_created' => current_time('Y-m-d H:i:s'),
				            'date_modified' => current_time('Y-m-d H:i:s'),
				        ] ) );
				        $formats[] = $format;
					}
				}
				$sql_formats = implode(", ", $formats);

				$sql = "INSERT INTO {$this->table_name} ({$keys})
						VALUES {$sql_formats}
						ON DUPLICATE KEY UPDATE `date_modified` = '" . current_time('Y-m-d H:i:s')."'";

				$query = $wpdb->prepare( $sql, array_values( $values ) );

				echo $wpdb->query( $query );
			}
		}
	}



    public function insert_booking ( $apartment_id, $start_date, $end_date, $fields = [], $order_id = 1 )
    {
    	global $wpdb;

    	$data = array(
            'calendar_id' => absint($apartment_id),
            'uid' => current_time('Y-m-d H:i:s') . '@navalachy.cz',
            'order_id' => $order_id,
            'start_date' => date('Y-m-d 00:00:00', strtotime($start_date)),
            'end_date' => date('Y-m-d 00:00:00', strtotime($end_date)),
            'fields' => serialize($fields),
            'status' => 'PENDING',
            'is_read' => '0',
            'date_created' => current_time('Y-m-d H:i:s'),
            'date_modified' => current_time('Y-m-d H:i:s'),
        );

        $keys = implode(", ", array_keys( $this->columns ) );
		$format = "(" . implode(", ", array_values( $this->columns ) ) . ")";

		$sql = "INSERT INTO {$this->table_name} ({$keys})
				VALUES {$format}
				ON DUPLICATE KEY UPDATE `date_modified` = '" . current_time('Y-m-d H:i:s')."'";

		$query = $wpdb->prepare( $sql, array_values( $data ) );
		$wpdb->query($query);

		return $wpdb->insert_id;
    }




	public function get_disabled_days ( $apartmentId )
	{
		global $wpdb;

		$query = $wpdb->prepare( "SELECT * FROM {$this->table_name}
		                         WHERE `calendar_id` = %s", $apartmentId );
		$results = $wpdb->get_results( $query );

		$disabledDates = [];

		foreach ( $results as $result )
	    {
	    	$range = $this->get_date_range_array( $result->start_date, $result->end_date );
	    	array_push( $disabledDates, ...$range );
	    }
	    return $disabledDates;
	}



	public function get_apartments_ids ()
	{
		$query = new WP_Query( ["post_type" => "accomodation"] );
		$apartments = [];

		if ( $query->have_posts() )
		{
			while ( $query->have_posts() )
			{
				$query->the_post();
				array_push( $apartments, $query->post->ID );
			}
			return $apartments;
		}
		else
		{
			return false;
		}
	}


	public function get_date_range_array ( $first, $last, $array = [] )
	{
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last )
	    {
	        array_push($array, date("Y-m-d", $current));
	        $current = strtotime("+1 day", $current);
	    }

	    return $array;
	}
}
global $nvbk;
$nvbk = new NVBK();



function nvbk_ajax_show_rates ()
{
	global $nvbk;
	header("Content-Type: application/json; charset=UTF-8");

	$apartmentId = $_POST["apartmentId"];

	$response = $nvbk->get_disabled_days( (int)$apartmentId );

	echo json_encode( $response );
	die();
}

add_action("wp_ajax_nvbk_get_disabled_dates", "nvbk_ajax_get_disabled_dates");
add_action("wp_ajax_nopriv_nvbk_get_disabled_dates", "nvbk_ajax_get_disabled_dates");