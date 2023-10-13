<?php
/**
 * Booking form price
 *
 * @var WC_Product_Booking $product
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Templates
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<div class="yith-wcbk-form-section yith-wcbk-form-section-price">
	<label class="yith-wcbk-form-section__label yith-wcbk-booking-form__label"><?php esc_html_e( 'Price', 'yith-booking-for-woocommerce' ); ?></label>
	<div class="yith-wcbk-form-section__content">
		<p class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	</div>
</div>
