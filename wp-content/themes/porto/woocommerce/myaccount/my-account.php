<?php
/**
 * My Account page
 *
 * @version     3.5.0
 */
defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();

wc_print_notices();

if ( version_compare( $porto_woo_version, '2.6', '>=' ) ) {

	/**
	 * My Account navigation.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_navigation' );
}
?>

<?php
if ( version_compare( $porto_woo_version, '2.6', '>=' ) ) :
	?>

	<div class="woocommerce-MyAccount-content">
		<div class="align-left">
			<div class="box-content">
				<?php
				/**
				 * My Account content.
				 *
				 * @since 2.6.0
				 */
				do_action( 'woocommerce_account_content' );
				?>
			</div>
		</div>
	</div>

<?php else : ?>

	<p class="myaccount_user alert alert-success m-b-lg">
		<?php
		printf(
			/* translators: 1: user number 2: logout url */
			esc_html__( 'Hello %1$s (not %2$s? %3$sSign out%4$s).', 'porto' ) . ' ',
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
			esc_html( $current_user->display_name ),
			'<a href="' . wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) ) . '">',
			'</a>'
		);

		printf(
			/* translators: %s: edit account url */
			esc_html__( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and %1$sedit your password and account details%2$s.', 'porto' ),
			'<a href="' . wc_customer_edit_account_url() . '">',
			'</a>'
		);
		?>
	</p>

	<?php do_action( 'woocommerce_before_my_account' ); ?>

	<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>

	<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>

	<?php wc_get_template( 'myaccount/my-address.php' ); ?>

	<?php do_action( 'woocommerce_after_my_account' ); ?>

<?php endif; ?>
