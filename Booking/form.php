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

	<?php if($iss) : ?>
		<h3>Vyberte termín</h3>
	<?php endif; ?>
	
	<div class="fieldgroup hovering space-around-md">

		<div class="datepicker hidden hiding padding-sm" aria-hidden="true" id="datepicker">
			<div id="calendar"></div>
			<div class="messages nodisplay"></div>
		</div>

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
	
	<a class="button continuebutton" onclick="this.closestParent('aside').removeClass('reallyaside').addClass('slided');document.body.css('overflow','hidden')">Pokračovat</a>

	<div class="reservation-form-popup rows gap-md">
		<div class="fieldgroup rows" id="fieldgroup-people">
			<div class="field" id="field-adults">	
				<div style="display:flex">
					<div style="flex:1">
						<div class="field-label">Dospělí</div>
						<div class="field-value">1</div>
					</div>
					<div style="display: flex;">
						<a class="button button-plain button-icon-only" onclick="cal.setPeople(-1, 'adults')"><i class="nvicon nvicon-minus"></i></a>
						<a class="button button-plain button-icon-only" onclick="cal.setPeople(+1, 'adults')"><i class="nvicon nvicon-plus"></i></a>
					</div>
				</div>
			</div>
			<div class="field" id="field-kids">	
				<div style="display:flex">
					<div style="flex:1">
						<div class="field-label">Děti</div>
						<div class="field-value">0</div>
					</div>
					<div style="display: flex;">
						<a class="button button-plain button-icon-only" onclick="cal.setPeople(-1, 'kids')"><i class="nvicon nvicon-minus"></i></a>
						<a class="button button-plain button-icon-only" onclick="cal.setPeople(+1, 'kids')"><i class="nvicon nvicon-plus"></i></a>
					</div>
				</div>
			</div>
		</div>

		<div class="fieldgroup rows space-around-md nodisplay" id="fieldset-price"></div>

		<div style="padding: 5px 30px">
			<p>Energie a dřevo navíc jednoduše doplatíte při odjezdu dle spotřeby. (postup bude zaslán v manuálu)</p>
		</div>

		<a class="button" onclick="cal.sendToCheckout()">Rezervovat</a>
		
		<div class="messages nodisplay"></div>

	</div>

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
			
			jax.post( "/wp-admin/admin-ajax.php", {
				"action": "nvbk_get_disabled_dates",
				"apartmentId": nv_vars.apartmentId
			},
			(data) => {
				c.iss = true;
				c.apartmentId = nv_vars.apartmentId;
				c.apartmentName = nv_vars.apartmentName;
				c.capacity = nv_vars.apartmentCapacity;
				c.disabledDays = JSON.parse(data);

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