<?php
/**
 * My Account > Account Funds page.
 *
 * @package WC_Account_Funds/Templates/My_Account
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notices();

?>
<div class="woocommerce-MyAccount-account-funds">
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: 1: funds amount, 2: funds name */
				__( 'You currently have <strong>%1$s</strong> worth of %2$s in your account.', 'woocommerce-account-funds' ),
				WC_Account_Funds::get_account_funds(),
				wc_get_account_funds_name()
			)
		);
		?>
	</p>

	<?php do_action( 'woocommerce_account_funds_content' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks ?>
</div>
