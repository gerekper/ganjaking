<?php
/**
 * My Account > Top-up in cart notice.
 *
 * @package WC_Account_Funds/Templates/My_Account
 * @version 2.2.0
 */

defined( 'ABSPATH' ) || exit;
?>

<p class="woocommerce-info">
	<a href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" class="button wc-forward"><?php esc_html_e( 'View Cart', 'woocommerce-account-funds' ); ?></a>
	<?php
	/* translators: %s: Top-up product title */
	echo esc_html( sprintf( __( 'You have "%s" in your cart.', 'woocommerce-account-funds' ), $topup_title_in_cart ) );
	?>
</p>
