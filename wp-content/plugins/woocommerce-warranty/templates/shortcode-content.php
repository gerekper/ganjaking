<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var $order_status string
 * @var $order_has_warranty bool
 * @var $args array
 *
 * @see \Warranty_Shortcodes::render_warranty_request_shortcode for extracted variable definitions
 */
?>
<div id="primary">
	<div id="wcContent" role="main">
		<?php
		if ( ! empty( $updated ) ) {
			echo '<div class="woocommerce-message">' . esc_html( $updated ) . '</div>';
		}

		if ( 'completed' === $order_status && $order_has_warranty ) {
			if ( empty( $idxs ) ) {
				// show products in an order.
				wc_get_template( 'shortcode-order-items.php', $args, 'warranty', WooCommerce_Warranty::$base_path . '/templates/' );
			} else {
				// Request warranty on selected product.
				wc_get_template( 'shortcode-request-form.php', $args, 'warranty', WooCommerce_Warranty::$base_path . '/templates/' );
			}
		} else {
			echo '<div class="woocommerce-error">' . esc_html__( 'There are no valid warranties for this order', 'wc_warranty' ) . '</div>';
			echo '<p><a href="' . esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ) . '" class="button">' . esc_html__( 'Back to My Account', 'wc_warranty' ) . '</a></p>';
		}
		?>
	</div>
</div>
