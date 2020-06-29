<?php
/**
 * Woocommerce Compare page
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.4
 */

if( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
}

global $yith_woocompare;

/**
 * var $product \WC_Product
 */
?>

<li>
	<a href="<?php echo esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" class="remove" title="<?php esc_html_e( 'Remove', 'yith-woocommerce-compare' ) ?>">x</a>
	<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="product-info">
		<?php echo wp_kses_post( $product->get_image( 'shop_thumbnail' ) ); ?>
		<span><?php echo esc_html( $product->get_title() ); ?></span>
	</a>
</li>