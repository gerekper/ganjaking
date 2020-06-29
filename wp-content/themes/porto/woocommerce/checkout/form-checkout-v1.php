<?php
/**
 * Checkout Form V1
 *
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$porto_woo_version = porto_get_woo_version_number();
$checkout          = WC()->checkout();

// filter hook for include new pages inside the payment method
$get_checkout_url = version_compare( $porto_woo_version, '2.5', '<' ) ? apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ) : wc_get_checkout_url(); ?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="row" id="customer_details">
			<div class="col-lg-6">
				<div class="featured-box featured-box-primary align-left">
					<div class="box-content">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="featured-box featured-box-primary align-left">
					<div class="box-content">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<div class="checkout-order-review featured-box featured-box-primary align-left">
		<div class="box-content">
			<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'porto' ); ?></h3>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>
	</div>

</form>
