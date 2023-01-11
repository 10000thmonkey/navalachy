<?php 
$iss = is_singular("ubytovani");
?>

<form class="availability-form">
	<div class="datepicker hidden" aria-hidden="true" id="datepicker">
		<div id="calendar"></div>
		<div class="datepicker-footer">
			<a class="button button-plain cancel" onclick="cal.reset()">Zrušit</a>
			<a class="button ok" onclick="cal.set()">OK</a>
		</div>
		<div class="messagebox hidden"></div>
	</div>

	<div class="fieldgroup">
		<div class="field" id="field-begin" onclick="cal.show()">
			<div class="field-label">Od</div>
			<div class="field-value">-</div>
			<input type="hidden" name="begin">
		</div>

		<div class="field" id="field-end" onclick="cal.show()">
			<div class="field-label">Do</div>
			<div class="field-value">-</div>
			<input type="hidden" name="end">
		</div>
	</div>
	
	<?php if($iss) : ?>

	<div class="fieldgroup">
		<div class="field" id="field-people">	
			<div style="display:flex">
				<div style="flex:1">
					<div class="field-label">Osoby</div>
					<div class="field-value">1</div>
				</div>
				<div style="display: flex;">
					<a class="button button-plain button-icon-only" onclick="cal.setPeople(-1)"><i class="nvicon nvicon-minus"></i></a>
					<a class="button button-plain button-icon-only" onclick="cal.setPeople(+1)"><i class="nvicon nvicon-plus"></i></a>
				</div>
			</div>
		</div>
	</div>
	<a class="button" onclick="cal.sendToCheckout()">Rezervovat</a>
	<div class="fieldgroup">
		<div class="field hidden" id="field-price">
			<div class="field-label">Celkem</div>
			<div class="field-value"></div>
		</div>
	</div>

	<?php else: ?>
		
	<a class="button button-icon" onclick="cal.search()">Vyhledat<div class="nvicon nvicon-search"></div></a>

	<?php endif; ?>

	<div class="spinner-wrapper" style="border-radius: 50px;">
		<div class="spinner"></div>
	</div>

</form>

<script type="text/javascript" src="/wp-content/themes/navalachy/Booking/booking.js"></script>
<script type="text/javascript">
q( () => {
	var c = {};
	<?php
	if ($iss) :

		echo "c.iss = true;";
		$disabledDays = $nvbk->get_disabled_days( $meta_fields["calendar_id"][0] );

		if ( is_wp_error($disabledDays) ) {
			echo "c.disabledDays = [];";
			echo "var systemerror = " . json_encode($disabledDays) . ";";
		} else {
			echo "c.disabledDays = ". json_encode( $disabledDays ) . ";"; 
		}
		echo "c.apartmentId = " . (int)$meta_fields["calendar_id"][0] . ";";
		echo "c.apartmentName = '" . $title . "';";
		echo "c.peopleLimit = " . (int)$meta_fields["capacity"][0] . ";";

	else:

		echo "c.iss = false;";
		echo "c.disabledDays = [];";
		echo "c.peopleLimit = 1;";

	endif;
	?>
	cal = new NV_Booking({
		peopleLimit: 12,
		selector: ".availability-form",
		disabled: c.disabledDays,
		iss: c.iss,
		apartmentId: c.apartmentId,
		apartmentName: c.apartmentName
	});
	cal.el.spinner.hide();
} );
</script>