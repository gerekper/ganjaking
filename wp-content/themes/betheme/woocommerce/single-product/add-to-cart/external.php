<?php
/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version 	3.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_add_to_cart_form' );

do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<p class="cart">
	<a target="_blank" class="button button_theme button_js" href="<?php echo esc_url( $product_url ); ?>" rel="nofollow">
		<span class="button_icon"><i class="icon-forward"></i></span>
		<span class="button_label"><?php echo esc_html( $button_text ); ?></span>
	</a>
</p>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

<?php do_action( 'woocommerce_after_add_to_cart_form' );
