<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */


if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<div class="cart-empty-page text-center">
		<?php do_action( 'woocommerce_cart_is_empty' ); ?>
		<i class="cart-empty porto-icon-bag-2"></i>
		<p class="px-3 py-2 cart-empty"><?php esc_html_e( 'No products added to the cart', 'porto' ); ?></p>
		<p class="return-to-shop">
			<a class="button wc-backward btn-v-dark btn-go-shop" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php esc_html_e( 'Return to shop', 'woocommerce' ); ?>
			</a>
		</p>
	</div>
<?php endif; ?>
