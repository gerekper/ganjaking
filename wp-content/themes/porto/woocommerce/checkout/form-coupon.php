<?php
/**
 * Checkout coupon form
 *
 * @version     3.4.4
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
$checkout_ver      = porto_checkout_version();

if ( !( version_compare($porto_woo_version, '2.5', '<') ? WC()->cart->coupons_enabled() : wc_coupons_enabled() ) ) { // @codingStandardsIgnoreLine.
	return;
}

?>

<?php if ( 'v2' == $checkout_ver ) : ?>
	<div class="cart_totals_toggle mb-3">
		<div class="card card-default">
<?php endif; ?>

		<?php if ( 'v2' == $checkout_ver ) { ?>
			<div class="card-header arrow">
				<h2 class="card-title m-0">
					<a class="accordion-toggle collapsed" data-toggle="collapse" href="#panel-cart-discount" ><?php esc_html_e( 'DISCOUNT CODE', 'porto' ); ?></a>
				</h2>
			</div>
		<?php } else { ?>
			<div class="woocommerce-form-coupon-toggle mb-4">
				<?php echo esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon text-v-dark text-uppercase font-weight-bold">' . esc_html__( 'Enter your code', 'porto' ) . '</a>'; ?>
			</div>
		<?php } ?>

		<?php if ( 'v2' == $checkout_ver ) : ?>
			<div id="panel-cart-discount" class="accordion-body collapse">
				<div class="card-body">
		<?php endif; ?>
			<form class="checkout_coupon" method="post" style="display:none">
				<div class="featured-box align-left">
					<div class="box-content">
						<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woocommerce' ); ?></p>
						<p class="form-row form-row-first">
							<input type="text" name="coupon_code" class="input-text py-0" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
						</p>
						<p class="form-row form-row-last">
							<button type="submit" class="btn button wc-action-btn wc-action-sm" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
						</p>
						<div class="clear"></div>
					</div>
				</div>
			</form>

<?php if ( 'v2' == $checkout_ver ) : ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
