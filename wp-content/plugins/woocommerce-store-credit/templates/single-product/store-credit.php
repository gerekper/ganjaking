<?php
/**
 * Single product store credit.
 *
 * @package WC_Store_Credit/Templates
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wc-store-credit-product-container">
	<h3 class="send-to-different-customer">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input id="send-to-different-customer" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox" name="send-to-different-customer" value="1" />
			<span><?php esc_html_e( 'Send credit to someone?', 'woocommerce-store-credit' ); ?></span>
		</label>
	</h3>

	<div class="store-credit-receiver-fields">
		<?php
		$fields = WC_Store_Credit_Product_Addons::get_receiver_fields();

		foreach ( $fields as $key => $field ) :
			woocommerce_form_field( $key, $field );
		endforeach;
		?>
	</div>
</div>
