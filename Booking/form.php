<?php
//provide array of Attachment IDs, component will handle rest

wp_enqueue_script( "booking-datepicker", $templ_dir . "/assets/hello-week.min.js" );
wp_enqueue_style( "booking-datepicker", $templ_dir . "/assets/hello-week.min.css" );

function nv_template_booking_form( $_VAR ) {

global $nvbk;
$iss = $_VAR["iss"];

$output = NULL;
ob_start();
?>

<form id="booking-form">
	
	<?= $iss ? "" : '<div class="fieldgroup hovering">'; ?>

		<div class="datepicker hidden hiding padding-sm" aria-hidden="true" id="datepicker">
			<div id="calendar"></div><? /*
			<!--div class="datepicker-footer">
				<a class="button button-plain cancel" onclick="cal.reset()">Zrušit</a>
				<a class="button ok" onclick="cal.set()">OK</a>
			</div--->*/ ?>
			<div class="messagebox hidden"></div>
		</div>

	<?= !$iss ? "" : '<div class="fieldgroup hovering">'; ?>

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
	<div class="fieldgroup">
		<div class="field hidden" id="field-price">
			<div class="field-label">Celkem</div>
			<div class="field-value"></div>
		</div>
	</div>
	<a class="button" onclick="cal.sendToCheckout()">Rezervovat</a>

	<?php else: ?>
		
	<a class="button button-icon nomargin" onclick="cal.search()">Vyhledat<div class="nvicon nvicon-search"></div></a>

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
			$disabledDays = $nvbk->get_disabled_days( (int)$_VAR["apartmentId"] );

			echo "c.disabledDays = ". json_encode( $disabledDays ) . ";"; 
			echo "c.apartmentId = nv_vars.apartmentId;";
			echo "c.apartmentName = nv_vars.apartmentName;";
			echo "c.capacity = nv_vars.apartmentCapacity;";

		else:

			echo "c.iss = false;";
			echo "c.disabledDays = [];";
			echo "c.capacity = 1;";

		endif;
		?>

		cal = new NV_Booking({
			peopleLimit: c.capacity,
			selector: "#booking-form",
			disabled: c.disabledDays,
			iss: c.iss,
			apartmentId: c.apartmentId,
			apartmentName: c.apartmentName
		});
		cal.el.spinner.hide();


		var URLParams = new URLSearchParams(location.search);

		if ( URLParams.get("begin") && URLParams.get("end") )
			cal.setFromUrl( URLParams.get("begin"), URLParams.get("end") );


		document.body.on( "click", (e) => {
			if ( e.path.indexOf( cal.form[0] ) == -1 ) {
				if (cal.shown) cal.show();
			}
		} );

	} );
</script>

<?php
return ob_get_clean();
}
?>