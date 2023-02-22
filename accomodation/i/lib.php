<?php 

class NVBK
{
	protected $columns = [
        'apartment_id' => '%d',
        'uid' => '%s',
        'order_id' => '%d',
        'begin_date' => '%s',
        'end_date' => '%s',
        'data' => '%s',
        'status' => '%s',
        'is_read' => '%d',
        'date_created' => '%s',
        'date_synced' => '%s'
    ];
    protected $data = [
    	'source' => '',
    	'adults' => 1,
    	'kids' => 0,
    	'currency' => 'EUR',
    	'events' => [
    		'deposit' => [],
    		'cleaning' => [],
    		'provision' => [],
    		'payment' => []
    	],
    	'customer' => [
    		'name' => '',
    		'email' => '',
    		'phone' => '',
    		'address' => '',
    		'ip' => '',
    		'notes' => ''
    	],
    	'customer_note' => '',
    	'host_note' => ''
    ];
	public $table_name = "nvbk_booking";
	public $debug = false;


	public function __construct( $debug = false )
	{
		$this->rate_eur_czk = get_option("nvbk_exchange_EUR_CZK");

		if ($debug) $this->debug = true;

		//check if table is created
		$nvbk_table_exists = get_option("nvbk_table_exists");

		if ( $nvbk_table_exists === false || $this->debug )
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
			id int PRIMARY KEY AUTO_INCREMENT, 
			apartment_id int NOT NULL, 
			uid tinytext NOT NULL,
			order_id int NOT NULL,
            begin_date datetime NOT NULL,
			end_date datetime NOT NULL,
			data text NOT NULL,
			status text NOT NULL,
            is_read tinyint(1) NOT NULL,
			date_created datetime NOT NULL, 
			date_synced datetime NOT NULL,
			UNIQUE KEY (apartment_id, begin_date, end_date)
		) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        return $wpdb->query($query);
    }



	public function sync ( )
	{
		global $wpdb;
		require_once("lib-ical.php");

		$current_time = date('Y-m-d 00:00:00');
		$current_sync_time = date('Y-m-d H:i:s');
		update_option("nvbk_last_synced", $current_time);

		$apartments = $this->get_apartments_ids();

		$table_keys = implode(", ", array_keys( $this->columns ) );
		$table_format = "(" . implode(", ", array_values( $this->columns ) ) . ")";

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
						$data = ["summary" => $event["SUMMARY"], "description" => $event["DESCRIPTION"]];
						$begin = date('Y-m-d 00:00:00', strtotime($event["DTSTART"]));
						$end = date('Y-m-d 00:00:00', strtotime($event["DTEND"]));
						$key = absint($apartment) . "_" . $begin . "_" . $end;

				    	array_push($values, ...array_values( [
				            'apartment_id' => absint($apartment),
				            'uid' => $event["UID"],
				            'order_id' => 0,
				            'begin_date' => $begin,
				            'end_date' => $end,
				            'data' => json_encode($data),
				            'status' => 'SYNCED',
				            'is_read' => '0',
				            'date_created' => $current_time,
				            'date_synced' => $current_time,
				        ] ) );
				        $formats[] = $table_format;
					}
				}
				unset($ical);
		
				$sql_formats = implode(", ", $formats);

				$sql = "INSERT INTO {$this->table_name} ({$table_keys})
						VALUES {$sql_formats}
						ON DUPLICATE KEY UPDATE `date_synced` = '" . $current_time . "'";

				$query = $wpdb->prepare( $sql, array_values( $values ) );

				print_r( $wpdb->query( $query ) );
				$wpdb->print_error();
			}
		}

		$sql = "UPDATE {$this->table_name}
				SET `status` = 'UNSYNCED'
				WHERE `date_synced` < '{$current_time}'";
		$wpdb->query($sql);


		$exchangeUrl = 'https://api.exchangerate.host/latest?base=EUR&symbols=CZK';
		$exchangeData = json_decode(file_get_contents($exchangeUrl));
		update_option("nvbk_exchange_EUR_CZK", $exchangeData->rates->CZK);
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

    public function confirm_booking ( $booking_id, $wc_order_id, $wc_order, $wc_order_meta )
    {
    	global $wpdb;

    	$query = $wpdb->prepare( "UPDATE {$this->table_name}
    							SET `order_id` = %d, `status` = 'CONFIRMED'
    	                        WHERE `id` = %d"
    	                        , (int)$wc_order_id, (int)$booking_id );

    	$results = $wpdb->query( $query );

    	// $data = json_encode([
    	// 	"apartment_id" => $wc_order_meta["nvbk_booking_apartmentId"],
		// 	"apartment_name" => $wc_order_meta["nvbk_booking_apartmentName"],
		// 	"begin" => $wc_order_meta["nvbk_booking_begin"],
		// 	"end" => $wc_order_meta["nvbk_booking_end"],
		// 	"price" => $wc_order_meta["nvbk_booking_price"],
		// 	"guests" => $wc_order_meta["nvbk_booking_people"],
		// 	"booking_id" => $wc_order_meta["nvbk_booking_id"]
    	// ]);

    	// $confirm_booking = wp_remote_post( "http://142.93.157.222:5678/webhook-test/69ae1463-85a5-4497-8694-aed4fb067fa6",
    	//     array(
		// 	    'body'    => $data,
		// 	    'headers' => array(
		// 	    	'Content-Type' => 'application/json'
		//     	)
		// 	)
		// );
		//add_action("qm/debug", $confirm_booking );

    	return $results;
    }

    public function get_bookings ( $apartment_id, $begin = NULL, $end = NULL )
    {
    	global $wpdb;

    	$range = ( $begin == NULL || $end == NULL ) ? "" : "AND `end_date` >= %s AND `begin_date` <= %s";

    	return $wpdb->get_results(
    		$wpdb->prepare(
	    		"SELECT * FROM {$this->table_name}
	            WHERE `status` IN ('CONFIRMED', 'PENDING', 'SYNCED', 'CLOSED')
	            AND `apartment_id` = %s
	            " . $range, $apartment_id, $begin, $end
	        )
    	);

    	$results = $wpdb->get_results( $query );

    	return $results;
    }


    public function get_confirmed_bookings ( $apartment_id, $begin = NULL, $end = NULL )
    {
    	global $wpdb;

    	$range = ( $begin == NULL || $end == NULL ) ? "" : "AND `end_date` >= %s AND `start_date` <= %s";

    	$query = $wpdb->prepare("
			SELECT * FROM {$this->table_name}
			WHERE `calendar_id` = %s
			AND `uid` LIKE '%navalachy.cz'
			AND `status` = 'CONFIRMED'
		" . $range, $apartment_id );

    	$results = $wpdb->get_results( $query );

    	return $results;
    }




	public function get_available_apartments ( $from, $to, $apartments = [] )
	{
		global $wpdb;

		$apartments_all = $this->get_apartments_ids();
		$apartments_booked = [];

		$from .= " 00:00:00";
		$to .= " 00:00:00";

		$apartments_sql = "";
		if ( is_integer($apartments) ) {
			$apartments = [$apartments];
		}
		if ( is_array($apartments) && !empty($apartments) ) {
			$apartments_sql = "AND `apartment_id` IN (".implode(",", $apartments).")";
		}

		$query = $wpdb->prepare("
			SELECT * FROM {$this->table_name}
	        WHERE
	        (
	        	( `begin_date` BETWEEN %s AND %s )
	        	OR ( `end_date` BETWEEN %s AND %s )
	        )
	        AND `status` IN ('SYNCED', 'PENDING', 'CONFIRMED', 'CLOSED')" . $apartments_sql,
			$from,
			date( "Y-m-d H:i:s", strtotime( $to . " -1 day" ) ), // reservations with arrival on end date can be skipped
			date( "Y-m-d H:i:s", strtotime( $from . " +1 day" ) ), // reservations with departure on the same day as begin skipped
			$to
		);
		$results = $wpdb->get_results($query);

		foreach($results as $result) {
			$apartments_booked[] = (int)$result->apartment_id;	
		}

		return array_diff($apartments_all, $apartments_booked);
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

    public function is_available ( $apartment_id, $begin = NULL, $end = NULL )
	{
		global $wpdb;

		$query = $wpdb->prepare("
		    SELECT * FROM {$this->table_name}
		    WHERE `calendar_id` = %d
		    AND `end_date` >= %s
		    AND `start_date` <= %s
		    AND `status` NOT LIKE 'PENDING'
		", (int)$apartment_id, $begin." 00:00:00", $end." 00:00:00" );

		$res = $wpdb->get_results($query);

		return empty( $res ) ? true : false;
	}

	
	public function get_new_booking_price ( $apartment_id, $begin = NULL, $end = NULL )
	{
		global $wpdb;
		global $currencies;


		$apartment = new WP_Query(["post_type" => "accomodation", "post__in" => [$apartment_id]] );
		$meta = get_post_meta($apartment_id);

		if ( empty($begin) || empty($end) ) {
			$nights = 1;
		}
		else {
			$booking_array = $this->get_date_range_array( $begin, $end );
			$nights = count( $booking_array ) - 1;
		}




		//user currency only changes output prices
		$user_currency = $_SESSION['currency'];

		if ($user_currency == "CZK") { 
			$user_currency_coef = $currencies[$user_currency][1];
			$user_currency_appendix = " ".$currencies[$user_currency][0]; // str "e"
		} else {
			$user_currency_coef = $currencies["EUR"][1];
			$user_currency_appendix = " ".$currencies["EUR"][0];
		}


		//set currency coefficient, convert to EURO if other currency is set
		if ( !empty($meta["currency"]) && $meta["currency"][0] == "CZK") { 
			$currency_coef = $currencies["CZK"][1];
		} else {
			$currency_coef = $currencies["EUR"][1];
		}

		$price_base = ( $nights * ( empty($meta["price"]) ? 1 : (int)$meta["price"][0] ) );




		$discounts = [];

		if ($nights >= 7 && $nights < 30) {
			$discounts = [
				"label" => "Sleva na týden",
				"value" => "-" . $meta["discount_week"][0] . "%"
			];
			$price_final = $price_base * ( (int)$meta["discount_week"][0] / 100 );
		}
		else if ($nights >= 30) {
			$discounts = [
				"label" => "Sleva na měsíc",
				"value" => "-" . $meta["discount_month"][0] . "%"
			];
			$price_final = $price_base * ( (int)$meta["discount_month"][0] / 100 );
		}
		else {
			$price_final = $price_base;
		}


		$price_host = $price_final;

		$costs = [];
		//parse array of costs
		if( !empty($meta["costs"]) )
		{
			$costs = json_decode($meta["costs"][0]);
			if ( is_array($costs) ) for ($i = 0; $i < count($costs); $i++)
			{
				$price_host = ( (int)$price_host - (int)$costs[$i][1] );
				$costs[$i][1] = intval( $costs[$i][1] / $currency_coef * $user_currency_coef ) . $user_currency_appendix;
			}
		}

		return [
			"price_base" => intval($price_base / $currency_coef * $user_currency_coef) . $user_currency_appendix,
			"price_final" => intval($price_final / $currency_coef * $user_currency_coef) . $user_currency_appendix,
			"price_host" => intval($price_host / $currency_coef * $user_currency_coef) . $user_currency_appendix,
			"discounts" => $discounts,
			"costs" => $costs ? $costs : [],
			"nights" => $nights
		];
	}




	public function get_disabled_dates ( $apartmentId )
	{
		global $wpdb;

		$query = $wpdb->prepare( "
			SELECT * FROM {$this->table_name}
			WHERE `apartment_id` = %s
			AND `end_date` >= %s
			AND `begin_date` < %s
			AND `status` IN ('SYNCED', 'CLOSED', 'PENDING', 'CONFIRMED')"
		, $apartmentId, date("Y-m-d 00:00:00"), date('Y-m-d 00:00:00', strtotime("+1 year") ) );
		$results = $wpdb->get_results( $query );

		$disabledDates = [];

		foreach ( $results as $result )
	    {
	    	$range = $this->get_date_range_array( $result->begin_date, $result->end_date );
	    	array_pop($range);
	    	array_shift($range);
	    	array_push( $disabledDates, ...$range );
	    }
	    return $disabledDates;
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