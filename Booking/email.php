<?php

function nvbk_email_order_complete ( $VAR )
{
	$featured_img = wp_get_attachment_image_url( get_post_thumbnail_id( (int)$VAR["nvbk_booking_apartmentId"][0] ), "medium");
	$dates = date("j. n. Y", strtotime($VAR["nvbk_booking_begin"][0]) ) . " - " . date("j. n. Y", strtotime($VAR["nvbk_booking_end"][0]) );

	$output = <<<HTML
		
	<div style="margin:0;padding:70px 0;width:100%" width="100%">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">

			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="600">
						<tr>
							<td valign="top" style="line-height:100%;vertical-align:middle;">
								<h1 style="color:#232f5b;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;">Rezervace vytvořena</h1>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<p style="margin:0 0 16px">Dobrý den {$VAR["_billing_first_name"][0]},</p>
								<p style="margin:0 0 16px">Vaši rezervaci č. {$VAR["_order_id"]} jsme úspěšně zpracovali!</p>

								<div style="text-align:center;padding: 20px; border-radius: 30px; background-color: #ffe4b3; margin: 15px 0; border-radius: 30px;">
									<p style="">{$VAR["nvbk_booking_apartmentName"][0]}</p>
									<img src="{$featured_img}" style="width:100%; border-radius: 30px;">
									<h2 style="color:#232f5b;">{$dates}</h2>
								</div>

								<div style="margin-bottom:40px">
									<table border="0" style="color:#878787;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" width="100%">
										<tr>
											<th scope="row" colspan="2" style="vertical-align:middle;padding:12px;text-align:left" align="left">Platební metoda:</th>
											<td style="vertical-align:middle;padding:12px;text-align:left" align="left">
												{$VAR["_payment_method_title"][0]}
											</td>
										</tr>
										<tr>
											<th scope="row" colspan="2" style="vertical-align:middle;padding:12px;text-align:left;border-top-width:4px" align="left">Cena:</th>
											<td style="vertical-align:middle;padding:12px;text-align:left;border-top-width:4px" align="left">
												{$VAR["_order_total"][0]} {$VAR["_order_currency"][0]}
											</td>
										</tr>
									</table>
								</div>

								<table cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:40px;padding:0" width="100%">
									<tr>
										<td valign="top" width="50%" style="text-align:left;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border:0;padding:0" align="left">
											<h2 style="color:#232f5b;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Fakturační adresa</h2>

											<address style="padding:12px;color:#787878;">
												{$VAR["_billing_company"][0]}<br>
												{$VAR["_billing_first_name"][0]}{$VAR["_billing_last_name"][0]}<br>
												{$VAR["_billing_address_1"][0]}<br>
												{$VAR["_billing_city"][0]}<br>
												{$VAR["_billing_postcode"][0]}<br>
												<a href="tel:{$VAR["_billing_phone"][0]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["_billing_phone"][0]}</a><br>
												<a href="mailto:{$VAR["_billing_email"][0]}" target="_blank">{$VAR["_billing_email"][0]}</a>
											</address>
										</td>
									</tr>
								</table>
								
								<p style="margin:0 0 16px">Děkujeme za Váš nákup.</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

	HTML;

	return $output;
}