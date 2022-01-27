<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}

?>
<li class="gt3_widget_product_list">
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a class="gt3_woo_prod_widget_img" href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<?php echo wp_kses_post($product->get_image()); ?>
	</a>

	<div class="gt3_woo_prod_widget_descr">
		<a class="gt3_woo_widget_title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<span class="product-title"><?php echo esc_html($product->get_name()); ?></span>
		</a>

		<?php if ( ! empty( $show_rating ) ) : ?>
			<?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) ); ?>
		<?php endif; ?>

		<p class="price">
			<?php echo wp_kses_post($product->get_price_html()); ?>
		</p>
	</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
