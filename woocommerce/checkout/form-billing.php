<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php /*esc_html_e( 'Billing details', 'woocommerce' ); */?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">

		<?php
		$checkoutfields = $checkout->get_checkout_fields( 'billing' );

		woocommerce_form_field( "billing_first_name", $checkoutfields["billing_first_name"], $checkout->get_value( "billing_first_name" ) );
		woocommerce_form_field( "billing_last_name", $checkoutfields["billing_last_name"], $checkout->get_value( "billing_last_name" ) );
		woocommerce_form_field( "billing_email", $checkoutfields["billing_email"], $checkout->get_value( "billing_email" ) );
		woocommerce_form_field( "billing_phone", $checkoutfields["billing_phone"], $checkout->get_value( "billing_phone" ) );
		unset($checkoutfields["billing_first_name"]);
		unset($checkoutfields["billing_last_name"]);
		unset($checkoutfields["billing_email"]);
		unset($checkoutfields["billing_phone"]);
		?>

	</div>

	<style>
		.billing_fields_toggle_fields {max-height: 0; overflow: hidden; transition: ease 200ms all;}
		#billing_fields_toggle:checked ~ .billing_fields_toggle_fields {max-height: 2000px;}
	</style>

	
	<div class="form-row">
		<input type="checkbox" id="billing_fields_toggle">
		<label for="billing_fields_toggle">Fakturační údaje</label>
		

		<div class="woocommerce-billing-fields__field-wrapper billing_fields_toggle_fields">
			<div class="optional-form-fields"> 
				<?php
					foreach ( $checkoutfields as $key => $field ) {
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					}
				?>
			</div>
		</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
	</div>
</div>



<?php 
//ignorovat

if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
