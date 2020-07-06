<?php
/**
 * Checkout fields Newsletter
 *
 * @package WC_Newsletter_Subscription/Templates
 * @version 2.9.0
 * @global  WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wc-newsletter-subscription-checkout-fields">
	<?php
	$fields = $checkout->get_checkout_fields( 'newsletter' );

	if ( ! empty( $fields ) ) {
		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}
	}
	?>
</div>
