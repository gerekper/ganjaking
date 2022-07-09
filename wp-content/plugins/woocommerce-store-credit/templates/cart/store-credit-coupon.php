<?php
/**
 * Store credit coupon.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Coupon $coupon Coupon object.
 */
?>
<div class="wc-store-credit-cart-coupon" data-coupon-code="<?php echo esc_attr( $coupon->get_code() ); ?>">
	<div class="wc-store-credit-cart-coupon-inner">
		<div class="coupon-amount"><?php echo wp_kses_post( wc_price( $coupon->get_amount() ) ); ?></div>
		<div class="coupon-code"><?php echo esc_html( $coupon->get_code() ); ?></div>
		<div class="coupon-date-expires">
		<?php
		$expiration_date = $coupon->get_date_expires();

		if ( $expiration_date ) :
			/* translators: %s: coupon date expires */
			echo wp_kses_post( sprintf( __( 'Expires on %s', 'woocommerce-store-credit' ), '<span class="date-expires">' . wc_format_datetime( $expiration_date ) . '</span>' ) );
		else :
			esc_html_e( 'Never expires', 'woocommerce-store-credit' );
		endif;
		?>
		</div>
	</div>
</div>
