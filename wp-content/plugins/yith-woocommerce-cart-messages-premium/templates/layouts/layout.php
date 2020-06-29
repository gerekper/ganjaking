<?php
/**
 * Cart Message Template
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWCM_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id="woocommerce-cart-notice-name" class="woocommerce-cart-notice woocommerce-cart-notice-minimum-amount woocommerce-info">
	<?php echo $text . ' ' . $button; //phpcs:ignore ?>
</div>
