<?php
/**
 * Booking form shortcode Template
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

global $product, $post;
?>
<div class="woocommerce">
	<div id="product-<?php echo esc_attr( $product->get_id() ); ?>" class="product type-product yith-wcbk-shortcode-booking-form">
		<div class="yith_wcbk_booking_form_shortcode_summary">
			<?php
			/**
			 * DO_ACTION: yith_wcbk_booking_form_shortcode_before_add_to_cart_form
			 * Used to output title, rating and price before the booking add-to-cart form in the booking form shortcode.
			 *
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_price - 10
			 */
			do_action( 'yith_wcbk_booking_form_shortcode_before_add_to_cart_form' );

			/**
			 * DO_ACTION: yith_wcbk_booking_add_to_cart_form
			 * Hook to output the add-to-cart form for the bookable product.
			 *
			 * @hooked \YITH_WCBK_Frontend::print_add_to_cart_template - 10
			 */
			do_action( 'yith_wcbk_booking_add_to_cart_form' );

			/**
			 * DO_ACTION: yith_wcbk_booking_form_shortcode_after_add_to_cart_form
			 * Used to output meta and sharing sections after the booking add-to-cart form in the booking form shortcode.
			 *
			 * @hooked woocommerce_template_single_meta - 40
			 * @hooked woocommerce_template_single_sharing - 50
			 */
			do_action( 'yith_wcbk_booking_form_shortcode_after_add_to_cart_form' );

			?>
		</div><!-- .summary -->
	</div>
</div>
