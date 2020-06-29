<?php
/**
 * Order tracking form
 *
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
?>

<div class="row">
	<div class="col-lg-6 offset-lg-3">
		<form action="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" method="post" class="woocommerce-form woocommerce-form-track-order track_order featured-box featured-box-primary align-left mt-0">
			<div class="box-content">
				<p><?php esc_html_e( 'To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.', 'porto' ); ?></p>

				<p class="form-row"><label for="orderid"><?php esc_html_e( 'Order ID', 'porto' ); ?></label> <input class="input-text" type="text" name="orderid" id="orderid" value="<?php echo isset( $_REQUEST['orderid'] ) ? esc_attr( wp_unslash( $_REQUEST['orderid'] ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Found in your order confirmation email.', 'porto' ); ?>" /></p><?php // @codingStandardsIgnoreLine ?>
				<p class="form-row"><label for="order_email"><?php esc_html_e( 'Billing email', 'porto' ); ?></label> <input class="input-text" type="text" name="order_email" id="order_email" value="<?php echo isset( $_REQUEST['order_email'] ) ? esc_attr( wp_unslash( $_REQUEST['order_email'] ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Email you used during checkout.', 'porto' ); ?>" /></p><?php // @codingStandardsIgnoreLine ?>
				<div class="clear"></div>

				<p class="form-row clearfix"><button type="submit" class="button btn-lg pt-right" name="track" value="<?php esc_attr_e( 'Track', 'woocommerce' ); ?>"><?php esc_html_e( 'Track', 'woocommerce' ); ?></button></p>
				<?php wp_nonce_field( 'woocommerce-order_tracking', 'woocommerce-order-tracking-nonce' ); ?>
			</div>
		</form>
	</div>
</div>
