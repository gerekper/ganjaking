<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details mt-5">

	<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

	<?php endif; ?>

	<h3 class="woocommerce-column__title account-sub-title mb-1"><?php esc_html_e( 'Billing Address', 'woocommerce' ); ?></h3>

	<address class="font-size-md">
		<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

		<?php if ( $order->get_billing_phone() ) : ?>
			<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
		<?php endif; ?>

		<?php if ( $order->get_billing_email() ) : ?>
			<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
		<?php endif; ?>
	</address>

	<?php if ( $show_shipping ) : ?>

		</div><!-- /.col-1 -->

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<h3 class="woocommerce-column__title account-sub-title mb-1"><?php esc_html_e( 'Shipping Address', 'woocommerce' ); ?></h3>
			<address class="font-size-md">
				<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
				<?php if ( method_exists( $order, 'get_shipping_phone' ) && $order->get_shipping_phone() ) : ?>
					<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
				<?php endif; ?>
			</address>
		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

	<?php endif; ?>
	<div class="porto-separator"><hr class="separator-line  align_center"></div>
	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
	<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="woocommerce-button button wc-action-btn mt-3 px-4"><i class="vc_btn3-icon fas fa-arrow-left pe-2"></i> <?php esc_html_e( 'Back To List', 'porto' ); ?></a>
</section>
