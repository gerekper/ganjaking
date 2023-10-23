<?php
/**
 * Woocommerce Compare page
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.1.4
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

global $yith_woocompare;

?>

<li>
	<a href="<?php echo esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" class="remove" title="<?php esc_html_e( 'Remove', 'yith-woocommerce-compare' ); ?>">x</a>
	<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="product-info">
		<?php echo wp_kses_post( $product->get_image( 'shop_thumbnail' ) ); ?>
		<span><?php echo esc_html( $product->get_title() ); ?></span>
	</a>
</li>
