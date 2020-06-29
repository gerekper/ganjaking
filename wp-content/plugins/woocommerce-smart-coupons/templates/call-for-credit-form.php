<?php
/**
 * Call For Credit Form
 *
 * @author      StoreApps
 * @package     WooCommerce Smart Coupons/Templates
 *
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$custom_classes = array(
	'container' => '',
	'row'       => '',
	'label'     => '',
	'input'     => '',
	'error'     => '',
);

$custom_classes = apply_filters( 'wc_sc_call_for_credit_template_custom_classes', $custom_classes );

?>

<br /><br />
<div id="call_for_credit" class="wc-sc-call-for-credit-container <?php echo esc_attr( $custom_classes['container'] ); ?>">
	<?php
		$currency_symbol = get_woocommerce_currency_symbol();
	?>
	<div class="wc-sc-row <?php echo esc_attr( $custom_classes['row'] ); ?>">
		<div class="wc-sc-label <?php echo esc_attr( $custom_classes['label'] ); ?>">
			<label for="credit_called">
				<?php
				if ( ! empty( $currency_symbol ) ) {
					echo stripslashes( $smart_coupon_store_gift_page_text ) . ' (' . $currency_symbol . ')'; // phpcs:ignore
				} else {
					echo stripslashes( $smart_coupon_store_gift_page_text ); // phpcs:ignore
				}
				?>
			</label>
		</div>
		<div class="wc-sc-input <?php echo esc_attr( $custom_classes['input'] ); ?>">
			<input id="credit_called" step="any" type="number" min="1" name="credit_called" value="" autocomplete="off" autofocus />		<!-- This line is required in this template -->
		</div>
	</div>
	<div class="wc-sc-row <?php echo esc_attr( $custom_classes['row'] ); ?>">
		<p id="error_message" class="wc-sc-error <?php echo esc_attr( $custom_classes['error'] ); ?>" style="color: red;"></p>
	</div>
</div><br />
