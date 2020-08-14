<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH Custom ThankYou Page for Woocommerce
 */

/**
 * PDF Customer Details Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/ctpw_pdf/pdf_template_customer_details.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<h2 class="customer_details">
	<?php
	// APPLY_FILTER ctpw_customer_details_title: change the title for Customer Details table.
	echo wp_kses_post( apply_filters( 'ctpw_customer_details_title', esc_html__( 'Customer details', 'yith-custom-thankyou-page-for-woocommerce' ) ) );
	?>
</h2>
<ul class="woocommerce-customer-details customer_details">
	<?php
	if ( $order->get_billing_email() ) {
		echo '<li><p class="woocommerce-customer-details--email">' . esc_html__( 'Email:', 'yith-custom-thankyou-page-for-woocommerce' ) . '<span> ' . $order->get_billing_email() . '</span></p></li>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	if ( $order->get_billing_phone() ) {
		echo '<li><p class="woocommerce-customer-details--phone">' . esc_html__( 'Telephone:', 'yith-custom-thankyou-page-for-woocommerce' ) . '<span> ' . $order->get_billing_phone() . '</span></p></li>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	// DO_ACTION woocommerce_order_details_after_customer_details: hook after Customer Details after email and telephone: provided $order object.
	do_action( 'woocommerce_order_details_after_customer_details', $order );
	?>
	<div style="clear:both;"></div>
</ul>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="col2-set addresses">

	<div class="col-1">

		<?php endif; ?>

		<header class="billig_address_title">
			<h3 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'yith-custom-thankyou-page-for-woocommerce' ); ?></h3>
		</header>
		<address class="woocommerce-column--billing-address">
			<?php
			if ( ! $order->get_formatted_billing_address() ) {
				esc_html_e( 'N/A', 'yith-custom-thankyou-page-for-woocommerce' );
			} else {
				echo $order->get_formatted_billing_address();//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</address>

		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

	</div><!-- /.col-1 -->

	<div class="col-2">

		<header class="shipping_address_title">
			<h3 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'yith-custom-thankyou-page-for-woocommerce' ); ?></h3>
		</header>
		<address class="woocommerce-column--shipping-address">
			<?php
			if ( ! $order->get_formatted_shipping_address() ) {
				esc_html_e( 'N/A', 'yith-custom-thankyou-page-for-woocommerce' );
			} else {
				echo $order->get_formatted_shipping_address();//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>
