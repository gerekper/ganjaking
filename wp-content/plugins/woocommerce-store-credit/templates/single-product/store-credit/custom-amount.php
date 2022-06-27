<?php
/**
 * Store Credit Product: Custom amount.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.0.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $fields The fields to display.
 */
?>
<div class="store-credit-custom-amount-fields">
	<?php
	foreach ( $fields as $key => $field ) :
		woocommerce_form_field( $key, $field, WC_Store_Credit_Product_Addons::get_value( $key ) );
	endforeach;
	?>
</div>
