<?php
/**
 * Popup raq template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit; // Exit if accessed directly.
}

?>

<h3 class="raq-list-title"><?php echo esc_html( apply_filters( 'yith_wacp_raq_popup_title', __( 'Your Quote', 'yith-woocommerce-added-to-cart-popup' ) ) ); ?></h3>

<?php
echo do_shortcode( '[yith_ywraq_request_quote]' );
