<?php
/**
 * Order Failed Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/yith_ctpw_failed.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="yith_ctpw_failed_payment">
	<p><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'yith-custom-thankyou-page-for-woocommerce' ); ?></p>

	<p>
		<?php
		if ( is_user_logged_in() ) {
			esc_html_e( 'Please make a new attempt or go to your account page.', 'yith-custom-thankyou-page-for-woocommerce' );
		} else {
			esc_html_e( 'Please make a new attempt.', 'yith-custom-thankyou-page-for-woocommerce' );
		}
		?>
	</p>

	<p>
		<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'yith-custom-thankyou-page-for-woocommerce' ); ?></a>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'yith-custom-thankyou-page-for-woocommerce' ); ?></a>
		<?php endif; ?>
	</p>
</div>
