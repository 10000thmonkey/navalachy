<?php
class NV_Booking {

private $NVBK_API = "fSYOWh6Qb0guxN8U6sBY2D7VrW1HrAFXJIP1XrMwqa";
private $NVBK_USER = 526450;

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
}
?>