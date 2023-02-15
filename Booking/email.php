<?php

function nvbk_email_order_complete ( $VAR )
{
	echo var_dump($VAR);
	$featured_img = wp_get_attachment_image_url( get_post_thumbnail_id( (int)$VAR["apartment"]["ID"][0] ), "medium");
	$dates = date("j. n. Y", strtotime($VAR["nvbk_booking_begin"][0]) ) . " - " . date("j. n. Y", strtotime($VAR["nvbk_booking_end"][0]) );

	$output = <<<HTML
		
	<div style="text-align:center;">
		<img src="https://navalachy.cz/wp-content/uploads/logo-do-rohu.svg" height="50" style="margin: 20px 0">
	</div>
	<div style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; margin:0 auto; width:100%; max-width: 500px; border-radius: 30px; box-shadow: 0px 5px 40px rgba(0, 0, 0, 0.08),0px 3px 5px 3px rgba(0,0,0,0.05);" width="100%">
		<div style="padding: 30px">
			<h1 style="color:#232f5b;font-size:30px; line-height:150%;margin:0 0 20px 0;text-align:left;">Rezervace potvrzena</h1>
		</div>

		<div style="padding: 30px; background-color: #ffe4b3;">
			<p style="margin:10px 0">{$VAR["nvbk_booking_apartmentName"][0]}</p>
			<img src="{$featured_img}" style="width:100%; border-radius: 30px; margin: 30px 0;object-fit: cover;">

			<h2 style="color:#232f5b;">Rezervace č. {$VAR["_order_id"]}</h2>

			<h3 style="color:#232f5b;">Příjezd</h3>
			<p style="margin:10px 0">{$dates}</p>
			<p style="margin:10px 0">Check-in: {$checkin}</p>

			<h3 style="color:#232f5b;">Odjezd</h3>
			<p style="margin:10px 0">{$dates}</p>
			<p style="margin:10px 0">Check-out: {$checkin}</p>

			<h3 style="color:#232f5b;">Počet osob</h3>
			<p style="margin:10px 0">{$dates}</p>

		</div>

		<div style="padding: 30px;">

			<h3 style="color:#232f5b;">Cena</h3>
			<p style="margin:10px 0">{$VAR["_order_total"][0]} {$VAR["_order_currency"][0]}</p>
			
			<h3 style="color:#232f5b;">Kontaktní údaje</h3>
			<p style="">{$VAR["_billing_first_name"][0]}</p>
			<a style="margin:10px 0" href="tel:{$VAR["_billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["_billing_phone"][0]}</a>
			<br>
			<a style="margin:10px 0" href="mailto:{$VAR["_billing_email"][0]}" target="_blank">{$VAR["_billing_email"][0]}</a>
			<address>
				{$VAR["_billing_company"][0]}<br>
				{$VAR["_billing_first_name"][0]}{$VAR["_billing_last_name"][0]}<br>
				{$VAR["_billing_address_1"][0]}<br>
				{$VAR["_billing_city"][0]}<br>
				{$VAR["_billing_postcode"][0]}<br>
			</address>
		
		</div>

		<div style="padding: 30px; background-color: rgba(0,0,0,.05);">
			<h3 style="color:#232f5b;">Hostitel</h3>
			<div style="display: flex; gap: 15px">
				<img style="width:64px;height: 64px" src="{$avatar}">
				<div style="flex: 1">
					<p style="margin:10px 0"><b>{$host}</b></p>
					<p style="margin:10px 0"><a href="tel:{$VAR["_billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$host_phone}</a></p>
					<p style="margin:10px 0"><a href="mailto:{$VAR["_billing_email"][0]}" target="_blank">{$host_mail}</a></p>
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
}