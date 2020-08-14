<?php
/**
 * Add deposit to cart (loop product)
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

global $product;
?>

<div class="yith-wcdp">
	<div class="yith-wcdp-loop-add-to-cart-fields" >
		<a href="<?php echo esc_url( $product_url ); ?>" class="button add-deposit-to-cart-button" ><?php echo esc_html( apply_filters( 'yith_wcdp_pay_deposit_label', __( 'Pay Deposit', 'yith-woocommerce-deposits-and-down-payments' ) ) ); ?></a>
	</div>
</div>
