<?php
/**
 * Template to display product selection fields in a table (with thumbnail etc.)
 *
 * @package WooCommerce-One-Page-Checkout/Templates
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<table class="shop_table" cellspacing="0">
	<?php foreach( $products as $product ) : ?>

	<tr class="product-item cart <?php if ( wcopc_get_products_prop( $product, 'in_cart' ) ) echo 'selected'; ?>">

		<td class="product-thumbnail">
			<a href="<?php echo wp_get_attachment_url( $product->get_image_id() ); ?>" data-rel="prettyPhoto"  itemprop="image" class="woocommerce-main-image zoom">
				<?php echo $product->get_image(); ?>
			</a>
		</td>

		<td class="product-name">
			<?php echo $product->get_title(); ?>
			<?php if ( $product->is_type( 'variation' ) ) : ?>
				<?php $attribute_string = sprintf( '&nbsp;(%s)', wc_get_formatted_variation( $product->get_variation_attributes(), true ) ); ?>
			<span class="attributes"><?php echo esc_html( apply_filters( 'wcopc_attributes', $attribute_string, $product->get_variation_attributes(), $product ) ); ?></span>
			<?php else : ?>
				<?php $attributes = $product->get_attributes(); ?>
				<?php foreach ( $attributes as $attribute ) : ?>
					<?php $attribute_string = sprintf( '&nbsp;(%s)', $product->get_attribute( $attribute['name'] ) ); ?>
			<span class="attributes"><?php echo esc_html( apply_filters( 'wcopc_attributes', $attribute_string, $attribute, $product ) ); ?></span>
				<?php endforeach; ?>
			<?php endif; ?>
		</td>

		<td class="product-price">
			<span itemprop="price" class="price"><?php echo $product->get_price_html(); ?></span>
		</td>

		<td class="product-quantity">
			<?php wc_get_template( 'checkout/add-to-cart/opc.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
		</td>
	</tr>
	<?php endforeach; // end of the loop. ?>
</table>
