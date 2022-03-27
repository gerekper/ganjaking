<?php
/**
 * My account: 'Store credit' section for the dashboard.
 *
 * @package WC_Store_Credit/Templates/My Account
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

$coupons = wc_store_credit_get_customer_coupons( get_current_user_id() );
?>

<?php if ( ! empty( $coupons ) && 'yes' === get_option( 'wc_store_credit_show_my_account', 'yes' ) ) : ?>
	<h3><?php echo esc_html_x( 'Store credit', 'my account: dashboard title', 'woocommerce-store-credit' ); ?></h3>
	<p>
		<?php
		printf(
			/* translators: %s: store-credit endpoint */
			wp_kses_post( _x( 'You have <a href="%s">store credit coupons</a> available to spend on your next purchase.', 'my account: dashboard desc', 'woocommerce-store-credit' ) ),
			esc_url( wc_get_endpoint_url( 'store-credit' ) )
		);
		?>
	</p>
<?php endif; ?>
