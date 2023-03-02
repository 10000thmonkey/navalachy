<?php
/*

$VAR [
	bool $iss - is single
	bool $isa - is archive - to ivoke ajax search
	?int $apartment_id  - id, if single
]

*/

nv_new_c(
	"accomodation/c/form",
	function ( $VAR ) 
	{
	global $nvbk;
	global $nv_vars;

	$VAR = array_merge( [
		"iss" => false,
		"isa" => false,
		"apartment_id" => 0
	], $VAR );

	ob_start();
	?>

	<form id="booking-form">

		<?php if( $VAR["iss"] ) : ?>
			<h3 class="show-only-when-closed-popup">Vyberte termín</h3>
			<h3 class="show-only-when-opened-popup">Kdy</h3>
		<?php endif; ?>


		
		<div class="fieldgroup hovering">

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
		
		<?php if( $VAR["iss"] ) : ?>
		
		<a class="button continuebutton" onclick="this.closestParent('aside').removeClass('reallyaside').addClass('slided');document.body.css('overflow','hidden')">Pokračovat</a>


		<div class="reservation-form-popup rows gap-md">

			<h3>Kdo</h3>

			<div class="fieldgroup rows" id="fieldgroup-people">
				<div class="field cols-flex center" id="field-adults" style="flex-direction: row;">	
					<div class="field-label" style="flex:1">Dospělí</div>
					<a class="button button-transparent-grey button-icon-only" onclick="cal.setPeople(-1, 'adults')"><i class="nvicon nvicon-minus"></i></a>
					<div class="field-value text-center">1</div>
					<a class="button button-transparent-grey button-icon-only" onclick="cal.setPeople(+1, 'adults')"><i class="nvicon nvicon-plus"></i></a>
				</div>
				<div class="field cols-flex center" id="field-kids" style="flex-direction: row;">
					<div class="field-label" style="flex:1">Děti</div>
					<a class="button button-transparent-grey button-icon-only" onclick="cal.setPeople(-1, 'kids')"><i class="nvicon nvicon-minus"></i></a>
					<div class="field-value text-center">0</div>
					<a class="button button-transparent-grey button-icon-only" onclick="cal.setPeople(+1, 'kids')"><i class="nvicon nvicon-plus"></i></a>
				</div>
			</div>

			<div class="rows space-around-md nodisplay" id="fieldset-price"></div>

			<div class="rows space-around-md nodisplay" id="fieldset-price-additional"></div>

			<p style="padding: 5px 30px" class="font-sm">Palivové dřevo navíc a energie jednoduše doplatíte při odjezdu dle spotřeby.</p>



			<a class="button" onclick="cal.sendToCheckout()">Rezervovat</a>
			
			<div class="messages nodisplay"></div>

		</div>

		<?php
		else:
		$search_action = $VAR["isa"] ? "cal.search()" : "cal.goToSearch()";
		?>
			
		<nv-button class="button button-icon nomargin" onclick="<?=$search_action;?>">Vyhledat<div class="nvicon nvicon-search"></div></nv-button>

		<?php endif; ?>

		<div class="spinner-wrapper hiding" style="border-radius: 50px;">
			<div class="spinner"></div>
		</div>

	</form>

	<script type="text/javascript">
		q( () => {

			var c = {};
			<?php
			if ( $VAR["iss"] ) : //will be loading async
			?>
				
				jax.post( "/wp-admin/admin-ajax.php", {
					"action": "accomodation/init-form",
					"apartment_id": <?= $VAR["apartment_id"]; ?>
				},

				(response) => {
					data = JSON.parse( response );
					//console.log(data);
					c.iss = true;
					c.apartment_id = data.apartment_id;
					c.apartment_name = data.apartment_name;
					c.capacity = data.capacity;
					c.disabled_dates = data.disabled_dates;

					loadDatePicker(c);
				});
			<?php
			else:
			?>
				c.iss = false;
				c.capacity = 1;
				c.disabled_dates = [];
				c.apartment_id = 0;
				c.apartment_name = "";

				loadDatePicker(c);

			<?php
			endif;
			?>

		} );
	</script>

	<?php
	return ob_get_clean();
	}
);
?>