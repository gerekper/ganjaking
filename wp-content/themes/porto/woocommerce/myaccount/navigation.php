<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
$woo         = defined( 'WOOCOMMERCE_VERSION' );
$wishlist    = defined( 'YITH_WCWL' );
?>

<nav class="woocommerce-MyAccount-navigation">
	<h5 class="font-weight-bold text-md text-uppercase pt-1 m-b-sm"><?php esc_html_e( 'My account', 'woocommerce' ); ?></h5>
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
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
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
