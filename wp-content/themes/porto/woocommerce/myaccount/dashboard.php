<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
$woo          = defined( 'WOOCOMMERCE_VERSION' );
$wishlist     = defined( 'YITH_WCWL' );
$account_arr  = array(
	'orders'          => array( __( 'Orders', 'woocommerce' ), 'Simple-Line-Icons-social-dropbox' ),
	'downloads'       => array( __( 'Downloads', 'woocommerce' ), 'Simple-Line-Icons-cloud-download' ),
	'edit-address'    => array( __( 'Addresses', 'woocommerce' ), 'Simple-Line-Icons-pointer' ),
	'edit-account'    => array( __( 'Account details', 'woocommerce' ), 'porto-icon-user-2' ),
	'wishlist'        => array( __( 'Wishlist', 'porto' ), 'Simple-Line-Icons-heart' ),
	'customer-logout' => array( __( 'Logout', 'woocommerce' ), 'Simple-Line-Icons-logout' ),
);

$account_arr = apply_filters( 'porto_woocommerce_account_box_items', $account_arr );
?>
<p class="mb-4">
	<?php
	printf(
		/* translators: 1: user display name 2: logout url */
		wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
		'<strong class="account-text-user">' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url() )
	);
	?>
</p>

<p class="m-b-xl">
	<?php
	/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
	$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	if ( wc_shipping_enabled() ) {
		/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	}
	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p>
<div class="box-with-icon">
	<div class="row">
		<?php foreach ( $account_arr as $endpoint => $label ) : ?>
			<div class="col-md-4 col-sm-6 col-12 m-b-md">
				<div class="porto-content-box featured-boxes featured-boxes-style-5 p-t-lg pb-4">
					<div class="featured-box featured-box-effect-4 mb-0">
						<?php
						if ( 'wishlist' == $endpoint ) {
							if ( $wishlist & $woo ) {
								$url = YITH_WCWL()->get_wishlist_url();
							} else {
								$url = get_home_url();
							}
						} else {
							$url = wc_get_account_endpoint_url( $endpoint );
						}
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="text-decoration-none">
							<div class="box-content">
								<i class="icon-featured border-0 bg-transparent <?php echo esc_attr( $label[1] ); ?>"></i>
								<h4 class="font-weight-bold mt-1 mb-0 text-md"><?php echo esc_html( $label[0] ); ?></h4>
							</div>
						</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
