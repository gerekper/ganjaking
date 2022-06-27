<?php
/**
 * Store Credit Product: Custom receiver.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.0.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $data   The Store Credit product data.
 * @var array $fields The fields to display.
 */
?>
<h3 class="send-to-different-customer">
	<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
		<input id="send-to-different-customer" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" type="checkbox" name="send-to-different-customer" value="1" <?php checked( 'expanded', $data['display_receiver_fields'] ); ?> />
		<span><?php echo wp_kses_post( $data['receiver_fields_title'] ); ?></span>
	</label>
</h3>

<div class="store-credit-receiver-fields">
	<?php
	foreach ( $fields as $key => $field ) :
		woocommerce_form_field( $key, $field, WC_Store_Credit_Product_Addons::get_value( $key ) );
	endforeach;
	?>
</div>
