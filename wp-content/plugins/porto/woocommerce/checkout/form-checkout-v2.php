<?php
/**
 * Checkout Form V2
 *
 * @version     3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$porto_woo_version = porto_get_woo_version_number();
$checkout          = WC()->checkout();

// filter hook for include new pages inside the payment method
$get_checkout_url = wc_get_checkout_url();
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

	<div class="row">

		<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="col-lg-4" id="customer_details">

				<?php do_action( 'woocommerce_checkout_billing' ); ?>

				<?php do_action( 'woocommerce_checkout_shipping' ); ?>

			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

		<div class="checkout-order-review align-left col-lg-8">

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<div class="row">
					<div class="col-lg-6">
						<h3><?php esc_html_e( 'Order review', 'porto' ); ?></h3>
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>

	</div>

</form>
