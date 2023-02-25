<?php
/// this page is a weird connection of order confirm wc hook from  accomodaion/functions-global and accomodation/e/order-complete. It violates many patterns used in NV development



$order_id = wc_get_order_id_by_order_key( $_GET[ 'key' ] );
$order = wc_get_order( $order_id );
$order_meta = get_post_meta( $order_id );

// do nothing if this is not reservation
if ( empty( $order_meta["nvbk_meta"] ) )
	return;


include_once get_template_directory() . "/accomodation/i/lib.php";
global $nvbk;


$nvbk_meta = json_decode( $order_meta["nvbk_meta"][0], true, 512, JSON_UNESCAPED_UNICODE );
$apartment_meta = get_post_meta( $nvbk_meta["apartment_id"] );
$host_meta = get_user_meta( $apartment_meta["host"][0] );




$VAR = [ 
	"order" => $order_meta,
	"order_id" => $order_id,
	"nvbk" => $nvbk_meta,
	"apartment" => $apartment_meta,
	"host" => $host_meta
];



$featured_img = wp_get_attachment_image_url( (int)$VAR["apartment"]["_thumbnail_id"][0], "medium" );
$avatar = wp_get_attachment_image_url( $VAR["host"]["profile_picture"][0] );
$host = $VAR["host"]["first_name"][0] . " " . $VAR["host"]["last_name"][0];
$begin = date("j. n. Y", strtotime($VAR["nvbk"]["begin"]) );
$end = date("j. n. Y", strtotime($VAR["nvbk"]["end"]) );
$people = intval($VAR["nvbk"]["adults"]) + intval($VAR["nvbk"]["kids"]);
$checkin = date("H:i", strtotime($VAR["apartment"]["checkin"][0]));
$checkout = date("H:i", strtotime($VAR["apartment"]["checkout"][0]));
$billing_info = <<<HTML
<address>
	<h3>Fakturační údaje</h3>
	{$VAR["order"]["_billing_company"][0]}<br>
	{$VAR["order"]["_billing_address_1"][0]}<br>
	{$VAR["order"]["_billing_city"][0]}<br>
	{$VAR["order"]["_billing_postcode"][0]}<br>
</address>
HTML;

if (empty($VAR["order"]["_billing_address_1"][0])) $billing_info = "";



$output = <<<HTML

<style type="text/css">
	p {color: var(--primary);}
</style>
<div class="contentwrap rows gap-hg padding-md space-around-lg">

	<h1 class="space-around-hg" style="color:var(--primary);">Rezervace potvrzena</h1>

	<div class="cols cols-md-2 gap-lg" style="position: relative;">
	
		<div class="padding-lg rows gap-lg">

			<h2>Rezervace č. {$VAR["order_id"]}</h2>

			<div>
				<h3>Cena</h3>
				<p>{$VAR["nvbk"]["price"]}</p>
			</div>
			
			<div class="cols cols-sm-2 gap-md">
				<div>
					<h3>Kontaktní údaje</h3>
					<p>{$VAR["order"]["_billing_first_name"][0]} {$VAR["order"]["_billing_last_name"][0]}</p>
					<a href="tel:{$VAR["order"]["_billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["order"]["_billing_phone"][0]}</a>
					<br>
					<a href="mailto:{$VAR["order"]["_billing_email"][0]}" target="_blank">{$VAR["order"]["_billing_email"][0]}</a>
				</div>

				{$billing_info}
			</div>

			<div class="rows gap-md padding-md">
				<h3>Hostitel</h3>
				<div style="display: flex; gap: 15px">
					<img src="{$avatar}" width="80" height="80" style="width:80px;height:80px;border-radius:40px">
					<div style="margin: 0 0 0 10px; flex: 1">
						<h3>{$host}</h3>
						<p style="margin:10px 0"><a href="tel:{$VAR["host"]["billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["host"]["billing_phone"][0]}</a></p>
						<p style="margin:10px 0"><a href="mailto:{$VAR["host"]["billing_email"][0]}" target="_blank">{$VAR["host"]["billing_email"][0]}</a></p>
					</div>
				</div>
			</div>
		</div>



		<div class="box padding-lg rows gap-md" style="position: sticky; top: 15px; background-color: var(--secondary-light);align-self: start;">

			<h2>{$VAR["nvbk"]["apartment_name"]}</h2>

			<img src="{$featured_img}" style="width:100%; border-radius: 15px; margin: 30px 0;object-fit: cover;">

			<div class="cols cols-sm-3 gap-md">
				<div>
					<h3>Příjezd</h3>
					<p>{$begin}</p>
					<p>Check-in: {$checkin}</p>
				</div>
				<div>
					<h3>Odjezd</h3>
					<p>{$end}</p>
					<p>Check-out: {$checkout}</p>
				</div>
				<div>
					<h3>Počet osob</h3>
					<p>{$people}</p>
				</div>
			</div>
		</div>



	</div>




</div>

HTML;

echo $output;