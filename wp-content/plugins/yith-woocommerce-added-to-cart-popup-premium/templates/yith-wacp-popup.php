<?php
/**
 * Popup bone template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
}
?>

<div id="yith-wacp-popup" class="<?php echo esc_attr( $animation ); ?>">

	<div class="yith-wacp-overlay"></div>

	<div class="yith-wacp-wrapper woocommerce">

		<div class="yith-wacp-main">

			<div class="yith-wacp-head">
				<a href="#"
					class="yith-wacp-close">X <?php esc_html_e( 'Close', 'yith-woocommerce-added-to-cart-popup' ); ?></a>
			</div>

			<div class="yith-wacp-content"></div>

		</div>

	</div>

</div>
