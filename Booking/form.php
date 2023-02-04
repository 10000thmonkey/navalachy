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
		if ($iss) : //will be loading async
		?>
			
			jax.post( "/wp-admin/admin-ajax.php", { action: "nvbk_get_disabled_days", apartmentId: nv_vars.apartmentId }, (data) =>
			{
				c.iss = true;
				c.apartmentId = nv_vars.apartmentId;
				c.apartmentName = nv_vars.apartmentName;
				c.capacity = nv_vars.apartmentCapacity;

				loadDatePicker(c);
			});
		<?php
		else:
		?>
			c.iss = false;
			c.capacity = 1;
			c.disabledDays = [];
			c.apartmentId = 0;
			c.apartmentName = "";

			loadDatePicker(c);

		<?php
		endif;
		?>

	} );
</script>

<?php
return ob_get_clean();
}
?>