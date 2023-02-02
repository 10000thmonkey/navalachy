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
			//$this->sync();

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


    public function confirm_booking ( $booking_id, $wc_order_id )
    {
    	global $wpdb;

    	$query = $wpdb->prepare( "UPDATE {$this->table_name}
    							SET `order_id` = %d, `status` = 'CONFIRMED'
    	                        WHERE `id` = %d"
    	                        , (int)$wc_order_id, (int)$booking_id );

    	$results = $wpdb->query( $query );

    	return $results;
    }

    public function get_bookings ( $apartment_id, $begin = NULL, $end = NULL )
    {
    	global $wpdb;

    	$range = ( $begin == NULL || $end == NULL ) ? "" : "AND `end_date` >= %s AND `start_date` <= %s";

    	$query = $wpdb->prepare( "SELECT * FROM {$this->table_name}
    	                        WHERE `calendar_id` = %s
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

				//echo var_dump( $query );
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












/*
global $nvbk;
$nvbk = new NV_Booking();

class NV_Booking {

private $NVBK_API = "NY7JGjs7qBoKQqWCM7TDngEWw8vta1cIYoQWxlvNib";
private $NVBK_USER = 543631;

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


public function connect ( $url, $method = "GET", $payload = "", $transientId = false, $transientLife = HOUR_IN_SECONDS, $transientOverwrite = false )
{
	if ( $transientId && !$transientOverwrite && !empty( get_transient( $transientId ) ) )
	{
		return json_decode( get_transient( $transientId ), true );
	}
	else
	{
		$response = wp_safe_remote_request( "https://login.smoobu.com/" . $url, array (
			'headers' => array(
				'Api-Key' => $this->NVBK_API,
				'Cache-Control' => 'no-cache'
			),
			'body' => $payload,
			'method' => $method,
			'timeout' => 30
		) );
		if ( is_wp_error($response) ) 
			return $response;

		if ( $transientId ) {
			set_transient( $transientId, $response["body"], $transientLife );
		}
		$return = json_decode($response["body"], true);
		return $return;
	}
}


public function confirm_reservation ($args)
{
	return $this->connect( "api/reservations", "POST", json_encode($args) );
}


public function get_new_booking_price ( $begin, $end, $apartment, $guests = 1 )
{
	return $this->connect( "booking/checkApartmentAvailability", "POST", json_encode( array(
		'arrivalDate' => $begin,
		'departureDate' => $end,
		'apartments' => [$apartment],
		'customerId' => $this->NVBK_USER,
		'guests' => $guests
	) ), false );
}

public function get_availability ( $begin, $end, $apartments = [] )
{
	$transientId = "nvbk_avail-" . http_build_query($apartments) . "-" . $begin . "_" . $end;
	
	$response = $this->connect( "booking/checkApartmentAvailability", "POST", json_encode( array(
		'arrivalDate' => $begin,
		'departureDate' => $end,
		'apartments' => $apartments,
		'customerId' => $this->NVBK_USER,
		'guests' => 1
	) ), $transientId, MINUTE_IN_SECONDS );

	return $response;
}

public function get_apartment( $apartmentId = '' )
{
	if ( $apartmentId == "" ) return NULL;
	$transientId = "nvbk_apartment-" . $apartmentId;

	$response = $this->connect( "api/apartments/" . $apartmentId, "GET", "", $transientId, HOUR_IN_SECONDS * 12 );
	return $response;
}

public function get_rates ( $begin, $end, $apartments = [1520050] )
{
	$transientId = "nvbk_rates-" . http_build_query($apartments) . "-" . $begin . "_" . $end;
	$querystring = http_build_query( array(
		'start_date' => $begin,
		'end_date' => $end,
		'apartments' => $apartments
	) );
	$response = $this->connect( "api/rates?" . $querystring, "GET", "", $transientId, HOUR_IN_SECONDS * 6);
	
	return $response;
}


public function get_disabled_days ( $propertyId )
{	
	return $this->sync_disabled_dates( $propertyId, false, HOUR_IN_SECONDS / 6 );
}

public function sync_disabled_dates ( $propertyId, $caching = false, $transientCache = HOUR_IN_SECONDS / 6 )
{
	$dates = [];
	$transientId = "nvbk_availability_" . $propertyId;

	$query =  "api/reservations?" . http_build_query( array(
		'from' => '2023-01-01',
		'to' => '2024-01-01',
		'excludeBlocked' => true,
		'showCancellation' => false,
		'pageSize' => 100,
		'apartmentId' => $propertyId
	) );

	$response = $this->connect( $query, "GET", "", $transientId, $transientCache, $caching ? false : true ); //last param: bypass transient

	if ( is_wp_error($response) ) return $response;

	if ( $response["bookings"] ) {
		foreach ( $response["bookings"] as $booking ) {
			$dates = $this->get_date_range_array( $booking["arrival"], $booking["departure"], $dates);
		}
	}
	//echo print_r( $response["bookings"] );
	return $dates;
}
public function get_apartments_array ()
{
	$apartamentos = get_transient("nvbk_apartamentos");
	if(empty($apartamentos)) {
		$query = new WP_Query(array(
			"post_type" => "ubytovani",
		));
		$apartamentos = [];
		while ($query->have_posts()) {
			$query->the_post();
			$cal_id = get_post_meta($query->post->ID, "calendar_id");
			$apartamentos[ $query->post->ID ] = (int)$cal_id[0];
		}
		set_transient("nvbk_apartamentos", $apartamentos, DAY_IN_SECONDS );
	}
	return $apartamentos;
}

}
*/