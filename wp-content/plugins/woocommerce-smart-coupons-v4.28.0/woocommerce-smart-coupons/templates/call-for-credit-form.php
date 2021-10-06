<?php
/**
 * Call For Credit Form
 *
 * @author      StoreApps
 * @package     WooCommerce Smart Coupons/Templates
 *
 * @version     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<br /><br />
<div id="call_for_credit" class="wc-sc-call-for-credit-container <?php echo esc_attr( $custom_classes['container'] ); ?>">
	<div class="wc-sc-row <?php echo esc_attr( $custom_classes['row'] ); ?>">
		<div class="wc-sc-label <?php echo esc_attr( $custom_classes['label'] ); ?>">
			<label for="credit_called">
				<?php
				if ( ! empty( $currency_symbol ) ) {
					echo wp_kses_post( stripslashes( $smart_coupon_store_gift_page_text ) . ' (' . $currency_symbol . ')' ); // phpcs:ignore
				} else {
					echo wp_kses_post( stripslashes( $smart_coupon_store_gift_page_text ) ); // phpcs:ignore
				}
				?>
			</label>
		</div>
		<div class="wc-sc-input <?php echo esc_attr( $custom_classes['input'] ); ?>">
			<?php echo wp_kses( $input_element, $allowed_html ); // This code block is required in this template. ?>
		</div>
	</div>
	<div class="wc-sc-row <?php echo esc_attr( $custom_classes['row'] ); ?>">
		<p id="error_message" class="wc-sc-error <?php echo esc_attr( $custom_classes['error'] ); ?>" style="color: red;"></p>
	</div>
</div><br />
