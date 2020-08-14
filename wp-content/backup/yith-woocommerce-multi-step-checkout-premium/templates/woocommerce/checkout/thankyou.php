<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $order ) : ?>

    <?php $order_id = yit_get_prop( $order, 'id' ); ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'yith-woocommerce-multi-step-checkout' ); ?></p>

		<p><?php
			if ( is_user_logged_in() )
				_e( 'Please, either submit your order again or go to your account page.', 'yith-woocommerce-multi-step-checkout' );
			else
				_e( 'Please submit your order again.', 'yith-woocommerce-multi-step-checkout' );
		?></p>

		<p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'yith-woocommerce-multi-step-checkout' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'yith-woocommerce-multi-step-checkout' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>
        <span class="yith-wcms-title">
            <h1 class="endpoint-title"><?php echo WC()->query->get_endpoint_title( 'order-received' ); ?></h1>
            <p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. We have received your order.', 'yith-woocommerce-multi-step-checkout' ), $order ); ?></p>
        </span>

		<ul class="order_details yith-order-info">
			<li class="order">
				<?php _e( 'Order number:', 'yith-woocommerce-multi-step-checkout' ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php _e( 'Date:', 'yith-woocommerce-multi-step-checkout' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( yit_get_prop( $order, 'date_created' ) ) ); ?></strong>
			</li>
			<li class="total">
				<?php _e( 'Total:', 'yith-woocommerce-multi-step-checkout' ); ?>
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $payment_method_title = yit_get_prop( $order, 'payment_method_title' ) ) : ?>
			<li class="method">
				<?php _e( 'Payment method:', 'yith-woocommerce-multi-step-checkout' ); ?>
				<strong><?php echo $payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . yit_get_prop( $order, 'payment_method' ), $order_id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order_id ); ?>

<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. We have received your order.', 'yith-woocommerce-multi-step-checkout' ), null ); ?></p>

<?php endif; ?>
