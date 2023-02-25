<?php

nv_new_e( "accomodation/e/order-complete", function ( $VAR )
{
	$featured_img = wp_get_attachment_image_url( (int)$VAR["apartment"]["_thumbnail_id"][0], "medium" );
	$avatar = wp_get_attachment_image_url( $VAR["host"]["profile_picture"][0] );
	$host = $VAR["host"]["first_name"][0] . " " . $VAR["host"]["last_name"][0];
	$begin = date("j. n. Y", strtotime($VAR["nvbk"]["begin"][0]) );
	$end = date("j. n. Y", strtotime($VAR["nvbk"]["end"][0]) );
	$people = intval($VAR["nvbk"]["adults"][0]) + intval($VAR["nvbk"]["kids"][0]);
	$checkin = date("H:i", strtotime($VAR["apartment"]["checkin"][0]));
	$checkout = date("H:i", strtotime($VAR["apartment"]["checkout"][0]));


	$output = <<<HTML

	<div style="text-align:center;" id="nv_email_logo">
		<img src="https://navalachy.cz/wp-content/uploads/Logo-navalachy-Modre@2x.png" height="50" style="height:50px;object-fit:contain;margin: 20px 0">
	</div>
	<div style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; margin:0 auto; width:100%; max-width: 500px; border-radius: 30px; border: 1px solid rgba(0,0,0,0.05);" width="100%">
		<div style="padding: 30px">
			<h1 style="text-align:center;color:#232f5b;font-size:30px; line-height:150%;margin:0 0 20px 0;">Rezervace potvrzena</h1>
		</div>

		<div style="padding: 30px; background-color: #ffe4b3;">
			<h2 style="margin:10px 0">{$VAR["nvbk"]["apartment_name"]}</h2>
			<img src="{$featured_img}" style="width:100%; border-radius: 15px; margin: 30px 0;object-fit: cover;">

			<h2 style="color:#232f5b;">Rezervace č. {$VAR["order_id"]}</h2>

			<h3 style="color:#232f5b;">Příjezd</h3>
			<p style="margin:10px 0">{$begin}</p>
			<p style="margin:10px 0">Check-in: {$checkin}</p>

			<h3 style="color:#232f5b;">Odjezd</h3>
			<p style="margin:10px 0">{$end}</p>
			<p style="margin:10px 0">Check-out: {$checkout}</p>

			<h3 style="color:#232f5b;">Počet osob</h3>
			<p style="margin:10px 0">{$people}</p>

		</div>

		<div style="padding: 30px;">

			<h3 style="color:#232f5b;">Cena</h3>
			<p style="margin:10px 0">{$VAR["order"]["_order_total"][0]} {$VAR["order"]["_order_currency"][0]}</p>
			
			<h3 style="color:#232f5b;">Kontaktní údaje</h3>
			<p style="">{$VAR["order"]["_billing_first_name"][0]} {$VAR["order"]["_billing_last_name"][0]}</p>
			<a style="margin:10px 0" href="tel:{$VAR["order"]["_billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["order"]["_billing_phone"][0]}</a>
			<br>
			<a style="margin:10px 0" href="mailto:{$VAR["order"]["_billing_email"][0]}" target="_blank">{$VAR["order"]["_billing_email"][0]}</a>
			<address>
				{$VAR["order"]["_billing_company"][0]}<br>
				{$VAR["order"]["_billing_address_1"][0]}<br>
				{$VAR["order"]["_billing_city"][0]}<br>
				{$VAR["order"]["_billing_postcode"][0]}<br>
			</address>
		
		</div>

		<div style="padding: 30px; background-color: rgba(0,0,0,.05);">
			<h3 style="color:#232f5b;">Hostitel</h3>
			<div style="display: flex; gap: 15px">
				<img src="{$avatar}" width="80" height="80" style="width:80px;height:80px;border-radius:40px">
				<div style="margin: 0 0 0 10px; flex: 1">
					<p style="margin:10px 0"><b>{$host}</b></p>
					<p style="margin:10px 0"><a href="tel:{$VAR["host"]["billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["host"]["billing_phone"][0]}</a></p>
					<p style="margin:10px 0"><a href="mailto:{$VAR["host"]["billing_email"][0]}" target="_blank">{$VAR["host"]["billing_email"][0]}</a></p>
				</div>
			</div>
		</div>

		<div style="padding: 30px">
			<pre style="white-space: pre-wrap; margin: 10px 0; font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size: 80%">
				<a href="https://navalachy.cz/">NaValachy</a>
				Návrat ovečky do Bílých Karpat z.s.
				Brumovská 918
				Valašské Klobouky
				766 01
				IČO: 01306553
				info@navalachy.cz</pre>
		</div>
	</div>

	HTML;

	return $output;
} );