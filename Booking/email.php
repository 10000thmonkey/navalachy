<?php

function nvbk_email_order_details ( $VAR )
{
	$featured_img = nv_responsive_img( get_post_thumbnail_id( (int)$VAR["nvbk_booking_apartmentId"] ) );

	$output = <<<HTML
		
	<div style="margin:0;padding:70px 0;width:100%" width="100%">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">

			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="600">
						<tr>
							<td align="center" valign="top" style="color:#232f5b;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;">
								<h1 style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;color:#fff;background-color:inherit" bgcolor="inherit">Rezervace vytvořena</h1>
							</td>
						</tr>
						<tr>
							<td align="center" valign="top">
								<p style="margin:0 0 16px">Dobrý den {$VAR["_billing_first_name"]},</p>
								<p style="margin:0 0 16px">Dokončili jsme zpracování Vaší objednávky č. {$VAR["_order_id"]}</p>

								<div style="border-radius: 30px; background-color: #ffe4b3; border-radius: 30px;">
									{$featured_img}
									<div style="padding: 15px">
										<h2 style="color:#232f5b;">{$VAR["nvbk_booking_begin"]} - {$VAR["nvbk_booking_end"]}</h2>
										<p>{$VAR["nvbk_booking_apartmentName"]}</p>
									</div>
								</div>

								<div style="margin-bottom:40px">
									<table cellspacing="0" cellpadding="6" border="1" style="color:#9f9f9f;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" width="100%">
										<tr>
											<th scope="row" colspan="2" style="color:#9f9f9f;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left" align="left">Platební metoda:</th>
											<td style="color:#9f9f9f;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left" align="left">
												{$VAR["_payment_method_title"]}
											</td>
										</tr>
										<tr>
											<th scope="row" colspan="2" style="color:#9f9f9f;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px" align="left">Cena:</th>
											<td style="color:#9f9f9f;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px" align="left">
												{$VAR["_order_total"]} {$VAR["_order_currency"]}
											</td>
										</tr>
									</table>
								</div>

								<table cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:40px;padding:0" width="100%">
									<tr>
										<td valign="top" width="50%" style="text-align:left;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border:0;padding:0" align="left">
											<h2 style="color:#232f5b;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Fakturační adresa</h2>

											<address style="padding:12px;color:#9f9f9f;border:1px solid #e5e5e5">
												{$VAR["_billing_company"]}<br>
												{$VAR["_billing_first_name"]}{$VAR["_billing_last_name"]}<br>
												{$VAR["_billing_address_1"]}<br>
												{$VAR["_billing_city"]}<br>
												{$VAR["_billing_postcode"]}<br>
												<a href="tel:{$VAR["_billing_phone"]}" style="color:#232f5b;font-weight:normal;text-decoration:underline" target="_blank">{$VAR["_billing_phone"]}</a><br>
												<a href="mailto:{$VAR["_billing_email"]}" target="_blank">{$VAR["_billing_email"]}</a>
											</address>
										</td>
										<td valign="top" width="50%" style="text-align:left;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;padding:0" align="left">
											<h2 style="color:#232f5b;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">Doručovací adresa</h2>

											<address style="padding:12px;color:#9f9f9f;border:1px solid #e5e5e5">
													Indigos<br>Vojtech Slovacek<br>Potec 128<br>77601 Valasske Klobouky
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